<?php

namespace app\components;

use Yii;
use yii\apidoc\helpers\ApiMarkdown;
use yii\apidoc\models\Context;
use yii\helpers\HtmlPurifier;
use yii\helpers\Markdown;
use yii\helpers\StringHelper;

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

        $output = $this->replaceImageUrlForProxy($output);

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

        $output = $this->replaceImageUrlForProxy($output);

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

        $output = HtmlPurifier::process($html, $this->purifierConfig);

        $output = $this->replaceImageUrlForProxy($output);

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
        if (!isset(Yii::$app->params['image-proxy'], Yii::$app->params['image-proxy-secret'])) {
            return $html;
        }

        return preg_replace_callback('/(<img[^>]+?)src=(["\'])([^"\']+)\2/i', function($matches) {
            return $matches[1] . 'src="' . $this->generateProxyUrl($matches[3]) . '"';
        }, $html);
    }

    /**
     * Proxy external images through a content filter.
     *
     * Avoid the following issues:
     *
     * - Blocking by Content-Security-Policy header
     * - HTTPs vs. HTTP mixed content problems
     * - content filter will only allow passing images of type gif, png, jpg, webm and svg
     *   other content will be blocked.
     * - proxied images are not able to set cookies, i.e. not able to track users.
     *
     * URLs will be replaced as follows:
     *
     * - Input: `http://example.com/somepath/someimage.png`
     * - Output: `https://user-content.yiiframework.com/img/<hash>/http/example.com/somepath/someimage.png`
     *
     * `<hash>` is a value generated from the URL and a secret to avoid abuse of the proxy server.
     */
    private function generateProxyUrl($sourceUrl)
    {
        if (preg_match('~^(https?)://([^/]+)/(.*)$~', $sourceUrl, $matches)) {

            list( , $proto, $host, $uri) = $matches;

            $proxy = rtrim(Yii::$app->params['image-proxy'] ?? 'https://user-content.yiiframework.com', '/');
            $secret = Yii::$app->params['image-proxy-secret'];

            // https://nginx.org/en/docs/http/ngx_http_secure_link_module.html#secure_link_md5
            // echo "url secret" | openssl md5 -binary | openssl base64 | tr +/ -_ | tr -d =
            $hash = rtrim(StringHelper::base64UrlEncode(md5("$proto://$host/$uri $secret", true)), '=');
        	return "$proxy/img/$hash/$proto/$host/$uri";
        }

        return $sourceUrl;
    }
}
