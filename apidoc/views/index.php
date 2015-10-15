<?php

use yii\apidoc\models\ClassDoc;
use yii\apidoc\models\InterfaceDoc;
use yii\apidoc\models\TraitDoc;

/* @var $types ClassDoc[]|InterfaceDoc[]|TraitDoc[] */
/* @var $this yii\web\View */
/* @var $renderer \yii\apidoc\templates\html\ApiRenderer */

$renderer = $this->context;

if (isset($readme)) {
    echo \yii\apidoc\helpers\ApiMarkdown::process($readme);
}

?><h1>Yii Framework <?= $this->context->version ?> API Documentation</h1>

<p>
    This is the Yii Framework API Documentation. Here you will find detailed information about all classes
    provided by the Framework. Below you find a list of the existing classes, interfaces, and traits, ordered by their
    fully qualified name (including the namespace). Each of them has a dedicated page which contains a description about the
    purpose of the class, a list of the available methods, properties and constants, and detailed description
    on how to use each of them.
</p>
<p>
    On this page you find all the classes included in version <?= $this->context->version ?>
    of the framework. You can use the dropdown menu on the top right to switch between versions.
</p>
<!-- YII_DOWNLOAD_OPTIONS -->

<table class="summaryTable docIndex table table-bordered table-striped table-hover">
    <colgroup>
        <col class="col-package" />
        <col class="col-class" />
        <col class="col-description" />
    </colgroup>
    <tr>
        <th>Class</th>
        <th>Description</th>
    </tr>
<?php
ksort($types);
foreach ($types as $i => $class):
?>
    <tr>
        <td><?= $renderer->createTypeLink($class, $class, $class->name) ?></td>
        <td><?= \yii\apidoc\helpers\ApiMarkdown::process($class->shortDescription, $class, true) ?></td>
    </tr>
<?php endforeach; ?>
</table>
