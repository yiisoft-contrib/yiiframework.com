<?php

namespace app\commands;

use app\apidoc\ApiRenderer;
use app\models\Guide;
use app\models\News;
use Yii;
use yii\console\Controller;
use samdark\sitemap\Sitemap;
use samdark\sitemap\Index;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Sitemap generate
 *
 * @package app\commands
 */
class SitemapController extends Controller
{
    /**
     * Generate sitemap files
     */
    public function actionGenerate()
    {
        $tmpPath = Yii::getAlias('@runtime/sitemapGenerate');
        FileHelper::removeDirectory($tmpPath);
        FileHelper::createDirectory($tmpPath . '/sitemap');

        $sitemap = new Sitemap($tmpPath . '/sitemap/item.xml');
        $sitemap->setMaxUrls(Yii::$app->params['sitemap.indexMaxUrls']);

        $this->addUrls($sitemap);
        $sitemap->write();

        $sitemapIndex = new Index($tmpPath . '/sitemap.xml');
        $sitemapFiles = $sitemap->getSitemapUrls(Url::to('/sitemap/', true));
        foreach ($sitemapFiles as $sitemapFile) {
            $sitemapIndex->addSitemap($sitemapFile);
        }
        $sitemapIndex->write();

        //Copy prepared sitemap files to @webroot
        $sitemapFilePath = Yii::getAlias('@webroot/sitemap.xml');
        $sitemapIndexPath = Yii::getAlias('@webroot/sitemap');

        if (file_exists($sitemapFilePath)) {
            unlink($sitemapFilePath);
        }
        FileHelper::removeDirectory($sitemapIndexPath);

        copy($tmpPath . '/sitemap.xml', $sitemapFilePath);
        FileHelper::copyDirectory($tmpPath . '/sitemap', $sitemapIndexPath);

        FileHelper::removeDirectory($tmpPath);
    }

    /**
     * Adding url in sitemap
     *
     * @param Sitemap $sitemap
     */
    private function addUrls(Sitemap $sitemap)
    {
        $sitemap->addItem(Url::to('/', true), null, Sitemap::ALWAYS, 1);

        $baseUrls = [
            'site/books', 'site/contribute', 'site/chat', 'site/contact', 'site/license', 'site/team', 'wiki/index',
            'site/report-issue', 'site/security', 'site/download', 'site/tos', 'site/logo', 'site/tour', 'site/resources',
            'extension/index', 'user/index', 'user/badges'
        ];
        foreach ($baseUrls as $baseUrl) {
            $sitemap->addItem(Url::toRoute($baseUrl, true), null, Sitemap::DAILY, 0.2);
        }

        foreach ($this->getDocUrls() as $docsUrl) {
            $sitemap->addItem(Url::to($docsUrl, true), null, Sitemap::DAILY, 0.3);
        }

        //news
        $sitemap->addItem(Url::toRoute(['news/index'], true), null, Sitemap::HOURLY, 0.3);
        /** @var News $news */
        foreach (News::find()->latest()->asArray()->each(100) as $news) {
            $url = Url::to(['news/view', 'id' => $news['id'], 'name' => $news['slug']], true);
            $sitemap->addItem($url, $news['updated_at'], null, 0.3);
        }
    }

    /**
     * Get urls for:
     *  - api
     *  - guide
     *  - blog
     *
     * @return string[]
     */
    private function getDocUrls()
    {
        $urls = [];

        //api
        $apiBaseUrl = Yii::$app->params['api.baseUrl'];

        $urls[] = "{$apiBaseUrl}/2.0";

        $apiRenderer = new ApiRenderer([
            'version' => '2.0'
        ]);
        $classes = Json::decode(file_get_contents(Yii::getAlias("@app/data/api-2.0/json/typeNames.json")));
        foreach($classes as $class) {
            $urls[] = $apiRenderer->generateApiUrl($class['name']);
        }

        foreach (['1.0', '1.1'] as $version) {
            $urls[] = "{$apiBaseUrl}/{$version}";

            $classes = Json::decode(file_get_contents(Yii::getAlias("@app/data/api-{$version}/json/typeNames.json")));
            foreach($classes as $class) {
                $urls[] = "{$apiBaseUrl}/{$version}/{$class['name']}";
            }
        }

        //guide
        foreach (['guide', 'blogtut'] as $guideType) {
            $guideBaseUrl = Yii::$app->params["{$guideType}.baseUrl"];
            foreach (Yii::$app->params['guide.versions'] as $version => $languages) {
                foreach (array_keys($languages) as $language) {
                    $urls[] =  "{$guideBaseUrl}/{$version}/{$language}";
                    $guide = Guide::load($version, $language, $guideType);
                    if ($guide) {
                        foreach (array_keys($guide->sections) as $sectionName) {
                            $urls[] =  "{$guideBaseUrl}/{$version}/{$language}/{$sectionName}";
                        }
                    }
                }
            }
        }

        return $urls;
    }
}
