<?php

use app\models\Guide;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $versions array */
/* @var $versionInfo array */

$this->title = 'Download Yii';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container site-header">
	<div class="row">
		<div class="col-sm-7 col-md-6 col-lg-7">
			<h1>Download</h1>
			<h2>Yii Framework & Offline Documentation</h2>
		</div>
		<div class="col-sm-5 col-md-6 col-lg-5">
			<img class="background" src="<?= Yii::getAlias('@web/image/download/downloads.svg')?>" alt="Yii downloads">
		</div>
	</div>
</div>


<div class="container download">
	<div class="content">
		<div class="row">
            <div class="col-md-12">
                <p class="small">
                    Yii is an open source project released under the terms of the <?= Html::a('BSD License', ['site/license']) ?>.
				    This means that you can use Yii for free to develop either open-source or proprietary Web applications.
                </p>

                <p class="small">
                    There are two ways to install Yii:
                    by using Composer or
                    by downloading an application template.
                    We highly recommend you to use Composer.
                </p>

                <p class="small">
                    If you do not have Composer installed yet,
                    you may install it following the instructions
                    on the <a href="https://getcomposer.org/download/">Composer website</a>.
                </p>
            </div>

			<div class="col-md-12">
				<div class="heading-separator">
					<h2><span>Yii 2.0</span></h2>
				</div>
				<?php $version = '2.0'; ?>
				<p class="small">

                <ul>
                    <li>Latest release is <strong><?= $latest = key($versions[$version]) ?></strong> (<strong><?= $versions[$version][$latest] ?></strong>)</li>
                    <li><?= Html::a('Changes in this and older versions', "https://github.com/yiisoft/yii2/blob/$latest/framework/CHANGELOG.md") ?></li>
                    <li><?= Html::a('Upgrade instructions', "https://github.com/yiisoft/yii2/blob/$latest/framework/UPGRADE.md") ?></li>
                    <li><a href="https://github.com/yiisoft/yii2/releases">List of all Yii 2.x releases</a></li>
                </ul>


				<h1 id="install-composer">Install via Composer (recommended)</h1>

				<p class="small">
				The installation instructions described here are a short summary, you may want to check the
					<?= Html::a('Definitive Guide', [
						'guide/view',
						'type' => 'guide',
						'version' => '2.0',
						'language' => 'en',
						'section' => 'start-installation',
						'#' => 'installing-via-composer',
					]) ?> for more detailed instructions.</p>

				<p class="small">
					Choose one of the application templates to start installing Yii 2.0.
					An application template is a package that contains a skeleton Web application written in Yii
					which you can start building your application from.
					If you just start with Yii, we recommend the installation of the <strong>basic</strong> template.
				</p>

				<div class="row ptrem1">
					<div class="col-md-3">
					<p class="small">To install the <b>basic</b> application template, run the following command:</p>
					</div>
					<div class="col-md-9">
					<pre><code class="hljs bash language-bash">php composer.phar create-project yiisoft/yii2-app-basic basic</code></pre>
					</div>
				</div>

				<p class="small">
					The best way to get started with the basic application template is to follow the
					<?= Html::a('introduction in the definitive guide', [
						'guide/view',
						'type' => 'guide',
						'version' => '2.0',
						'language' => 'en',
						'section' => 'start-installation',
						'#' => 'verifying-installation',
					]) ?>.
				</p>

				<div class="row ptrem1">
					<div class="col-md-3">
					<p class="small">To install the <b>advanced</b> application template, run the following command:</p>
					</div>
					<div class="col-md-9">
					<pre><code class="hljs bash language-bash">php composer.phar create-project yiisoft/yii2-app-advanced advanced</code></pre>
					</div>
				</div>

				<p class="small">
					When you have installed the advanced application template, you should check out its <a href="<?= Yii::getAlias('@web/extension/yiisoft/yii2-app-advanced/doc/guide') ?>">documentation</a>
					to learn how to use it.
				</p>

				<h1 id="install-from-archive">Install from an Archive File</h1>

				<p class="small">Download one of the following archive files, and then extract it to Web-accessible folder:</p>

				<div class="row extensions">
					<div class="col-md-6 col-sm-12 col-xs-12">
						<a class="btn btn-lg btn-default btn-block download-btn" href="https://github.com/yiisoft/yii2/releases/download/<?= $latest ?>/yii-basic-app-<?= $latest ?>.tgz">
						<img alt="tar gz" src="<?= Yii::getAlias('@web/image/tgz.svg')?>" />
						<span>Yii 2 with basic application template</span></a>
					</div>
					<div class="col-md-6 col-sm-12 col-xs-12">
						<a class="btn btn-lg btn-default btn-block download-btn" href="https://github.com/yiisoft/yii2/releases/download/<?= $latest ?>/yii-advanced-app-<?= $latest ?>.tgz">
						<img alt="tar gz" src="<?= Yii::getAlias('@web/image/tgz.svg')?>" />
						<span>Yii 2 with advanced application template</span></a>
					</div>
				</div>

				<p class="small">
					Afterwards you may want to follow the
					<?= Html::a('introduction in the definitive guide', [
						'guide/view',
						'type' => 'guide',
						'version' => '2.0',
						'language' => 'en',
						'section' => 'start-installation',
						'#' => 'verifying-installation',
					]) ?> to get started, or
					check out the <a href="<?= Yii::getAlias('@web/extension/yiisoft/yii2-app-advanced/doc/guide') ?>">documentation of the advanced application template</a>
					to learn how to use it.
				</p>

				<h1>Upgrade from Older Versions</h1>

				<p class="small">If you are upgrading from Yii 2.0.x with Composer, simply run the following commands in your project's root directory:</p>

				<pre><code class="hljs bash language-bash">php composer.phar update yiisoft/yii2 yiisoft/yii2-composer bower-asset/jquery.inputmask</code></pre>

				<p class="small">
					When upgrading Yii there might be changes that require adjustment in you application code, so you should always check
					the <?= Html::a('UPGRADE', "https://github.com/yiisoft/yii2/blob/$latest/framework/UPGRADE.md") ?> notes. These also contain
					more detailed instructions on how to upgrade Yii with composer.
				</p>

				<p class="small">
					If you installed Yii using an archive file, you can either follow the progress described above using Composer,
					or download a new release file and replace the contents of the <code>vendor/</code> directory in your application
					with the files from the new archive file.
				</p>

				<p class="small">
					Upgrading from Yii 1.1 is not trivial and requires rewriting a great part of your application code.
					Please refer to the guide about
					<?= Html::a('upgrading from Yii 1.1', [
						'guide/view',
						'type' => 'guide',
						'version' => '2.0',
						'language' => 'en',
						'section' => 'intro-upgrade-from-v1',
					]) ?> for more details.
				</p>

				<div class="heading-separator">
					<h2 id="offline-documentation"><span>Offline Documentation</span></h2>
				</div>

				<p class="small">
					The Definitive Guide to Yii 2.0 and the API Documentation are available for offline browsing.
					Below you will find the download packages including the HTML files of Guide and API Documentation
					as well as links to the PDF versions of the Guide in different languages.
				</p>

				<ul class="offline-doc-v2">
					<?php foreach(Yii::$app->params['guide.versions'][$version] as $locale => $language): ?>
					<li>
						<img src="<?= Yii::getAlias("@web/image/download/$locale.png") ?>" alt="<?= $locale ?> language"/>
						<span><?= $language ?></span>
						<span>
						<?php
							$guide = Guide::load($version, $locale);
							if ($guide === null) {
								continue;
							}
							$items = [];
							if ($guide->getDownloadFile('pdf') !== false) {
								echo Html::a(
									'PDF',
									['guide/download', 'version' => $guide->version, 'language' => $guide->language, 'format' => 'pdf']
								) . ' - ';
							}
							if ($guide->getDownloadFile('tar.gz') !== false) {
								echo Html::a(
									'.tar.gz',
									['guide/download', 'version' => $guide->version, 'language' => $guide->language, 'format' => 'tar.gz']
								) . ' - ';
							}
							if ($guide->getDownloadFile('tar.bz2') !== false) {
								echo Html::a(
									'.tar.bz2',
									['guide/download', 'version' => $guide->version, 'language' => $guide->language, 'format' => 'tar.bz2']
								);
							}
						?>
						</span>
					</li>
					<?php endforeach; ?>
				</ul>

				<span id="yii-1-1">&nbsp;</span>
				<div class="heading-separator">
					<h2><span>Yii 1.1</span></h2>
				</div>
				<?php $version = '1.1'; ?>

                <p class="text-center medium">Yii 1.1 is currently in <a href="/news/90/update-on-yii-1-1-support-and-end-of-life/">maintenance mode</a>.</p>
                <p class="text-center">Continued support and bug fixes for this version have been provided until <b><?= $versionInfo[$version]['support-until'] ?></b>.</p>
                <p class="text-center">Security fixes and PHP 7 compatibility until <b><?= $versionInfo[$version]['security-until'] ?></b>.</p>

                <ul>
                    <li>Latest release is <strong><?= $latest = key($versions[$version]) ?></strong> (<strong><?= $versions[$version][$latest] ?></strong>)</li>
                    <li><a href="<?= $versionInfo[$version]['download-url'] ?>.tar.gz">Source Code</a> (<a href="<?= $versionInfo['1.1']['download-url'] ?>.zip">.zip</a>)</li>
                    <li><?= Html::a('Changes in this and older versions', "https://github.com/yiisoft/yii/blob/$latest/CHANGELOG") ?></li>
                    <li><?= Html::a('Upgrade instructions', "https://github.com/yiisoft/yii/blob/$latest/UPGRADE") ?></li>
                    <li><a href="<?= str_replace('yii-', 'yii-docs-', $versionInfo[$version]['download-url']) ?>.tar.gz">Offline Documentation</a>
                        (<a href="<?= str_replace('yii-', 'yii-docs-', $versionInfo[$version]['download-url']) ?>.zip">.zip</a>)</li>
                    <li><a href="https://github.com/yiisoft/yii/releases">List of all Yii 1.x releases</a></li>
                </ul>

				<p class="small">
					You may obtain the latest development version of the Yii 1.1 code from the <?= Html::a('Github repository', $versionInfo[$version]['github-url']) ?> using git:
				</p>
				<p><code>git clone <?= $versionInfo[$version]['git-url'] ?> yii</code></p>
				<p class="small">or via SVN using:</p>
				<p><code>svn checkout <?= $versionInfo[$version]['svn-url'] ?> yii</code></p>

                <p>The <a href="https://packagist.org/packages/yiisoft/yii">yiisoft/yii</a> composer package is available as well.</p>

				<h3 class="text-center">Documentation</h3>

				<ul class="offline-doc-v1">
					<?php foreach(Yii::$app->params['guide.versions']['1.1'] as $locale => $language): ?>
					<li>
						<?= Html::a($language, ['/doc/guide/1.1/'.$locale]) ?>
					</li>
					<?php endforeach; ?>
				</ul>


				<span id="archive">&nbsp;</span>
				<div class="heading-separator">
					<h2><span>Archive</span></h2>
				</div>

				<p class="text-center">If you are looking for Yii 1.0 and other really old resources, you may find them at the <a href="https://github.com/yiisoft-contrib/museum">museum</a>.</p>

			</div>
		</div>
	</div>
</div>

