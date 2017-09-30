<?php

namespace app\controllers;

use app\components\RowHelper;
use app\models\Extension;
use app\models\News;
use app\models\SecurityForm;
use app\models\Wiki;
use Yii;
use app\models\ContactForm;
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
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
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
            // there is no demo page anymore, redirect to home  page
            'demos' => ['site/index'],
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

        if (preg_match('%doc/cookbook/(\d+)%', $url, $matches)) {
            // old wiki URLs
            return $this->redirect(['wiki/view', 'id' => $matches[1]], 301); // Moved Permanently
        }

        throw new NotFoundHttpException('The requested page was not found.');
    }

    public function actionIndex()
    {
        $books = array_slice(Yii::$app->params['books2'], 0, 5);
        $news = News::find()->latest()->published()->limit(4)->all();
        $extensions = Extension::find()->latest()->limit(6)->all();
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

        $contributors = false;
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

    /**
     * used to download specific files
     */
    public function actionFile($category, $file)
    {
        if (!preg_match('~^[\w\d-.]+$~', $file)) {
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
}
