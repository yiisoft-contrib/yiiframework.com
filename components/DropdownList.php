<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\Html;

class DropdownList extends Widget
{
    /**
     * @var string the html tag to render for the container element
     */
    public $tag = 'div';
    /**
     * @var string the current selection text (it will not be HTML-encoded)
     */
    public $selection;
    /**
     * @var array list of items in the nav widget. Each array element represents a single
     * menu item which can be either a string or an array with the following structure:
     *
     * - label: string, required, the nav item label.
     * - url: optional, the item's URL. Defaults to "#".
     *
     * If an item is a string, it will be rendered directly without HTML encoding.
     */
    public $items = [];
    /**
     * @var bool whether to show the dropdown list as a button. If false, a link will be used.
     */
    public $useButton = false;
    /**
     * @var boolean whether the nav items labels should be HTML-encoded.
     */
    public $encodeLabels = true;
    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];


    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        if (!isset($this->options['class'])) {
            Html::addCssClass($this->options, 'dropdown');
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo $this->renderItems();
        BootstrapAsset::register($this->getView());
    }

    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $items = [];
        foreach ($this->items as $i => $item) {
            $label = $this->encodeLabels ? Html::encode($item['label']) : $item['label'];
            $items[] = '<li role="presentation">' . Html::a($label, $item['url'], ['role' => 'menuitem', 'tabindex' => -1]) . '</li>';
        }
        $menu = '<ul class="dropdown-menu" role="menu">' . implode("\n", $items) . '</ul>';
        if ($this->useButton) {
            $selection = '<button class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">' . $this->selection . ' <span class="caret"></span></button>';
        } else {
            $selection = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">' . $this->selection . ' <span class="caret"></span></a>';
        }
        return Html::tag($this->tag, $selection . $menu, $this->options);
    }
}
