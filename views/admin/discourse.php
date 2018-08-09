<?php

use app\models\User;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $css string */
/** @var $js string */
/** @var $header string */

?>
<h1>Discourse Forum Integration</h1>

<p>The following CSS and HTML snippets are used to customize Discourse look and feel to be integrated tightly with the current website.</p>
<p>Copy and paste them to the "CSS" "&lt;/head&gt;" and "Header" sections of the discourse customization settings (Admin &raquo; Customize &raquo; Themes &raquo; Default &raquo; Custom CSS/HTML).</p>

<h2>CSS</h2>

<textarea style="width: 75%; min-height: 250px;"><?= Html::encode($css) ?></textarea>

<h2>&lt;/head&gt;</h2>

<?php

$jsHtml = "<script type=\"text/javascript\">$js</script>"

?>

<textarea style="width: 75%; min-height: 250px;"><?= Html::encode($jsHtml) ?></textarea>

<h2>Header</h2>

<textarea style="width: 75%; min-height: 250px;"><?= Html::encode($header) ?></textarea>
