<?php

namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * Highlight widget renders the content between it's begin end tags.
 *
 * Usage example:
 *
 * ```php
 * <body>
 *     <?php Highlight::begin(); ?>
 *         <div class="nav-bar">
 *         </div>
 *         <div class="content">
 *         </div>
 *     <?php Highlight::end(); ?>
 * </body>
 * ```
 */
class Highlight extends Widget
{
    // what language to highlight in
    public $language = 'php';

    // if $capture is set to true
    // then $captured will be set to
    // the highlighted result and not returned
    public $capture = false;
    public $captured = null;

    protected $highlighter = null;

    /**
     * Starts capturing an output to be highlighted.
     */
    public function init()
    {
        parent::init();
        ob_start();
    }

    protected function getHighlighter()
    {
        if(!isset($this->highlighter)) {
            $this->highlighter = new \Highlight\Highlighter();
        }
        return $this->highlighter;
    }

    /**
     * Marks the end of content to be highlighted.
     * Stops capturing an output and returns highlighted result.
     */
    public function run()
    {
        $content = ob_get_clean();
        $highlighter = $this->getHighlighter();
        $result = $highlighter->highlight($this->language, $content);
        $rendered = "<pre class='hljs " . $result->language . "'>" . rtrim($result->value) . "</pre>";
        if($this->capture) {
            $this->captured = $rendered;
            return;
        }
        return $rendered;
    }
}
