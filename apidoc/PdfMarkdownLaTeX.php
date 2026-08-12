<?php

namespace app\apidoc;

/**
 * Prevents recoverable HTML parser diagnostics from aborting PDF generation.
 */
class PdfMarkdownLaTeX extends \yii\apidoc\helpers\ApiMarkdownLaTeX
{
    protected function renderApiLinkText($title)
    {
        $useInternalErrors = libxml_use_internal_errors(true);

        try {
            return parent::renderApiLinkText($title);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($useInternalErrors);
        }
    }
}
