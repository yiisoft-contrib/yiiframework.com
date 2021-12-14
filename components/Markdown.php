<?php

namespace app\components;

use cebe\markdown\GithubMarkdown;
use yii\apidoc\helpers\MarkdownHighlightTrait;

class Markdown extends GithubMarkdown
{
    use MarkdownHighlightTrait;

    /**
     * Add bootstrap classes to tables.
     * @inheritdoc
     */
    public function renderTable($block)
    {
        return str_replace('<table>', '<table class="table table-bordered table-striped">', parent::renderTable($block));
    }
}
