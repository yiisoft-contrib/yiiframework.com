<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;
use app\components\Highlight as HL;

/* @var $this yii\web\View */
$this->title = 'Getting started';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container hidden-sm hidden-xs">
    <?php $this->beginBlock('intro', true) ?>
    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
            <p>
                Introductory notes here..
            </p>
        </div>
    </div>
    <?php $this->endBlock() ?>
    <div class="timeline">
        <div class="row">
            <div class="col-lg-8 col-xs-12">
                <div class="media timeline">
                    <?php $this->beginBlock('composer', true) ?>
                    <div class="media-body">
                        <h2 class="media-heading">1) Composer</h2>
                        <p>
                            If you do not already have <a href="https://getcomposer.org/">Composer</a> installed, you need to install it.
                        </p>
                        <p>
                            You also need to install the composer asset plugin globally, as it is used by the Yii composer project templates:
                        </p>
<?php HL::begin(['language' => 'bash']); ?>
composer global require "fxp/composer-asset-plugin:~1.1.0"
<?php HL::end(); ?>
                        <p>
                            For detailed instructions, click the following button to open up an informative modal window:
                        </p>

<?php HL::begin(['capture' => true]); ?>
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
<?php $hl_composer_install = HL::end(); ?>
<?php HL::begin(['capture' => true]); ?>
composer global require "fxp/composer-asset-plugin:~1.1.0"
<?php $hl_fxp_install = HL::end(); ?>


                            <p>
                                <?php Modal::begin(['header' => '<h2>Composer Installation</h2>',
                                    'id' => 'composer_install',
                                    'toggleButton' => ['label' => 'View detailed instructions.'],
                                    'closeButton' =>  ['label' => 'Close'],
                                    'size' => 'modal-lg',
                                    'clientOptions' => ['backdrop' => false],
                                    ]); ?>
                                <p>On Linux and Mac OS X, you'll run the following commands:</p>
                                <?= $hl_composer_install->captured; ?>

                                <p>On Windows, you'll download and run <a href="https://getcomposer.org/Composer-Setup.exe">Composer-Setup.exe</a>.</p>
                                <p>Please refer to the <a href="https://getcomposer.org/doc/">Composer Documentation</a> if you encounter any
                                problems or want to learn more about Composer usage.</p>
                                <p>If you had Composer already installed before, make sure you use an up to date version. You can update Composer
                                by running <code>composer self-update</code>.</p>
                                <p>
                                    With Composer installed, you need to install the composer asset plugin:
                                </p>
                                <?= $hl_fxp_install->captured; ?>
                                <p>
                                    That command installs the <a href="https://github.com/francoispluchino/composer-asset-plugin/">composer asset plugin</a>
                                    which allows managing bower and npm package dependencies through Composer. You only need to run this command
                                    once for all.
                                </p>
                                <hr>
                                <p class="text-muted">
                                    Taken from <a href="/doc/guide/2.0/en/start-installation#installing-via-composer">Guide - Installation - Installing via Composer</a>
                                </p>
                                <?php Modal::end(); ?>
                            </p>
                    </div>
                    <?php $this->endBlock() ?>
                    <div class="media-right media-top">
                        <?php $this->beginBlock('composer-image', true) ?>
                        <img class="media-object img-circle img-thumbnail" src="/image/tour/composer.png" alt="">
                        <?php $this->endBlock() ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-lg-offset-4">
                <div class="media timeline">
                    <div class="media-left media-top timeline-image">
                        <?php $this->beginBlock('template-image', true) ?>
                        <img class="img-circle media-object img-thumbnail" src="/image/tour/project-install.png" alt="">
                        <?php $this->endBlock() ?>
                    </div>
                    <?php $this->beginBlock('template', true) ?>
                    <div class="media-body">
                        <h2 class="media-heading">2) Basic project template</h2>
<?php HL::begin(['language' => 'bash']); ?>
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
<?php HL::end(); ?>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                    </div>
                    <?php $this->endBlock() ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="media timeline">
                    <?php $this->beginBlock('welcome', true) ?>
                    <div class="media-body">
                        <h2 class="media-heading">3) Welcome page</h2>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                    </div>
                    <?php $this->endBlock() ?>
                    <div class="media-right media-top timeline-image">
                        <?php $this->beginBlock('welcome-image', true) ?>
                        <img class="img-circle media-object img-thumbnail" src="/image/tour/start-app-installed.png" alt="">
                        <?php $this->endBlock() ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-lg-offset-4">
                <div class="media timeline">
                    <div class="media-left media-top timeline-image">
                        <?php $this->beginBlock('migration-image', true) ?>
                        <img class="img-circle media-object img-thumbnail" src="/image/tour/migration.png" alt="">
                        <?php $this->endBlock() ?>
                    </div>
                    <?php $this->beginBlock('migration', true) ?>
                    <div class="media-body">
                        <h2 class="media-heading">4) Migrations</h2>
<?php HL::begin(['language' => 'bash']); ?>
./yii migrate/create create_comment_table
<?php HL::end(); ?>
<?php HL::begin(['capture' => true]); ?>
use yii\db\Schema;
use yii\db\Migration;

class m150416_155923_create_comment_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'object_type' => $this->string(64)->notNull(),
            'object_id' => $this->string(64)->notNull(),
            'text' => $this->text()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-comment-object_type-object_id', '{{%comment}}', ['object_type', 'object_id']);
    }

    public function down()
    {
        $this->dropTable('{{%comment}}');
    }
}
<?php $hl = HL::end(); ?>
                        <?php Modal::begin(['header' => '<h2>Comment Migration</h2>',
                            'id' => 'migration',
                            'toggleButton' => ['label' => 'View generated migration'],
                            'closeButton' =>  ['label' => 'Close'],
                            'size' => 'modal-lg',
                            'clientOptions' => ['backdrop' => false],
                            ]); ?>
                            <?= $hl->captured; ?>

                        <?php Modal::end(); ?>
                    </div>
                    <?php $this->endBlock() ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="media timeline">
                    <?php $this->beginBlock('gii', true) ?>
                    <div class="media-body">
                        <h2 class="media-heading">5) Gii - Yii Code Generator</h2>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <p class="text-muted">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                    </div>
                    <?php $this->endBlock() ?>
                    <div class="media-right media-top timeline-image">
                        <?php $this->beginBlock('gii-image', true) ?>
                        <img class="img-circle media-object img-thumbnail" src="/image/tour/gii.png" alt="">
                        <?php $this->endBlock() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->beginBlock('outro', true) ?>
    <div class="row">
        <div class="col-lg-12">
            <h2>What to do next?</h2>
            <ol>
                <li><?= Html::a('Read "Getting started" guide', ['guide/view', 'section' => 'start-installation', 'language' => 'en', 'version' => key(Yii::$app->params['guide.versions']), 'type' => 'guide']) ?>.</li>
                <li>To learn more, <?= Html::a('read the Guide', ['guide/entry']) ?>.</li>
                <li>Get to know the <?= Html::a('API docs', ['api/index', 'version' => reset(Yii::$app->params['api.versions'])])?>. you'll use them every day (at least for a while). You can view Yii source code directly in the API docs. The API search box is invaluable.</li>
            </ol>

            <p>Check this <a href="http://www.yiiframework.com/wiki/268/how-to-learn-yii">great wiki article written by Yii community</a>.</p>
        </div>
    </div>
    <?php $this->endBlock() ?>
</div>
<!-- Separate section for use on small devices - that's what all the blocks were for -->
<div class="container-fluid visible-sm visible-xs">
    <?= $this->blocks['intro']; ?>
    <div class="row mini-timeline">
        <div class="col-sm-12">
            <?= $this->blocks['composer-image']; ?>
            <?= $this->blocks['composer']; ?>
        </div>
        <div class="col-sm-12">
            <?= $this->blocks['template-image']; ?>
            <?= $this->blocks['template']; ?>
        </div>
        <div class="col-sm-12">
            <?= $this->blocks['welcome-image']; ?>
            <?= $this->blocks['welcome']; ?>
        </div>
        <div class="col-sm-12">
            <?= $this->blocks['migration-image']; ?>
            <?= $this->blocks['migration']; ?>
        </div>
        <div class="col-sm-12">
            <?= $this->blocks['gii-image']; ?>
            <?= $this->blocks['gii']; ?>
        </div>
    </div>
    <?= $this->blocks['outro']; ?>
</div>
