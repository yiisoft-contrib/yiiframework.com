<?php

namespace app\apidoc;

use Yii;
use yii\apidoc\helpers\ApiIndexer;
use yii\base\ErrorHandler;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class ApiRenderer extends \yii\apidoc\templates\html\ApiRenderer
{
    use RendererTrait;

    public $layout = '@app/apidoc/layouts/api.php';
    public $indexView = '@app/apidoc/views/index.php';

    public $version;
    public ?string $currentPackageName = null;

    /**
     * @inheritdoc
     */
    public function render($context, $targetDir)
    {
        $types = array_merge($context->classes, $context->interfaces, $context->traits);

        parent::render($context, $targetDir);

        if ($this->version === '3.0') {
            $indexFileContent = $this->renderWithLayout($this->indexView, [
                'docContext' => $context,
                'types' => $this->filterTypes($types, 'Yiisoft'),
                'readme' => null,
            ]);
        } else {
            $extTypes = [];
            foreach ($this->extensions as $k => $ext) {
                $extType = $this->filterTypes($types, $ext);
                if (empty($extType)) {
                    unset($this->extensions[$k]);
                    continue;
                }
                $extTypes[$ext] = $extType;
            }

            if ($this->controller !== null) {
                $this->controller->stdout('generating extension index files...');
            }

            foreach ($extTypes as $ext => $extType) {
                $readme = @file_get_contents("https://raw.github.com/yiisoft/yii2-$ext/master/README.md");
                $indexFileContent = $this->renderWithLayout($this->indexView, [
                    'docContext' => $context,
                    'types' => $extType,
                    'readme' => $readme ?: null,
                ]);
                file_put_contents($targetDir . "/ext-{$ext}-index.html", $indexFileContent);
            }
            if ($this->controller !== null) {
                $this->controller->stdout("done.\n", Console::FG_GREEN);
            }

            $indexFileContent = $this->renderWithLayout($this->indexView, [
                'docContext' => $context,
                'types' => $this->filterTypes($types, 'yii'),
                'readme' => null,
            ]);
        }

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

        $baseUrl = 'https://github.com/yiisoft/yii2/blob/master';
        switch ($this->getTypeCategory($type)) {
            case 'yii':
                if ($type->name === 'Yii') {
                    $url = '/framework/Yii.php';
                } elseif ($type->name === 'YiiRequirementChecker') {
                    $url = '/framework/requirements/YiiRequirementChecker.php';
                } else {
                    $url = '/framework/' . str_replace('\\', '/', substr($type->name, 4)) . '.php';
                }
                break;
            case 'app':
                return null;
            default:
                $url = '/extensions/' . str_replace('\\', '/', substr($type->name, 4)) . '.php';
                break;
        }

        if ($line === null) {
            return $baseUrl . $url;
        } else {
            return $baseUrl . $url . '#L' . $line;
        }
    }

    public function generateGuideUrl($file)
    {
        $hash = '';
        if (($pos = strrpos($file, '#')) !== false) {
            $hash = substr($file, $pos);
            $file = substr($file, 0, $pos);
        }
        return rtrim($this->guideUrl, '/') . '/' . $this->guidePrefix . basename($file, '.md') . $hash;
    }

	public function generateApiUrl($typeName)
	{
		return Yii::$app->params['api.baseUrl']
            . "/{$this->version}/"
            . ($this->currentPackageName !== null ? "{$this->currentPackageName}/" : '')
            . substr($this->generateFileName($typeName), 0, -5);
	}
}
