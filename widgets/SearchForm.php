<?php
/**
 *
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\widgets;


use yii\base\Widget;
use yii\helpers\Html;

class SearchForm extends Widget
{
    public $language;
    public $version;
    public $type;

    public $placeholder = 'Search…';

    public $value;

    public function run()
    {
        $html = '';

        $url = [
            'search/global',
            'language' => $this->language,
            'version' => $this->version,
            'type' => $this->type
        ];
        $html .= Html::beginForm($url, 'get', ['class' => 'mini-search-form']);

        $html .= Html::input('text', 'q', $this->value, [
            'class' => 'form-control',
            'placeholder' => $this->placeholder,
            'autocomplete' => 'off',
            'aria-label' => 'Search box',
        ]);

        $html .= ' ' . Html::submitButton('Search', ['class' => 'btn btn-primary sr-only']);

        $html .= Html::endForm();
        return $html;
    }
}
