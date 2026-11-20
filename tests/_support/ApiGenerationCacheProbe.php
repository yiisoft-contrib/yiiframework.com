<?php

use app\commands\ApiController;

class ApiGenerationCacheProbe extends ApiController
{
    public $afterRender;

    protected function storeMarkdownContext($context, $location): void
    {
        ($this->afterRender)($this->template, $location);
        parent::storeMarkdownContext($context, $location);
    }
}
