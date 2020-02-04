<?php

namespace app\controllers;

use app\components\RowHelper;
use app\models\Extension;
use app\models\News;
use app\models\SecurityForm;
use app\models\User;
use app\models\Wiki;
use Yii;
use app\models\ContactForm;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * SiteController serves the mostly static sites of the website.
 */
class SiteController extends BaseController
{
	/**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['render-markdown'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }


    /**
     * This action redirects old urls to the new location.
     */
    public function actionRedirect($url)
    {
        $urlMap = [
            // documentation terms are now on the license page
            'doc/terms' => ['site/license', '#' => 'docs'],
            // wiki has been under doc/cookbook long time ago on the old site
            'doc/cookbook' => ['wiki/index'],
            // about is now handled in the guide
            'about' => ['guide/view', 'type' => 'guide', 'version' => reset(Yii::$app->params['versions']['api']), 'language' => 'en', 'section' => 'intro-yii'],
            // there is no dedicated performance page, redirect to home page
            'performance' => ['site/index'],
            // send requests to /doc directly to the guide
            'doc' => ['guide/entry'],
            // features page
            'features' => ['site/index'],
            // tutorials page
            'tutorials' => ['site/resources'],
            // screencasts page
            'screencasts' => ['site/resources'],
        ];
        if (isset($urlMap[$url])) {
            return $this->redirect($urlMap[$url], 301); // Moved Permanently
        }

        if (preg_match('~^demos/.+$~', $url)) {
            // old demo pages
            return $this->redirect(['site/demos'], 301); // Moved Permanently
        }

        if (preg_match('~^doc/cookbook/(\d+)$~', $url, $matches)) {
            // old wiki URLs
            return $this->redirect(['wiki/view', 'id' => $matches[1]], 301); // Moved Permanently
        }

        throw new NotFoundHttpException('The requested page was not found.');
    }

    /**
     * This action redirects old urls to the new location.
     *
     * There are the following types of URLs:
     *
     * - link to a forum/category:
     *   https://www.yiiframework.com/forum/index.php/forum/7-framework-news/
     *   https://www.yiiframework.com/forum/index.php/forum/7-framework-news/page__prune_day__100__sort_by__Z-A__sort_key__last_post__topicfilter__all__st__30
     * - link to a topic:
     *   Topic 1st page: https://www.yiiframework.com/forum/index.php/topic/3253-forum-system-updated/
     *   Topic 2nd page: https://www.yiiframework.com/forum/index.php/topic/3253-forum-system-updated/page__st__20
     *   Link to a post: https://www.yiiframework.com/forum/index.php/topic/3253-forum-system-updated/page__view__findpost__p__24772
     * - link to members:
     *   https://www.yiiframework.com/forum/index.php/user/members/
     *   https://www.yiiframework.com/forum/index.php/user/5951-cebe/
     * - forum rss feed:
     *   https://www.yiiframework.com/forum/index.php/rss/forums/1-yii-framework-forum/
     *   Redirect to topic rss
     *
     * Some URLs seem to be of the following form:
     *
     * https://www.yiiframework.com/forum/index.php?/user/5951-cebe/
     * https://www.yiiframework.com/forum/index.php?/topic/7366-incorrect-links-to-forum-from-yii-docs/
     *
     *
     */
    public function actionRedirectForum($url = null)
    {
        // url must end with /
        $forumUrl = 'https://forum.yiiframework.com/';

        if ($url === 'index.php' && count($_GET) > 1) {
            // https://www.yiiframework.com/forum/index.php?showuser=5951
            if (isset($_GET['showuser'])) {
                $user = User::find()->active()->where(['forum_id' => (int) $_GET['showuser']])->one();
                if ($user !== null) {
                    $url = "user/{$user->forum_id}-{$user->username}";
                }
            } else {
                // find URLs like /forum/index.php?/user/5951-cebe/
                foreach($_GET as $key => $value) {
                    if ($key !== 'url' && empty($value)) {
                        $url = ltrim($key, '/');
                    }
                }
            }
        }

        if (empty($url) || $url === 'index.php' || $url === 'index.php/') {
            return $this->redirect($forumUrl);
        }

        // prevent injection of external URLs
        if (!Url::isRelative($url)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }
        // strip additional information like page, sorting, direct post link
        // the plain forum and topic urls are stored in discourse as permalinks
        if (preg_match('~^(topic|forum)/(.*?)/~', $url, $matches)) {
            $url = "{$matches[1]}/{$matches[2]}";
        }
        if (preg_match('~^user/(\d+)-([^/]+)~', $url, $matches)) {
            $url = "u/{$matches[2]}";
        }
        if (trim($url, '/') === 'members') {
            $url = 'u?order=post_count&period=all';
        }
        if (preg_match('~^uploads/(.*)~', $url, $matches)) {
            $url = "ipb_uploads/{$matches[1]}";
        }
        return $this->redirect($forumUrl . ltrim($url, '/'));
    }

    public function actionIndex()
    {
    	$yii2books = Yii::$app->params['books2'];
    	$random = array_rand($yii2books, 2);

        $books = [$yii2books[$random[0]], $yii2books[$random[1]]];
        $news = News::find()->latest()->published()->limit(4)->all();
        $extensions = Extension::find()->latest()->limit(8)->all();
        $tutorials = Wiki::find()->latest()->limit(10)->all();

        return $this->render('index', [
            'testimonials' => Yii::$app->params['testimonials'],
            'books' => $books,
            'news' => $news,
            'extensions' => $extensions,
            'tutorials' => $tutorials,
        ]);
    }

    public function actionContact()
    {
        $this->sectionTitle = 'Contact Us';
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionBooks()
    {
        return $this->render('books', ['books2' => Yii::$app->params['books2'], 'books1' => Yii::$app->params['books1']]);
    }

    public function actionContribute()
    {
        return $this->render('contribute');
    }

    public function actionChat()
    {
        $this->sectionTitle = 'Yii Framework Community';
        $this->headTitle = 'Live Chat';
        return $this->render('chat');
    }

    public function actionLicense()
    {
        return $this->render('license');
    }

    public function actionTeam()
    {
        $members = Yii::$app->params['members'];

        $activeMembers = [];
        $pastMembers = [];
        $inactiveMembers = [];

        foreach ($members as $member) {
            switch ($member['status']) {
                case \TeamStatus::TEAM_STATUS_ACTIVE:
                    $activeMembers[] = $member;
                    break;
                case \TeamStatus::TEAM_STATUS_PAST:
                    $pastMembers[] = $member;
                    break;
                case \TeamStatus::TEAM_STATUS_INACTIVE:
                    $inactiveMembers[] = $member;
                    break;
            }
        }

        $activeMembers = RowHelper::split($activeMembers, 6);
        $inactiveMembers = RowHelper::split($inactiveMembers, 6);
        $pastMembers = RowHelper::split($pastMembers, 6);

        try {
            $data_dir = Yii::getAlias('@app/data');
            $contributors = json_decode(file_get_contents($data_dir . '/contributors.json'), true);
        } catch(\Exception $e) {
            $contributors = false;
        }

        return $this->render('team', [
            'inactiveMembers' => $inactiveMembers,
            'activeMembers' => $activeMembers,
            'pastMembers' => $pastMembers,
            'contributors' => $contributors,
        ]);
    }

    public function actionReportIssue()
    {
        return $this->render('report-issue');
    }

    public function actionSecurity()
    {
        $model = new SecurityForm();
        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            Yii::$app->session->setFlash('securityFormSubmitted');

            return $this->refresh();
        }

        return $this->render('security', [
            'model' => $model,
        ]);
    }

    public function actionDownload()
    {
	    $versions = Yii::$app->params['versions']['minor-versions'];
	    $versionInfo = Yii::$app->params['versions']['version-info'];
        return $this->render('download', [
	        'versions' => $versions,
	        'versionInfo' => $versionInfo,
        ]);
    }

    public function actionTos()
    {
        $this->sectionTitle = 'Terms of Service';
        return $this->render('tos');
    }

    public function actionLogo()
    {
        $this->sectionTitle = 'Official Logos';
        return $this->render('logo');
    }

    public function actionTour()
    {
        return $this->redirect(['site/index']);
    }

    public function actionResources()
    {
        return $this->render('resources');
    }

    public function actionDemos()
    {
        $this->sectionTitle = 'Demos';
        Yii::$app->response->statusCode = 410; // Gone (no longer available)
        return $this->render('demos');
    }

    /**
     * used to download specific files
     */
    public function actionFile($category, $file)
    {
        if (!preg_match('~^[\w-.]+$~', $file)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }

        switch($category)
        {
            case 'docs-offline':
                $filePath = Yii::getAlias("@app/data/docs-offline/$file");
                if (file_exists($filePath)) {
                    return Yii::$app->response->sendFile($filePath, $file);
                }
                break;
        }
        throw new NotFoundHttpException('The requested page was not found.');
    }

    public function actionCommunity()
    {
        $this->sectionTitle = 'Community Resources';
        return $this->render('community');
    }

    public function actionReleaseCycle()
    {
        $this->sectionTitle = 'Release Cycle';
        return $this->render('release-cycle', [
            'versions' => Yii::$app->params['release-cycle'],
        ]);
    }

    public function actionRenderMarkdown()
    {
        $markdown = Yii::$app->request->post('content');
        return Yii::$app->formatter->asGuideMarkdown($markdown);
    }
}
