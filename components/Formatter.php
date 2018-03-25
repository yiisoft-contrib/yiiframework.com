<?php

namespace app\components;

use Yii;
use yii\apidoc\helpers\ApiMarkdown;
use yii\apidoc\models\Context;
use yii\helpers\HtmlPurifier;
use yii\helpers\Markdown;

class Formatter extends \yii\i18n\Formatter
{
   	public $dateFormat = 'medium';
   	public $timeFormat = 'medium';
   	public $datetimeFormat = 'medium';

    public $purifierConfig = [
        'HTML' => [
            'AllowedElements' => [
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'strong', 'em', 'b', 'i', 'u', 's', 'span',
                'pre', 'code',
                'table', 'tr', 'td', 'th',
                'a', 'p', 'br',
                'blockquote',
                'ul', 'ol', 'li',
                'img'
            ],
        ],
        'Attr' => [
            'EnableID' => true,
        ],
    ];

    /**
     * Format as normal markdown without class link extensions.
     *
     * @param $markdown
     * @return string
     */
    public function asMarkdown($markdown)
   	{
        Markdown::$flavors['yii-gfm'] = [
            'class' => \app\components\Markdown::class,
            'html5' => true,
        ];
        $html = Markdown::process($markdown, 'yii-gfm');

        $html = $this->replaceHeadlines($html);

        $output = HtmlPurifier::process($html, $this->purifierConfig);

   		return '<div class="markdown">'.$output.'</div>';
   	}

    /**
     * Format as comment markdown without class link extensions but preserves newlines.
     *
     * @param $markdown
     * @return string
     */
    public function asCommentMarkdown($markdown)
   	{
        Markdown::$flavors['yii-gfm-comment'] = [
            'class' => \app\components\Markdown::class,
            'html5' => true,
            'enableNewlines' => true,
        ];
        $html = Markdown::process($markdown, 'yii-gfm-comment');

        $html = $this->replaceCommentHeadlines($html);

        $output = HtmlPurifier::process($html, $this->purifierConfig);

   		return '<div class="markdown">'.$output.'</div>';
   	}

    /**
     * Format as guide markdown including class links and other special features.
     *
     * Do NOT use this to render Guide markdown files!
     * It will manipulate headline tags!
     * This is only used for other parts of the site that use similar markdown
     * features, e.g. Wiki and Extensions.
     *
     * @param $markdown
     * @return string
     */
   	public function asGuideMarkdown($markdown)
   	{
        if (ApiMarkdown::$renderer === null) {
            ApiMarkdown::$renderer = new \app\apidoc\GuideRenderer();
        }
        if (ApiMarkdown::$renderer->apiContext === null) {
            $cacheFile = Yii::getAlias('@app/data/api-2.0/cache/apidoc.data');
            if (file_exists($cacheFile)) {
                $context = unserialize(file_get_contents($cacheFile));
            } else {
                $context = new Context();
            }
            ApiMarkdown::$renderer->apiContext = $context;
        }

        $html = ApiMarkdown::process($markdown);

        $html = $this->replaceHeadlines($html);
        $html = $this->replaceImageUrlForProxy($html);

        $output = HtmlPurifier::process($html, $this->purifierConfig);

   		return '<div class="markdown">'.$output.'</div>';
   	}

    /**
     * Replace headlines in markdown to avoid users using H1 and H2 tags.
     * @param string $html
     * @return string
     */
    private function replaceHeadlines($html)
    {
        // replace level of headline tags, h1 -> h3, ...
        return preg_replace_callback('~(</?h)(\d)( |>)~i', function($matches) {
            $level = $matches[2] + 2;
            if ($level > 6) {
                $level = 6;
            }
            return $matches[1] . $level . $matches[3];
        }, $html);
    }

    /**
     * @param string $html
     * @return string
     */
    private function replaceCommentHeadlines($html)
    {
        return preg_replace('/<h\d+.*?>(.*?)<\\/h\d+>/i',"<p><strong>\\1</strong></p>", $html);
    }

    /**
     * @param string $html
     *
     * @return string
     */
    private function replaceImageUrlForProxy($html)
    {
        return preg_replace_callback('/(<img[^>]+?)src=(?:["\']([^"\']+)["\']|([^\s"\'`=<>]+))/i', function($matches) {
            return $matches[1] . 'src="' . Yii::$app->proxyFile->getConvertUrl($matches[2]) . '""';
        }, $html);
    }
}
