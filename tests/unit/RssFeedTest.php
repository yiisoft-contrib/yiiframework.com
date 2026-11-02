<?php

use Laminas\Feed\Writer\Feed;

/**
 * Regression test for the zend-feed -> laminas-feed 2.x migration used by
 * app\controllers\RssController. We do not boot the controller (it needs Yii
 * app + DB); we exercise the same Feed / Entry public API the actionAll()
 * path uses to build and export an RSS 2.0 document.
 */
class RssFeedTest extends \Codeception\Test\Unit
{
    public function testBuildsRssWithEntriesLikeRssController(): void
    {
        $feed = $this->buildSampleFeed();
        $xml = $feed->export('rss');

        self::assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $xml);
        self::assertStringContainsString('<rss version="2.0"', $xml);
        self::assertStringContainsString('<channel>', $xml);

        // Feed-level fields, exactly as RssController::actionAll() sets them.
        self::assertStringContainsString('<title>Live News for Yii Framework</title>', $xml);
        self::assertStringContainsString('<link>https://example.test/</link>', $xml);
        self::assertStringContainsString('<description>News, fresh extensions and wiki articles about Yii framework.</description>', $xml);

        // Each entry renders as an <item>, carrying title / link / author / dates / content.
        self::assertSame(2, substr_count($xml, '<item>'), 'expect one <item> per entry');
        self::assertStringContainsString('<title>[ext] Foo</title>', $xml);
        self::assertStringContainsString('<link>https://example.test/extension/foo</link>', $xml);
        self::assertStringContainsString('<dc:creator>alice</dc:creator>', $xml);
        self::assertStringContainsString('<content:encoded><![CDATA[Foo content]]></content:encoded>', $xml);
        self::assertStringContainsString('<dc:creator>bob</dc:creator>', $xml);
    }

    private function buildSampleFeed(): Feed
    {
        // Mirrors RssController::actionAll().
        $feed = new Feed();
        $feed->setTitle('Live News for Yii Framework');
        $feed->setLink('https://example.test/');
        $feed->setFeedLink('https://example.test/rss/all', 'rss');
        $feed->setDescription('News, fresh extensions and wiki articles about Yii framework.');

        // Mirrors RssController::createExtensionEntry().
        $entry = $feed->createEntry();
        $entry->setTitle('[ext] Foo');
        $entry->setLink('https://example.test/extension/foo');
        $entry->addAuthor(['name' => 'alice']);
        $entry->setDateCreated(new DateTime('2026-01-01T10:00:00Z'));
        $entry->setDateModified(new DateTime('2026-01-02T10:00:00Z'));
        $entry->setContent('Foo content');
        $feed->addEntry($entry);

        // Mirrors RssController::createNewsEntry() for a second item.
        $newsEntry = $feed->createEntry();
        $newsEntry->setTitle('[news] Bar');
        $newsEntry->setLink('https://example.test/news/bar');
        $newsEntry->addAuthor(['name' => 'bob']);
        $newsEntry->setDateCreated(new DateTime('2026-01-03T10:00:00Z'));
        $newsEntry->setDateModified(new DateTime('2026-01-04T10:00:00Z'));
        $newsEntry->setContent('Bar content');
        $feed->addEntry($newsEntry);

        // RssController then sets the latest entry date as the feed's date modified.
        $feed->setDateModified(new DateTime('2026-01-04T10:00:00Z'));

        return $feed;
    }
}
