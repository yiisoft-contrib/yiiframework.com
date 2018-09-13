<?php

namespace app\controllers;

use app\models\Extension;
use app\models\News;
use app\models\Wiki;
use DateTime;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use Zend\Feed\Writer\Feed;

class RssController extends BaseController
{
    const LIMIT_NEWS = 20;
    const LIMIT_WIKI = 20;
    const LIMIT_EXTENSIONS = 20;

    public function actionAll()
    {
        $feed = new Feed();
        // Title of the feed
        $feed->setTitle('Live News for Yii Framework');
        // Link to the feed's target, usually a homepage:
        $feed->setLink(Url::toRoute(['site/index'], true));
        // Link to the feed itself, and the feed type:
        $feed->setFeedLink(Url::toRoute(['rss/all'], true), 'rss');

        // Feed description; only required for RSS:
        $feed->setDescription('News, fresh extensions and wiki articles about Yii framework.');

        /** @var News[]|Extension[]|Wiki[] $data */
        $data = array_merge(
            $this->getNews(),
            $this->getExtensions(),
            $this->getWikiPages()
        );

        ArrayHelper::multisort($data, 'created_at', SORT_DESC);

        $latest = new DateTime('@0');
        foreach ($data as $datum) {
            if ($datum instanceof News) {
                $entry = $this->createNewsEntry($feed, $datum);
            } elseif ($datum instanceof Extension) {
                $entry = $this->createExtensionEntry($feed, $datum);
            } elseif ($datum instanceof Wiki) {
                $entry = $this->createWikiEntry($feed, $datum);
            } else {
                throw new Exception('Unable to create entry for datum of type ' . get_class($datum));
            }
            $feed->addEntry($entry);
            $latest = $entry->getDateModified() > $latest ? $entry->getDateModified() : $latest;
        }

        $feed->setDateModified($latest);

        \Yii::$app->response->headers->add('Content-Type', 'application/rss+xml');

        return $feed->export('rss');
    }

    protected function createNewsEntry(Feed $feed, News $news)
    {
        // Create an empty entry:
        $entry = $feed->createEntry();

        // Set the entry title:
        $entry->setTitle('[' . $news->getObjectType() . '] ' . $news->getLinkTitle());

        // Set the link to the entry:
        $entry->setLink(Url::to($news->getUrl(), true));

        // Add an author, if you can. Each author entry should be an
        // array containing minimally a "name" key, and zero or more of
        // the keys "email" or "uri".
        $entry->addAuthor(['name' => $news->creator->username]);

        // Set the date created:
        $entry->setDateCreated(new DateTime($news->created_at));

        // And the date last updated:
        $modified = new DateTime($news->updated_at);
        $entry->setDateModified($modified);

        // And finally, some content:
        $entry->setContent($news->contentHtml);

        return $entry;
    }

    protected function getNews()
    {
        return News::find()
            ->with('creator')
            ->latest()
            ->published()
            ->limit(self::LIMIT_NEWS)
            ->all();
    }

    protected function createExtensionEntry(Feed $feed, Extension $extension)
    {
        // Create an empty entry:
        $entry = $feed->createEntry();

        // Set the entry title:
        $entry->setTitle('[' . $extension->getObjectType() . '] ' . $extension->getLinkTitle());

        // Set the link to the entry:
        $entry->setLink(Url::to($extension->getUrl(), true));

        // Add an author, if you can. Each author entry should be an
        // array containing minimally a "name" key, and zero or more of
        // the keys "email" or "uri".
        $entry->addAuthor(['name' => $extension->owner->username]);

        // Set the date created:
        $entry->setDateCreated(new DateTime($extension->created_at));

        // And the date last updated:
        $modified = new DateTime($extension->updated_at);
        $entry->setDateModified($modified);

        // And finally, some content:
        $entry->setContent($extension->contentHtml);

        return $entry;
    }

    protected function getExtensions()
    {
        return Extension::find()
            ->with('owner')
            ->latest()
            ->limit(self::LIMIT_EXTENSIONS)
            ->all();
    }

    protected function createWikiEntry(Feed $feed, Wiki $wikiPage)
    {
        // Create an empty entry:
        $entry = $feed->createEntry();

        // Set the entry title:
        $entry->setTitle('[' . $wikiPage->getObjectType() . '] ' . $wikiPage->getLinkTitle());

        // Set the link to the entry:
        $entry->setLink(Url::to($wikiPage->getUrl(), true));

        // Add an author, if you can. Each author entry should be an
        // array containing minimally a "name" key, and zero or more of
        // the keys "email" or "uri".
        $entry->addAuthor(['name' => $wikiPage->creator->username]);

        // Set the date created:
        $entry->setDateCreated(new DateTime($wikiPage->created_at));

        // And the date last updated:
        $modified = new DateTime($wikiPage->updated_at);
        $entry->setDateModified($modified);

        // And finally, some content:
        $entry->setContent($wikiPage->contentHtml);

        return $entry;
    }

    protected function getWikiPages()
    {
        return Wiki::find()
            ->with('creator')
            ->latest()
            ->limit(self::LIMIT_WIKI)
            ->all();
    }
}
