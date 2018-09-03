<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $css string */
/** @var $js string */
/** @var $header string */

?>
<h1>Discourse Forum Integration</h1>

<div class="row">

    <div class="col-md-6">

        <h2>Style Integration</h2>


        <p>The following CSS and HTML snippets are used to customize Discourse look and feel to be integrated tightly with the current website.</p>
        <p>Copy and paste them to the "CSS" "&lt;/head&gt;" and "Header" sections of the discourse customization settings (Admin &raquo; Customize &raquo; Themes &raquo; Default &raquo; Custom CSS/HTML).</p>

        <h3>CSS</h3>

        <textarea style="width: 75%; min-height: 250px;"><?= Html::encode($css) ?></textarea>

        <h3>&lt;/head&gt;</h3>

        <?php

        $jsHtml = "<script type=\"text/javascript\">$js</script>"

        ?>

        <textarea style="width: 75%; min-height: 250px;"><?= Html::encode($jsHtml) ?></textarea>

        <h3>Header</h3>

        <textarea style="width: 75%; min-height: 250px;"><?= Html::encode($header) ?></textarea>

    </div>
    <div class="col-md-6">

        <h2>Discourse Settings</h2>

        <p>Make sure the following settings are set in Discourse Admin:</p>

        <h3>Basic Setup</h3>
        <table>
            <tr><th><code>top_menu</code></th><td>Categories, Latest, New, Unread, Top</td></tr>
            <tr><th><code>desktop_category_page_style</code></th><td>Categories with Featured Topics</td></tr>
        </table>
        <h3>Login</h3>
        <table>
            <tr><th><code>invite_only</code></th><td>Yes</td></tr>
            <tr><th><code>login_required</code></th><td>No</td></tr>
            <tr><th><code>enable_sso</code></th><td>Yes</td></tr>
            <tr><th><code>sso_url</code></th><td><?= Url::to(['auth/discourse-sso'], 'https') ?></td></tr>
            <tr><th><code>sso_secret</code></th><td>(same value as configured in site config <code>params.local.php</code>)</td></tr>
        </table>

        <h3>Users</h3>
        <table>
            <tr><th><code>hide_user_profiles_from_public</code></th><td>Yes</td></tr>
        </table>

        <h3>Email</h3>
        <table>
            <tr><th><code>reply_by_email_enabled</code></th><td>Yes</td></tr>
            <tr><th><code>reply_by_email_address</code></th><td>discourse-replies+%{reply_key}@yiiframework.com</td></tr>
            <tr><th><code>pop3_polling_enabled</code></th><td>Yes</td></tr>
            <tr><th><code>pop3_polling_period_mins</code></th><td>2</td></tr>
            <tr><th><code>pop3_polling_host</code></th><td>pop.cebe.cc</td></tr>
            <tr><th><code>pop3_polling_username</code></th><td>...</td></tr>
            <tr><th><code>pop3_polling_password</code></th><td>...</td></tr>
        </table>

        <h3>Security</h3>
        <table>
            <tr><th><code>force_https</code></th><td>Yes</td></tr>
            <tr><th><code>allow_index_in_robots_txt</code></th><td>Yes</td></tr>
        </table>


        <h2>Badges</h2>

        <p>Create the following Badges in Discourse:</p>

        <ul>
            <li>
                <dl>
                    <dt>Name:</dt> <dd>Greenhorn</dd>
                    <dt>Icon:</dt> <dd>...</dd>
                    <dt>Badge Type:</dt> <dd>Bronze</dd>
                    <dt>Group:</dt> <dd>Posting</dd>
                    <dt>Description:</dt> <dd>25 Active Forum Posts</dd>
                </dl>

            </li>
        -


        </ul>

    </div>
</div>