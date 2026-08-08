<?php

use app\models\Guide;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $versions array */
/* @var $versionInfo array */
/* @var $selectedVersion string */

$latestYii2 = key($versions['2.0']);
$latestYii1 = key($versions['1.1']);
$downloadTitles = [
    '3.0' => 'Download Yii3',
    '2.0' => 'Download Yii 2',
    '1.1' => 'Download Yii 1.1',
];
$this->title = $downloadTitles[$selectedVersion];
$this->params['breadcrumbs'][] = $this->title;

$this->beginBlock('contentSelectors');
echo $this->render('partials/_downloadVersions', ['currentVersion' => $selectedVersion]);
$this->endBlock();
?>
<main class="container content download-page style_external_links">
    <header class="download-page__intro">
        <h1 id="download-page-title"><?= Html::encode($downloadTitles[$selectedVersion]) ?></h1>
        <p class="download-page__note">
            Composer is the recommended way to install Yii. If it is not installed yet, follow the instructions on the
            <a href="https://getcomposer.org/download/">Composer website</a>.
        </p>
    </header>

    <?php if ($selectedVersion === '3.0'): ?>
    <div class="download-version">
        <section class="download-subsection download-subsection--first">
            <header>
                <h3>Install with Composer <span>Recommended</span></h3>
                <p>Choose an application template.</p>
            </header>
            <div class="download-command-list download-command-list--templates">
                <article>
                    <h4><a href="https://packagist.org/packages/yiisoft/app">Web application</a></h4>
                    <p>A traditional server-rendered web application.</p>
                    <pre><code class="hljs bash language-bash">composer create-project yiisoft/app my-project</code></pre>
                </article>
                <article>
                    <h4><a href="https://packagist.org/packages/yiisoft/app-api">API application</a></h4>
                    <p>An application focused on HTTP APIs.</p>
                    <pre><code class="hljs bash language-bash">composer create-project yiisoft/app-api my-api</code></pre>
                </article>
                <article>
                    <h4><a href="https://packagist.org/packages/yiisoft/app-console">Console application</a></h4>
                    <p>A command-line application without a web layer.</p>
                    <pre><code class="hljs bash language-bash">composer create-project yiisoft/app-console my-console</code></pre>
                </article>
            </div>
        </section>

        <section class="download-subsection">
            <header>
                <h3>Documentation</h3>
                <p>Continue with local or Docker setup.</p>
            </header>
            <p><a href="https://yiisoft.github.io/docs/guide/start/creating-project.html">Read the Yii 3 installation guide</a>.</p>
        </section>
    </div>
    <?php endif; ?>

    <?php if ($selectedVersion === '2.0'): ?>
    <div class="download-version">
        <div class="download-version__header">
            <p>Latest release: <strong><?= Html::encode($latestYii2) ?></strong> · <?= Html::encode($versions['2.0'][$latestYii2]) ?></p>
        </div>

        <nav class="download-reference-links" aria-label="Yii 2 release information">
            <?= Html::a('Changelog', "https://github.com/yiisoft/yii2/blob/$latestYii2/framework/CHANGELOG.md") ?>
            <?= Html::a('Upgrade instructions', "https://github.com/yiisoft/yii2/blob/$latestYii2/framework/UPGRADE.md") ?>
            <a href="https://github.com/yiisoft/yii2/releases">All Yii 2 releases</a>
        </nav>

        <section class="download-subsection" id="install-composer">
            <header>
                <h3>Install with Composer <span>Recommended</span></h3>
                <p>Choose an application template.</p>
            </header>

            <div class="download-command-list">
                <article>
                    <h4>Basic application</h4>
                    <p>Recommended when getting started with Yii.</p>
                    <pre><code class="hljs bash language-bash">php composer.phar create-project yiisoft/yii2-app-basic basic</code></pre>
                    <p>
                        Continue with the
                        <?= Html::a('introduction in the Definitive Guide', [
                            'guide/view',
                            'type' => 'guide',
                            'version' => '2.0',
                            'language' => 'en',
                            'section' => 'start-installation',
                            '#' => 'verifying-installation',
                        ]) ?>.
                    </p>
                </article>
                <article>
                    <h4>Advanced application</h4>
                    <p>For applications with distinct frontend and backend environments.</p>
                    <pre><code class="hljs bash language-bash">php composer.phar create-project yiisoft/yii2-app-advanced advanced</code></pre>
                    <p>
                        After installation, read the
                        <a href="<?= Yii::getAlias('@web/extension/yiisoft/yii2-app-advanced/doc/guide') ?>">advanced template documentation</a>.
                    </p>
                </article>
            </div>
            <p>
                <?= Html::a('Read the installation guide', [
                    'guide/view',
                    'type' => 'guide',
                    'version' => '2.0',
                    'language' => 'en',
                    'section' => 'start-installation',
                    '#' => 'installing-via-composer',
                ]) ?>.
            </p>
        </section>

        <section class="download-subsection" id="install-from-archive">
            <header>
                <h3>Other installation methods</h3>
                <p>Download a template and extract it into a web-accessible directory.</p>
            </header>

            <div class="download-archives">
                <a href="https://github.com/yiisoft/yii2/releases/download/<?= $latestYii2 ?>/yii-basic-app-<?= $latestYii2 ?>.tgz">
                    <img src="<?= Yii::getAlias('@web/image/tgz.svg') ?>" alt="">
                    <span><strong>Basic application</strong><small>Yii 2 · tar.gz archive</small></span>
                </a>
                <a href="https://github.com/yiisoft/yii2/releases/download/<?= $latestYii2 ?>/yii-advanced-app-<?= $latestYii2 ?>.tgz">
                    <img src="<?= Yii::getAlias('@web/image/tgz.svg') ?>" alt="">
                    <span><strong>Advanced application</strong><small>Yii 2 · tar.gz archive</small></span>
                </a>
            </div>

            <p>
                After extracting the archive, follow the
                <?= Html::a('introduction in the Definitive Guide', [
                    'guide/view',
                    'type' => 'guide',
                    'version' => '2.0',
                    'language' => 'en',
                    'section' => 'start-installation',
                    '#' => 'verifying-installation',
                ]) ?>, or read the
                <a href="<?= Yii::getAlias('@web/extension/yiisoft/yii2-app-advanced/doc/guide') ?>">advanced template documentation</a>.
            </p>
        </section>

        <section class="download-subsection" id="verify-integrity">
            <header>
                <h3>Verify download integrity</h3>
                <p>
                    Copy the SHA256 hash from the <a href="https://github.com/yiisoft/yii2/releases">GitHub releases page</a>
                    and verify the downloaded archive.
                </p>
            </header>
            <div class="download-command-list">
                <article>
                    <h4>Linux and macOS</h4>
                    <pre><code class="hljs bash language-bash">echo "EXPECTED_HASH  yii-basic-app-<?= $latestYii2 ?>.tgz" | sha256sum -c</code></pre>
                </article>
                <article>
                    <h4>Windows</h4>
                    <pre><code class="hljs powershell language-powershell">$expectedHash = "EXPECTED_HASH"
$actualHash = (Get-FileHash yii-basic-app-<?= $latestYii2 ?>.tgz -Algorithm SHA256).Hash.ToLower()
if ($expectedHash -eq $actualHash) { "✓ Verification successful" } else { "✗ Verification failed" }</code></pre>
                </article>
            </div>
            <p>Replace <code>EXPECTED_HASH</code> with the actual hash. If verification fails, download the file again.</p>
        </section>

        <section class="download-subsection">
            <header>
                <h3>Upgrade from older versions</h3>
                <p>For a Composer installation of Yii 2.0.x, run this command in the project root:</p>
            </header>
            <pre><code class="hljs bash language-bash">php composer.phar update yiisoft/yii2 yiisoft/yii2-composer bower-asset/jquery.inputmask</code></pre>
            <p>
                Upgrades may require application changes, so always read the
                <?= Html::a('UPGRADE notes', "https://github.com/yiisoft/yii2/blob/$latestYii2/framework/UPGRADE.md") ?>.
                They also contain more detailed Composer upgrade instructions.
            </p>
            <p>
                For an archive installation, either migrate to Composer as described above or download the new release
                and replace the contents of your application's <code>vendor/</code> directory.
            </p>
            <p>
                Upgrading from Yii 1.1 is not trivial and requires rewriting a large part of the application. See
                <?= Html::a('Upgrading from Yii 1.1', [
                    'guide/view',
                    'type' => 'guide',
                    'version' => '2.0',
                    'language' => 'en',
                    'section' => 'intro-upgrade-from-v1',
                ]) ?> for details.
            </p>
        </section>

        <section class="download-subsection" id="offline-documentation">
            <header>
                <h3>Documentation</h3>
                <p>
                    Download the Guide and API documentation for offline use.
                </p>
            </header>
            <ul class="download-languages">
                <?php foreach (Yii::$app->params['guide.versions']['2.0'] as $locale => $language): ?>
                    <?php
                    $guide = new Guide('2.0', $locale);
                    $downloads = [];
                    foreach (['pdf' => 'PDF', 'tar.gz' => '.tar.gz', 'tar.bz2' => '.tar.bz2'] as $format => $label) {
                        if ($guide->getDownloadFile($format) !== false) {
                            $downloads[] = Html::a($label, [
                                'guide/download',
                                'version' => $guide->version,
                                'language' => $guide->language,
                                'format' => $format,
                            ]);
                        }
                    }
                    if ($downloads === []) {
                        continue;
                    }
                    ?>
                    <li>
                        <img src="<?= Yii::getAlias("@web/image/download/$locale.png") ?>" alt="">
                        <div><strong><?= Html::encode($language) ?></strong><span><?= implode('<span aria-hidden="true"> · </span>', $downloads) ?></span></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>
    <?php endif; ?>

    <?php if ($selectedVersion === '1.1'): ?>
    <div class="download-version" id="yii-1-1">
        <div class="download-version__header">
            <p>Latest release: <strong><?= Html::encode($latestYii1) ?></strong> · <?= Html::encode($versions['1.1'][$latestYii1]) ?></p>
        </div>

        <div class="download-status">
            <p>Yii 1.1 is currently in <?= Html::a('maintenance mode', ['news/view', 'id' => 90]) ?>.</p>
            <dl>
                <div><dt>Support and bug fixes</dt><dd>Until <?= Html::encode($versionInfo['1.1']['support-until']) ?></dd></div>
                <div><dt>Security fixes and PHP 7/8 compatibility</dt><dd>Until <?= Html::encode($versionInfo['1.1']['security-until']) ?></dd></div>
            </dl>
        </div>

        <nav class="download-reference-links" aria-label="Yii 1.1 downloads and release information">
            <?= Html::a('Changelog', "https://github.com/yiisoft/yii/blob/$latestYii1/CHANGELOG") ?>
            <?= Html::a('Upgrade instructions', "https://github.com/yiisoft/yii/blob/$latestYii1/UPGRADE") ?>
            <a href="https://github.com/yiisoft/yii/releases">All Yii 1 releases</a>
        </nav>

        <section class="download-subsection">
            <header>
                <h3>Install with Composer <span>Recommended</span></h3>
                <p>Install Yii into the project vendor directory.</p>
            </header>
            <div class="download-command-list">
                <article>
                    <h4>Yii 1.1 package</h4>
                    <pre><code class="hljs bash language-bash">composer require yiisoft/yii:^1.1</code></pre>
                </article>
            </div>
            <p>See the <a href="https://packagist.org/packages/yiisoft/yii">package details on Packagist</a>.</p>
        </section>

        <section class="download-subsection">
            <header>
                <h3>Other installation methods</h3>
                <p>Download the stable release or obtain the development source.</p>
            </header>
            <nav class="download-reference-links download-reference-links--section" aria-label="Yii 1.1 source downloads">
                <a href="<?= $versionInfo['1.1']['download-url'] ?>.tar.gz">Source tar.gz</a>
                <a href="<?= $versionInfo['1.1']['download-url'] ?>.zip">Source zip</a>
            </nav>
            <div class="download-command-list">
                <article>
                    <h4>Git</h4>
                    <pre><code class="hljs bash language-bash">git clone <?= Html::encode($versionInfo['1.1']['git-url']) ?> yii</code></pre>
                </article>
                <article>
                    <h4>SVN</h4>
                    <pre><code class="hljs bash language-bash">svn checkout <?= Html::encode($versionInfo['1.1']['svn-url']) ?> yii</code></pre>
                </article>
            </div>
        </section>

        <section class="download-subsection" id="archive">
            <header>
                <h3>Older releases</h3>
                <p>Yii 1.0 and other historical resources.</p>
            </header>
            <p><a href="https://github.com/yiisoft-contrib/museum">Browse the Yii museum</a>.</p>
        </section>

        <section class="download-subsection">
            <header>
                <h3>Documentation</h3>
                <p>Read online or download for offline use.</p>
            </header>
            <nav class="download-reference-links download-reference-links--section" aria-label="Yii 1.1 offline documentation">
                <a href="<?= str_replace('yii-', 'yii-docs-', $versionInfo['1.1']['download-url']) ?>.tar.gz">Offline docs tar.gz</a>
                <a href="<?= str_replace('yii-', 'yii-docs-', $versionInfo['1.1']['download-url']) ?>.zip">Offline docs zip</a>
            </nav>
            <ul class="download-guide-languages">
                <?php foreach (Yii::$app->params['guide.versions']['1.1'] as $locale => $language): ?>
                    <li><?= Html::a($language, ['guide/index', 'type' => 'guide', 'version' => '1.1', 'language' => $locale]) ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>
    <?php endif; ?>
</main>
