<?php

use yii\helpers\Url;

echo '<?xml version="1.0"?>';
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
    <ShortName>Yii Site Search</ShortName>
    <Description>Search the whole Yii site.</Description>
    <Image width="16" height="16" type="image/x-icon"><?= Url::to('@web/favico/favicon.ico', true) ?></Image>
    <Url type="text/html" method="get"
         template="<?= Url::toRoute(['search/global', 'q' => ''], true) ?>{searchTerms}"/>
    <Url type="application/x-suggestions+json"
         template="<?= Url::toRoute(['search/opensearch-suggest', 'q' => ''], true) ?>{searchTerms}"/>
</OpenSearchDescription>
