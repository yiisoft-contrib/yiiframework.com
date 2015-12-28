<?php
/**
 * @var $this yii\web\View
 * @var $versions array all available API versions
 * @var $version string the currently chosen API version
 * @var $section string the currently active API file
 * @var $content string the API page content
 * @var $packages array the API page menu structure
 */

use app\components\SideNav;
use yii\apidoc\templates\bootstrap\SideNavWidget;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

$this->registerJs("
    $('.has-children.active + div').addClass('active-parent');

    // find the spans with class 'api-ns-level-1' that contains
    // text that ends with a backslash
    // - i.e. only target those that has a child namespace
    // that is 4 backslashes for jQuery and 4 for PHP to escape them
    $(\".api-ns-level-1:contains('\\\\\\\\')\").css('color', '#247BA0');

");


$this->title = Html::encode((!empty($title) ? "$title - " : '') .  "API Documentation for Yii $version");
?>
<div class="guide-header-wrap">
    <div class="container guide-header">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="guide-headline h1">API Documentation for Yii <?= $version ?></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-offset-8 col-md-4">
                <?= $this->render('partials/_versions.php', compact('version', 'versions', 'section')) ?>
            </div>
        </div>
    </div>
</div>
<div class="container api-content">
    <div class="row visible-xs">
        <div class="col-md-12">
            <p class="pull-right topmost">
                <button type="button" title="Toggle Side-Nav" class="btn btn-primary btn-xs" data-toggle="offcanvas">SideNav</button>
            </p>
        </div>
    </div>
    <div class="row row-offcanvas">
        <div class="col-md-3">
        <?php
        ksort($packages);
//        print_r($packages);
        $nav = [];
        foreach ($packages as $package => $classes) {

            $packageLabel = [];
            foreach(explode('.', $package) as $level => $ns) {
                $packageLabel[] = Html::tag('span', Html::encode($ns) . ($level < substr_count($package, '.') ? '.' : ''), ['class' => "api-ns-level-$level"]);
            }
            $packageLabel = implode('', $packageLabel);

            $nav[$package] = [
                'label' => $packageLabel,
                'encodeLabel' => false,
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
        <?= SideNav::widget([
            'id' => 'api-navigation',
            'items' => $nav,
            'view' => $this,
        ]) ?>
        </div>
        <div class="col-md-9" role="main">
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
