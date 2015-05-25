<?php
/**
 * @var $this yii\web\View
 * @var $versions array all available API versions
 * @var $version string the currently chosen API version
 * @var $section string the currently active API file
 * @var $content string the API page content
 * @var $packages array the API page menu structure
 */

use yii\apidoc\templates\bootstrap\SideNavWidget;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

$this->title = Html::encode((!empty($title) ? "$title - " : '') .  "Yii API Documentation $version");
?>

<div class="container api-content">
    <div class="row">
        <div class="col-md-3">
        <?php
        ksort($packages);
//        print_r($packages);
        $nav = [];
        foreach ($packages as $package => $classes) {
            $nav[$package] = [
                'label' => $package,
                'url' => '#',
                'items' => [],
            ];
            foreach($classes as $class) {
                $nav[$package]['items'][] = [
                    'label' => $class,
                    'url' => Url::to(['api/view', 'version' => $version, 'section' => $class]),
                    'active' => isset($section) && ($section == $class),
                ];
            }
        } ?>
        <?= SideNavWidget::widget([
            'id' => 'api-navigation',
            'items' => $nav,
            'view' => $this,
        ]) ?>
        </div>
        <div class="col-md-9" role="main">
            <?= $this->render('_versions.php', compact('version', 'versions', 'section')) ?>
            <div class="content api1">
            <?= $content ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?= \app\components\Comments::widget([
        'objectType' => 'api',
        'objectId' => $version . '-' . $section,
    ]) ?>
</div>

<?php

$this->registerJs(<<<'JS'

// toggle inherited methods
$(".api-content a.toggle").on('click', function () {
    var $this = $(this);
    if ($this.hasClass('properties-hidden')) {
        $this.text($this.text().replace(/Show/,'Hide'));
        $this.parents(".summary").find(".inherited").show();
        $this.removeClass('properties-hidden');
    } else {
        $this.text($this.text().replace(/Hide/,'Show'));
        $this.parents(".summary").find(".inherited").hide();
        $this.addClass('properties-hidden');
    }

    return false;
});

// toggle source code
$(".api-content .sourceCode a.show").on('click', function () {
    var $this = $(this);
    if ($this.hasClass('sourceCode-visible')) {
        $this.text($this.text().replace(/hide/,'show'));
        $this.parents(".sourceCode").find("div.code").hide();
        $this.removeClass('sourceCode-visible');
    } else {
        $this.text($this.text().replace(/show/,'hide'));
        $this.parents(".sourceCode").find("div.code").show();
        $this.addClass('sourceCode-visible');
    }

    return false;
});
$(".api-content a.sourceLink").click(function(){
    $(this).attr('target','_blank');
});

// make API redirection more straightforward
var $context = $('.summary.docProperty, .summary.docMethod, .summary.docEvent');
// if the current hash is not a detail one, try jumping to the detail one directly
if(location.hash && location.hash.indexOf('-detail')<0) {
    var $link = $(location.hash+' a', $context);
    if($link.length) {
        location.href = $link.attr('href');
    }
}
// monitor every link in the page that jumps within the page
$('.api-content a').click(function(){
    var href = $(this).attr('href');
    if(href.length>1 && (href.indexOf('#')==0 || href.indexOf(location.pathname+'#')==0)) {
        var pos = href.indexOf('#');
        var hash = href.substring(pos);
        if (hash.indexOf('-detail')<0) {
            if($(hash+'-detail').length) {
                location.hash = hash+'-detail';
                return false;
            }
            else {
                var l =	$(hash+' a', $context);
                if (l.length) {
                    location.href = l.attr('href');
                    return false;
                }
            }
        }
    }
});

JS
);
