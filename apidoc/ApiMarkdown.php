<?php

namespace app\apidoc;

use yii\helpers\Markdown;

class ApiMarkdown extends \yii\apidoc\helpers\ApiMarkdown
{
    public static function process($content, $context = null, $paragraph = false)
    {
        $flavor = 'app-api';
        if (!isset(Markdown::$flavors[$flavor])) {
            Markdown::$flavors[$flavor] = new static();
        }

        if (is_string($context)) {
            $context = static::$renderer->apiContext->getType($context);
        }
        Markdown::$flavors[$flavor]->renderingContext = $context;

        return $paragraph
            ? Markdown::processParagraph($content, $flavor)
            : Markdown::process($content, $flavor);
    }

    protected function consumeHtml($lines, $current)
    {
        if (strncmp($lines[$current], '<!--', 4) === 0) {
            return parent::consumeHtml($lines, $current);
        }

        preg_match('/^<([a-z][a-z0-9]*)\b/i', $lines[$current], $matches);
        $tag = $matches[1];
        $level = in_array(strtolower($tag), $this->selfClosingHtmlElements, true) ? -1 : 0;
        $content = [];

        for ($i = $current, $count = count($lines); $i < $count; $i++) {
            $line = $lines[$i];
            $content[] = $line;
            $level += preg_match_all('~<' . preg_quote($tag, '~') . '(?:\s|>)~i', $line);
            $level -= preg_match_all('~</' . preg_quote($tag, '~') . '\s*>~i', $line);
            if ($level <= 0) {
                break;
            }
        }

        return [[
            'html',
            'content' => implode("\n", $content),
        ], $i];
    }
}
