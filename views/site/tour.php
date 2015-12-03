<?php
use yii\helpers\Html;
use app\components\Highlight as HL;

/* @var $this yii\web\View */
$this->title = 'Getting started';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
            <p>
                Introductory notes here..
            </p>
            <ul class="timeline">
                <li>
                    <div class="timeline-image">
                        <img class="img-circle img-responsive" src="/image/tour/composer.png" alt="">
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4>Step One</h4>
                            <h4 class="subheading">Composer</h4>
                        </div>
                        <div class="timeline-body">
                            <p class="text-muted">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </p>
                        </div>
                    </div>
                    <div class="line"></div>
                </li>
                <li class="timeline-inverted">
                    <div class="timeline-image">
                        <img class="img-circle img-responsive" src="/image/tour/project-install.png" alt="">
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4>Step Two</h4>
                            <h4 class="subheading">Basic Project Template</h4>
                        </div>
                        <div class="timeline-body">
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
                    </div>
                    <div class="line"></div>
                </li>
                <li>
                    <div class="timeline-image">
                        <img class="img-circle img-responsive" src="/image/tour/start-app-installed.png" alt="">
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4>Step Three</h4>
                            <h4 class="subheading">Welcome Page</h4>
                        </div>
                        <div class="timeline-body">
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
                    </div>
                    <div class="line"></div>
                </li>
                <li class="timeline-inverted">
                    <div class="timeline-image">
                        <img class="img-circle img-responsive" src="/image/tour/migration.png" alt="">
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4>Step Four</h4>
                            <h4 class="subheading">Migrations</h4>
                        </div>
                        <div class="timeline-body">
                            <p class="text-muted">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </p>
<?php HL::begin(); ?>
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
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'object_type' => Schema::TYPE_STRING . ' NOT NULL',
            'object_id' => Schema::TYPE_STRING . ' NOT NULL',
            'text' => Schema::TYPE_TEXT . ' NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->addForeignKey('fk-comment-user_id-user-id', '{{%comment}}', 'user_id', '{{%user}}', 'id', 'RESTRICT');
        $this->createIndex('idx-comment-object_type-object_id', '{{%comment}}', ['object_type', 'object_id']);
    }

    public function down()
    {
        $this->dropTable('{{%comment}}');
    }
}
<?php HL::end(); ?>
                        </div>
                    </div>
                    <div class="line"></div>
                </li>
                <li>
                    <div class="timeline-image">
                        <img class="img-circle img-responsive" src="/image/tour/gii.png" alt="">
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4>Step Five</h4>
                            <h4 class="subheading">Gii - Yii Code Generator</h4>
                        </div>
                        <div class="timeline-body">
                            <p class="text-muted">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
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
</div>
