<?php

namespace app\apidoc;

use Yii;
use yii\helpers\Console;

/**
 * Works around LaTeX output that cannot be compiled by the legacy PDF template.
 */
class PdfGuideRenderer extends \yii\apidoc\templates\pdf\GuideRenderer
{
    public function render($files, $targetDir)
    {
        $fileData = [];
        $chapters = $this->loadGuideStructure($files);
        // Progress is updated for local guide-structure entries below, not for
        // every discovered input file. Count the same entries so the bar does
        // not over/undershoot when the structure contains remote URLs.
        $fileCount = array_sum(array_map(static function (array $chapter) {
            return count(array_filter($chapter['content'], static function (array $content) {
                return strpos($content['file'], 'http://') !== 0 && strpos($content['file'], 'https://') !== 0;
            }));
        }, $chapters)) + 1;
        if ($this->controller !== null) {
            Console::startProgress(0, $fileCount, 'Rendering markdown files: ', false);
        }
        $done = 0;
        foreach ($files as $file) {
            $fileData[basename($file)] = self::normalizeMarkdown(file_get_contents($file));
        }

        $md = new PdfMarkdownLaTeX();
        $output = '';
        foreach ($chapters as $chapter) {
            if (isset($chapter['headline'])) {
                $output .= '\chapter{' . $chapter['headline'] . "}\n";
            }
            foreach ($chapter['content'] as $content) {
                if (strpos($content['file'], 'http://') === 0 || strpos($content['file'], 'https://') === 0) {
                    continue;
                }
                $output .= '\label{' . $content['file'] . '}';
                // loadGuideStructure() may retain a directory in the entry,
                // while $fileData is intentionally keyed by basename. Without
                // normalization a valid guide page becomes an "Error: not
                // existing file" page in the PDF.
                $fileName = basename($content['file']);
                if (isset($fileData[$fileName])) {
                    $md->labelPrefix = $content['file'] . '#';
                    $output .= $md->parse($fileData[$fileName]) . "\n\n";
                } else {
                    $output .= '\newpage\textbf{Error: not existing file: ' . $content['file'] . '}\newpage' . "\n";
                }

                if ($this->controller !== null) {
                    Console::updateProgress(++$done, $fileCount);
                }
            }
        }

        file_put_contents($targetDir . '/guide.tex', self::normalizeLatex($output));
        $templateDir = Yii::getAlias('@vendor/yiisoft/yii2-apidoc/templates/pdf');
        copy($templateDir . '/main.tex', $targetDir . '/main.tex');
        copy($templateDir . '/title.tex', $targetDir . '/title.tex');
        copy($templateDir . '/Makefile', $targetDir . '/Makefile');

        if ($this->controller !== null) {
            Console::updateProgress(++$done, $fileCount);
            Console::endProgress(true);
            $this->controller->stdout('done.' . PHP_EOL, Console::FG_GREEN);
        }

        echo "\nnow run `make` in $targetDir (you need pdflatex to compile pdf file)\n\n";
    }

    public static function normalizeMarkdown($markdown)
    {
        // cebe/markdown-latex requires a blank line before a fenced block. In
        // caching-fragment.md the missing line made it emit inline backticks
        // and interpret yii\widgets\FragmentCache as a LaTeX command, aborting
        // the English and Japanese PDFs with "Undefined control sequence".
        //
        // Keep the blockquote prefix on the inserted blank line. The Russian
        // structure-applications.md contains a `> ```php` fence; inserting an
        // unquoted blank line makes the legacy parser nest minted environments
        // and pdflatex aborts with "Bad space factor (0)".
        //
        // The Unicode modifier is essential: without it PCRE treats byte 0x85
        // as a newline, but 0x85 is also the trailing UTF-8 byte in Cyrillic х.
        // Splitting there corrupted Russian text and caused pdflatex's
        // "Invalid UTF-8 byte sequence" error.
        $lines = preg_split('~\R~u', $markdown);
        $result = [];
        $fencePrefix = null;

        foreach ($lines as $line) {
            if (preg_match('~^([ \t]*(?:>[ \t]*)*)```(?:[a-zA-Z0-9_+.-]+)?[ \t]*$~', $line, $matches)) {
                if ($fencePrefix === null) {
                    $prefix = $matches[1];
                    $previousLine = end($result);
                    $previousContent = preg_replace('~^' . preg_quote($prefix, '~') . '~', '', $previousLine);
                    if ($previousLine !== false && trim($previousContent) !== '') {
                        $result[] = rtrim($prefix);
                    }
                    $fencePrefix = $prefix;
                } elseif ($matches[1] === $fencePrefix) {
                    $fencePrefix = null;
                }
            }

            $result[] = $line;
        }

        return implode("\n", $result);
    }

    public static function normalizeLatex($latex)
    {
        // Defense in depth for fences the Markdown normalization does not
        // recognize: convert the legacy parser's inline-backtick output into a
        // real minted block before PHP namespaces can become LaTeX commands.
        $latex = preg_replace_callback(
            '~\\\\mintinline\{text\}\{`\}([a-zA-Z0-9_+.-]+)\R(.+?)\R\\\\mintinline\{text\}\{`\}~s',
            static function (array $matches) {
                $code = str_replace('\\$', '$', $matches[2]);

                return "\\begin{minted}{{$matches[1]}}\n{$code}\n\\end{minted}";
            },
            $latex
        );

        // minted invokes Pygments through shell escape for every inline value.
        // On the production TeX Live 2020 stack, the Russian filtering-keywords
        // table failed on cells such as < and <= with "Missing Pygments output".
        // These are plain-text cells, so \detokenize preserves their appearance
        // and literal characters without the fragile external invocation.
        return preg_replace_callback(
            '~\\\\begin\{tabularx\}.*?\\\\end\{tabularx\}~s',
            static function (array $table) {
                return preg_replace(
                    '~\\\\mintinline\{text\}\{([^{}]*)\}~',
                    '\\texttt{\\detokenize{$1}}',
                    $table[0]
                );
            },
            $latex
        );
    }
}
