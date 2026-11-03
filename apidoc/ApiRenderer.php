<?php

namespace app\apidoc;

use RuntimeException;
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

        if ($this->version === '2.0') {
            $cacheDir = $targetDir . '/cache';
            FileHelper::createDirectory($cacheDir);
            $cacheFile = $cacheDir . '/markdown.data';
            $data = serialize(MarkdownContext::create($context));
            $temporaryFile = @tempnam($cacheDir, 'markdown-');
            if ($temporaryFile === false) {
                throw new RuntimeException("Cannot create temporary Markdown API cache in $cacheDir.");
            }

            try {
                // tempnam creates owner-only files; the published cache must be readable by PHP-FPM.
                if (dirname($temporaryFile) !== realpath($cacheDir)
                    || @file_put_contents($temporaryFile, $data) !== strlen($data)
                    || !@chmod($temporaryFile, 0644)
                    || !@rename($temporaryFile, $cacheFile)
                ) {
                    throw new RuntimeException("Cannot publish Markdown API cache: $cacheFile.");
                }
            } finally {
                if (is_file($temporaryFile)) {
                    unlink($temporaryFile);
                }
            }
        }
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

    /**
     * @inheritdoc
     */
    public function renderMethodSignature($method, $context = null)
    {
        return str_replace(
            ['<strong>', '</strong>', '<b>', '</b>'],
            ['<span>', '</span>', '<span>', '</span>'],
            parent::renderMethodSignature($method, $context),
        );
    }

	public function generateApiUrl($typeName)
	{
		return Yii::$app->params['api.baseUrl']
            . "/{$this->version}/"
            . ($this->currentPackageName !== null ? "{$this->currentPackageName}/" : '')
            . substr($this->generateFileName($typeName), 0, -5);
	}
}
