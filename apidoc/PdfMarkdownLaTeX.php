<?php

namespace app\apidoc;

use DOMDocument;
use yii\apidoc\helpers\EncodingHelper;
use yii\helpers\Markdown;

/**
 * Prevents recoverable HTML parser diagnostics from aborting PDF generation.
 */
class PdfMarkdownLaTeX extends \yii\apidoc\helpers\ApiMarkdownLaTeX
{
    protected function renderApiLinkText($title)
    {
        if (!$title) {
            return $title;
        }

        $title = Markdown::process($title);
        if ($title === '') {
            return '';
        }

        $title = EncodingHelper::convertToUtf8WithHtmlEntities($title);
        if ($title === '') {
            return '';
        }
        $useInternalErrors = libxml_use_internal_errors(true);

        try {
            $doc = new DOMDocument();
            $doc->loadHTML($title);

            return $doc->getElementsByTagName('p')[0]->childNodes[0]->c14n();
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($useInternalErrors);
        }
    }
}
