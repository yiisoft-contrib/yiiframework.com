<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $versions array */
/* @var $versionInfo array */

$this->title = 'Download Yii';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container style_external_links">
    <div class="row">
        <div class="content site-license">
            <p>
	            Yii is an open source project released under the terms of the <?= Html::a('BSD License', ['site/license']) ?>.
	            This means that you can use Yii for free to develop either open-source or proprietary Web applications.
            </p>

            <p>
	            On this page you find download options for the current two major versions of Yii,
	            <a href="#yii2-0">version 2.0</a> and <a href="#yii1-1">version 1.1</a> as well as an <a href="#archive">archive</a> of old resources.
            </p>

	        <?php foreach($versionInfo as $version => $info): ?>

		        <?php if($info['status'] === 'deprecated') continue; ?>


	            <h2 id="yii<?= str_replace('.', '-', $version) ?>">Yii <?= $version ?> <span class="label"><?= $info['status'] ?></span></h2>

		        <p class="intro">
			        <?= $info['summary'] ?>
		        </p>

		        <p>
		        The latest release of Yii <?= $version ?> is <strong><?= $latest = key($versions[$version]) ?></strong>
		        released on <strong><?= $versions[$version][$latest] ?></strong>.
		        For changes in this and older Versions, see the <?= Html::a('CHANGELOG', '#') ?> file.
		        Instructions on how to upgrade to this version can be found in the <?= Html::a('UPGRADE', '#') ?> file.
		        </p>

		        Download options:

		        <ul>
			        <li>source code: Files, GIT, SVN

				        <ul>
					        <li>GIT: <?= $info['git-url'] ?></li>
					        <li>SVN: <?= $info['svn-url'] ?></li>
				        </ul>

			        </li>
			        <li>documentation</li>
		        </ul>

		        <h3 id="yii2-docs">Offline Documentation</h3>

				<?php foreach(Yii::$app->params['guide.versions']['2.0'] as $locale => $language): ?>
				<ul>
				  <li><?php
				      echo "$language: ";
				      $file = "yii-docs-2.0-$locale.tar";
				      echo Html::a("$file.gz", ['site/file', 'category' => 'docs-offline', 'file' => "$file.gz"]) . ' ';
				      echo Html::a("$file.bz2", ['site/file', 'category' => 'docs-offline', 'file' => "$file.bz2"]);
				  ?>
				  </li>
				</ul>
				<?php endforeach; ?>


		        Information about this version:

		        <ul>
			        <li>Latest: <strong><?= $latest = key($versions[$version]) ?></strong></li>
			        <li>status: <strong><?= $info['status'] ?></strong></li>
			        <li>minimum PHP version: <strong><?= $info['min-php-version'] ?></strong></li>
			        <?php if ($info['support-until']): ?>
				        <li>End of Maintenance: <strong><?= $info['support-until'] ?></strong></li>
			        <?php endif; ?>
			        <?php if ($info['security-until']): ?>
				        <li>Security support until: <strong><?= $info['security-until'] ?></strong></li>
			        <?php endif; ?>
		        </ul>

	        <?php endforeach; ?>

	        <h2 id="archive">Archive</h2>

	        <p>Below you'll find a list of download resources from old Yii versions and other resources that were available
		        for download before but are not referenced elsewhere anymore.
	        </p>

	        <?php foreach($versionInfo as $version => $info): ?>

		        <?php if($info['status'] !== 'deprecated') continue; ?>

	            <h3 id="yii<?= str_replace('.', '-', $version) ?>">Yii <?= $version ?></h3>

		        <p class="intro">
			        <?= $info['summary'] ?>
		        </p>

		        <p>
		        The latest release of Yii <?= $version ?> is <?= $latest = key($versions[$version]) ?>
		        released on <?= $versions[$version][$latest] ?>.
		        <?php /*
		        For changes in this and older Versions, see the <?= Html::a('CHANGELOG', '#') ?> file.
		        Instructions on how to upgrade to this version can be found in the <?= Html::a('UPGRADE', '#') ?> file.
                */ ?>
		        </p>

		        <ul>
			        <li>... TODO: Table of files in tar.gz and zip format for framework file <b>and</b> docs file <a href="https://code.google.com/p/yii/downloads/list?can=1&q=1.0">example files</a> ...</li>
		        </ul>

	        <?php endforeach; ?>

	        <h3>Other resources</h3>

	        <ul>
		        <li>... TODO: <a href="https://code.google.com/p/yii/downloads/list?can=1&q=vmware+OR+blog+OR+benchmark+OR+cheatsheet">example files</a> </li>
	        </ul>



        </div>
    </div>
</div>
