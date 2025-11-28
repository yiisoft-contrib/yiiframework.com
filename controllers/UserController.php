<?php

namespace app\controllers;

use app\components\mailers\EmailVerificationMailer;
use app\models\Badge;
use app\models\ChangeEmailForm;
use app\models\ChangePasswordForm;
use app\models\Extension;
use app\models\Star;
use app\models\UserAvatarUploadForm;
use app\models\UserBadge;
use app\models\Wiki;
use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
{
    public $sectionTitle = 'Yii Framework Community';
    public $headTitle = 'Community';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['profile', 'request-email-verification', 'change-password', 'upload-avatar', 'delete-avatar', 'change-email'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['profile', 'request-email-verification', 'change-password', 'upload-avatar', 'delete-avatar', 'change-email'],
                        'roles' => ['@'],
                    ],
                ]
            ],
            'verb' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'upload-avatar' => ['POST'],
                    'delete-avatar' => ['POST'],
                ],
            ]
        ];
    }

    /**
     * Lists all User models.
     */
    public function actionIndex()
    {
        // temporarily
        return $this->redirect(['site/index']);

        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->active(),
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => ['rank' => SORT_ASC],
                'attributes' => [
                    'rank'=> [
                        'asc'=>['rank' => SORT_ASC],
                        'desc'=>['rank' => SORT_DESC],
                        'label'=>'Rank',
                    ],
                    'display_name'=> [
                        'asc'=>['display_name' => SORT_ASC],
                        'desc'=>['display_name' => SORT_DESC],
                        'label'=>'User',
                    ],
                    'joined'=> [
                        'asc'=>['created_at' => SORT_ASC],
                        'desc'=>['created_at' => SORT_DESC],
                        'label'=>'Member Since',
                        'default'=>SORT_DESC,
                    ],
                    'rating'=> [
                        'asc'=>['rating' => SORT_ASC],
                        'desc'=>['rating' => SORT_DESC],
                        'label'=>'Overall Rating',
                        'default'=>SORT_DESC,
                    ],
                    'extensions'=> [
                        'asc'=>['extension_count' => SORT_ASC],
                        'desc'=>['extension_count' => SORT_DESC],
                        'label'=>'Extensions',
                        'default'=>SORT_DESC,
                    ],
                    'wiki'=> [
                        'asc'=>['wiki_count' => SORT_ASC],
                        'desc'=>['wiki_count' => SORT_DESC],
                        'label'=>'Wiki Articles',
                        'default'=>SORT_DESC,
                    ],
                    'comments'=> [
                        'asc'=>['comment_count' => SORT_ASC],
                        'desc'=>['comment_count' => SORT_DESC],
                        'label'=>'Comments',
                        'default'=>SORT_DESC,
                    ],
                    'posts'=> [
                        'asc'=>['post_count' => SORT_ASC],
                        'desc'=>['post_count' => SORT_DESC],
                        'label'=>'Forum Posts',
                        'default'=>SORT_DESC,
                    ],
                ],
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'userCount' => User::find()->active()->count(),
            'wikis' => $model->getWikis()->orderBy('title')->active()->all() ,
            'extensions' => $model->getExtensions()->excludeOfficial()->orderBy('name')->active()->all(),
        ]);
    }

    public function actionProfile()
    {
        $this->sectionTitle = null;

        $userId = Yii::$app->user->getId();

        /** @var User $user */
        $user = Yii::$app->user->identity;

        $extensions = Extension::find()
            ->active()
            ->excludeOfficial()
            ->andWhere(['owner_id' => $userId])
            ->orderBy('name')
            ->all();

        $wikiPages = Wiki::find()
            ->active()
            ->andWhere(['creator_id' => $userId])
            ->orderBy('title')
            ->all();

        return $this->render('profile', [
            'model' => $user,
            'extensions' => $extensions,
            'wikiPages' => $wikiPages,
            'starTargets' => Star::getTargets($user->id)
        ]);
    }

    public function actionHalloffame()
   	{
        return $this->render('halloffame');
   	}

    public function actionBadges()
    {
        $badges = Badge::find()->active()->orderBy('achieved DESC, urlname')->all();

        $forumBadges = Yii::$app->forumAdapter->getForumBadges();
        ArrayHelper::multisort($forumBadges, 'grant_count', SORT_DESC);

        return $this->render('badges', [
            'badges' => $badges,
            'forumBadges' => $forumBadges,
            'counts' => UserBadge::countUsers(),
        ]);
    }

    public function actionViewBadge($name)
    {
        /** @var Badge $badge */
        $badge = Badge::find()->active()->andWhere(['urlname' => $name])->one();
        if ($badge === null) {
            throw new NotFoundHttpException('Unknown badge');
        }
        return $this->render('badge', [
            'badge' => $badge,
            'users' => UserBadge::listUsers($badge),
            'count' => UserBadge::countUsers($badge),
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::find()->active()->andWhere(['id' => $id])->one()) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionRequestEmailVerification()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        if ($user->email_verified) {
            Yii::$app->getSession()->setFlash('success', 'Your email is already verified.');
        } else {
            (new EmailVerificationMailer($user, EmailVerificationMailer::TYPE_PROFILE))->send();
            Yii::$app->getSession()->setFlash('success', 'Please check your email to verify it.');
        }

        return Yii::$app->user->isGuest ? $this->goHome() : $this->redirect(['user/profile']);
    }

    public function actionChangePassword()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        $this->sectionTitle = null;

        $changePasswordForm = new ChangePasswordForm($user);
        if ($changePasswordForm->load(Yii::$app->request->post()) && $changePasswordForm->save()) {
            Yii::$app->getUser()->switchIdentity($changePasswordForm->getUser(), $this->getRememberMeDuration());
            Yii::$app->getSession()->setFlash('success', 'The password has been changed.');
            return $this->redirect(['/user/profile']);
        }

        return $this->render('changePassword', [
            'changePasswordForm' => $changePasswordForm,
        ]);
    }

    public function actionChangeEmail()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        $this->sectionTitle = null;

        $changeEmailForm = new ChangeEmailForm($user);
        if ($changeEmailForm->load(Yii::$app->request->post()) && $changeEmailForm->save()) {
            Yii::$app->getUser()->switchIdentity($changeEmailForm->getUser(), $this->getRememberMeDuration());
            Yii::$app->getSession()->setFlash('success', 'The email has been changed.');
            return $this->redirect(['/user/profile']);
        }

        return $this->render('changeEmail', [
            'changeEmailForm' => $changeEmailForm,
        ]);
    }

    protected function getRememberMeDuration(): int
    {
        $name = Yii::$app->getUser()->identityCookie['name'];

        return Yii::$app->getRequest()->getCookies()->has($name)
            ? (int) Yii::$app->params['user.rememberMeDuration']
            : 0;
    }

    /**
     * Download avatar file (inline)
     * @param int $id user id
     * @throws NotFoundHttpException
     */
    public function actionAvatar($id)
    {
        $user = $this->findModel($id);
        if ($user->hasAvatar()) {
            return $this->sendFile($user->getAvatarPath());
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Upload new user avatar file
     */
    public function actionUploadAvatar()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        $form = new UserAvatarUploadForm([
            'user' => $user,
        ]);
        $form->load(Yii::$app->request->post());
        $form->avatar = UploadedFile::getInstance($form, 'avatar');

        $success = $form->upload();

        if (Yii::$app->request->isAjax) {
            // reply with JSON
            if ($success) {
                // success
                return Json::encode([
                    'url' => $user->getAvatarUrl(),
                ]);
            }

            // error
            Yii::$app->response->setStatusCode(422); // Unprocessable entity
            return Json::encode([
                'error' => $form->getFirstError('avatar'),
            ]);
        }

        if (!$success) {
            Yii::$app->session->setFlash('error', 'Failed to upload file: ' . $form->getFirstError('avatar'));
        }
        return $this->redirect(['/user/profile']);
    }

    public function actionDeleteAvatar()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $user->deleteAvatar();
        return $this->redirect(['/user/profile']);
    }
}
