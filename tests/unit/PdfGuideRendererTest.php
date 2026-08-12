<?php

use app\apidoc\PdfGuideRenderer;

class PdfGuideRendererTest extends \Codeception\Test\Unit
{
    public function testAddsBlankLineBeforeFencedCodeBlock()
    {
        $markdown = "To delete it use\n```php\necho 'ok';\n```\n";
        $expected = "To delete it use\n\n```php\necho 'ok';\n```\n";

        self::assertSame($expected, PdfGuideRenderer::normalizeMarkdown($markdown));
    }

    public function testAddsBlankLineBeforeLanguageLessFencedCodeBlock()
    {
        $markdown = "Example\n```\nplain text\n```\n";
        $expected = "Example\n\n```\nplain text\n```\n";

        self::assertSame($expected, PdfGuideRenderer::normalizeMarkdown($markdown));
    }

    public function testAddsQuotedBlankLineBeforeFencedCodeBlockInBlockquote()
    {
        $markdown = "> Example\n> ```php\n> echo 'ok';\n> ```\n";
        $expected = "> Example\n>\n> ```php\n> echo 'ok';\n> ```\n";

        self::assertSame($expected, PdfGuideRenderer::normalizeMarkdown($markdown));
    }

    public function testNormalizesMisparsedFencedCodeBlock()
    {
        $latex = <<<'LATEX'
\mintinline{text}{`}php
Yii::\$app->cache->delete(['yii\widgets\FragmentCache', $id]);
\mintinline{text}{`}
LATEX;

        $expected = <<<'LATEX'
\begin{minted}{php}
Yii::$app->cache->delete(['yii\widgets\FragmentCache', $id]);
\end{minted}
LATEX;

        self::assertSame($expected, PdfGuideRenderer::normalizeLatex($latex));
    }

    public function testReplacesInlineMintedInsideTableOnly()
    {
        $latex = <<<'LATEX'
\mintinline{text}{outside}
\begin{tabularx}{\textwidth}{|c|c|}
\mintinline{text}{lt} & \mintinline{text}{<}\\ \hline
\mintinline{text}{lte} & \mintinline{text}{<=}\\ \hline
\end{tabularx}
LATEX;

        $expected = <<<'LATEX'
\mintinline{text}{outside}
\begin{tabularx}{\textwidth}{|c|c|}
\texttt{\detokenize{lt}} & \texttt{\detokenize{<}}\\ \hline
\texttt{\detokenize{lte}} & \texttt{\detokenize{<=}}\\ \hline
\end{tabularx}
LATEX;

        self::assertSame($expected, PdfGuideRenderer::normalizeLatex($latex));
    }
}
