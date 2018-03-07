<?php

namespace app\controllers;

use yii\web\Controller;

abstract class BaseController extends Controller
{
    /**
     * @var string the title to display in the headline bar under that navigation. e.g. 'Yii Framework Wiki'
     */
    public $sectionTitle;
    /**
     * @var string part of the title to add in the HTML page title. e.g. 'Wiki'
     * Defaults to [[sectionTitle]], if not explicitly set.
     */
    public $headTitle;
    /**
     * @var string limit the global search field scope. Make it aware of the current site context, e.g. only
     * search Wiki when looking at the Wiki.
     */
    public $searchScope;
}
