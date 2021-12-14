<?php

namespace app\apidoc;

use Yii;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class ExtensionApiRenderer extends ApiRenderer
{
    public $layout = '@app/apidoc/layouts/api.php';
    public $indexView = '@app/apidoc/views/extension-index.php';

    public $version;
    public $extension;

    /**
     * @inheritdoc
     */
    public function render($context, $targetDir)
    {
        $types = array_merge($context->classes, $context->interfaces, $context->traits);

        // render view files
        parent::render($context, $targetDir);

        // create index.html
        $indexFileContent = $this->renderWithLayout($this->indexView, [
            'docContext' => $context,
            'types' => $types,
            'readme' => null,
        ]);
        file_put_contents($targetDir . '/index.html', $indexFileContent);

        // create file with page titles
        $titles = [];
        foreach($types as $type) {
            $titles[$this->generateFileName($type->name)] = StringHelper::basename($type->name) . ", {$type->name}";
        }
        file_put_contents($targetDir . '/titles.php', '<?php return ' . VarDumper::export($titles) . ';');
    }

    public function getSourceUrl($type, $line = null)
    {
        if (is_string($type)) {
            $type = $this->apiContext->getType($type);
        }

        preg_match('/\/(\d+\.\d+(?:\.\d+)?)\/(.+)/', $type->sourceFile, $matches);

        $baseUrl = rtrim($this->extension->github_url, '/');
        $url = "$baseUrl/blob/master/$matches[2]";

        return !$line ? $url : "$url#L$line";
    }

	public function generateApiUrl($typeName)
	{
        $type = $this->apiContext->getType($typeName);

        if ($type !== null) {
            return Yii::$app->params['baseUrl'] . "/extension/{$this->extension->name}/doc/api/$this->version/" . substr($this->generateFileName($typeName), 0, -5);
        } else {
            // cross linking to framework api
       		return Yii::$app->params['api.baseUrl'] . "/$this->version/" . substr($this->generateFileName($typeName), 0, -5);
        }
	}
}
