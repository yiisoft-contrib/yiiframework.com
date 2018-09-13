<?php

namespace app\commands;

use app\apidoc\ExtensionApiRenderer;
use app\models\Extension;
use Yii;
use yii\apidoc\models\ClassDoc;
use yii\apidoc\models\ConstDoc;
use yii\apidoc\models\EventDoc;
use yii\apidoc\models\InterfaceDoc;
use yii\apidoc\models\MethodDoc;
use yii\apidoc\models\PropertyDoc;
use yii\apidoc\models\TraitDoc;
use yii\base\ErrorHandler;
use yii\console\ExitCode;
use yii\helpers\Console;
use app\apidoc\ApiRenderer;
use yii\helpers\FileHelper;
use yii\helpers\Json;

/**
 * Generates API documentation for Yii.
 */
class ApiController extends \yii\apidoc\commands\ApiController
{
    public $defaultAction = 'generate';
    public $guidePrefix = '';
    protected $version = '2.0';

    /**
     * Generates the API documentation for the specified version of Yii.
     * @param string $version version number, such as 1.1, 2.0
     * @return integer exit status
     */
    public function actionGenerate($version)
    {
        $versions = Yii::$app->params['versions']['api'];
        if (!in_array($version, $versions)) {
            $this->stderr("Unknown version $version. Valid versions are " . implode(', ', $versions) . "\n\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $this->version = $version;

        $targetPath = Yii::getAlias('@app/data');
        $sourcePath = Yii::getAlias('@app/data');

        if ($version[0] === '2') {
            $source = [
                "$sourcePath/yii-$version/framework",
//                "$sourcePath/yii-$version/extensions",
            ];
            $target = "$targetPath/api-$version";
            $this->guide = Yii::$app->params['guide.baseUrl'] . "/{$this->version}/en";

            $this->stdout("Start generating API $version...\n");
            $this->template = 'bootstrap';
            $this->actionIndex($source, $target);

            $this->stdout("Start generating API $version JSON Info...\n");
            $this->template = 'json';
            $this->actionIndex($source, $target);
            $this->splitJson($target);

            $this->stdout("Finished API $version.\n\n", Console::FG_GREEN);
        } elseif ($version[0] === '1') {
            $source = [
                "$sourcePath/yii-$version/framework",
            ];
            $target = "$targetPath/api-$version";
            $cmd = Yii::getAlias("@app/data/yii-$version/build/build");

            if ($version === '1.1' && !is_file($composerYii1 = Yii::getAlias('@app/data/yii-1.1/vendor/autoload.php'))) {
                $this->stdout("WARNING: Composer dependencies of Yii 1.1 are not installed, api generation may fail.\n", Console::BOLD, Console::FG_YELLOW);
            }

            $this->stdout("Start generating API $version...\n");
            FileHelper::createDirectory($target);
            passthru("php $cmd api $target online");

            foreach(FileHelper::findFiles($target, ['only' => ['*.html']]) as $file) {
                file_put_contents($file, preg_replace(
                    '~href="/doc/api/([\w\#\-\.]*)"~i',
                    'href="' . Yii::$app->params['api.baseUrl'] . '/' . $version . '/\1"',
                    file_get_contents($file))
                );
            }
            file_put_contents("$target/api/index.html", str_replace(
                '<h1>Class Reference</h1>',
                <<<HTML
<h1>Yii Framework $version API Documentation</h1>
<p>
    This is the Yii Framework API Documentation. Here you will find detailed information about all classes
    provided by the Framework. Below you find a list of the existing classes, interfaces, and traits, ordered by their
    fully qualified name (including the namespace). Each of them has a dedicated page which contains a description about the
    purpose of the class, a list of the available methods, properties and constants, and detailed description
    on how to use each of them.
</p>
<p>
    On this page you find all the classes included in version $version
    of the framework. You can use the dropdown menu on the top right to switch between versions.
</p>
<p>
    <strong>You can search API documentation using the search form on the top.</strong>
	You can search for class names and also method and property names, e.g. <code>ActiveRecord.save()</code> or just <code>.save()</code> or <code>::save()</code>.
</p>
HTML
                , file_get_contents("$target/api/index.html")));

            $this->stdout("Finished API $version.\n\n", Console::FG_GREEN);
        }

        return ExitCode::OK;
    }

    public function actionExtension($extensionName)
    {
        $extension = Extension::find()->where(['name' => $extensionName])->active()->one();

        if ($extension === null) {
            $this->stderr("Unknown extension $extensionName.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $this->_extension = $extension;

        $targetPath = Yii::getAlias("@app/data/extensions/$extensionName");

        $versions = Json::decode($extension->version_references);
        $apiVersions = [];
        foreach($versions as $version => $gitRef) {

            $this->version = $version;

            try {
                $sourcePath = Yii::getAlias("@app/data/extensions/$extensionName/$version");
                if (!$extension->cloneGitRepo($sourcePath, $gitRef)) {
                    $this->stderr("Failed to clone git repo for extension {$extension->name}.\n", Console::FG_RED);
                    return ExitCode::UNSPECIFIED_ERROR;
                }

                if (is_dir("$sourcePath/src")) {
                    $source = ["$sourcePath/src"];
                } else {
                    $source = [$sourcePath];
                }
                $target = "$targetPath/api-$version";
                $this->guide = "/extension/$extensionName/doc/guide/{$this->version}/en";

                $this->stdout("Start generating $extensionName API $version...\n");
                $this->template = 'extension';
                $this->actionIndex($source, $target);

                $this->stdout("Start generating $extensionName API $version JSON Info...\n");
                $this->template = 'json';
                $this->actionIndex($source, $target);
                $this->splitJson($target);

                $this->stdout("Finished $extensionName API $version.\n\n", Console::FG_GREEN);
                $apiVersions[] = $version;
            } catch (\Throwable $e) {
                $this->stderr("Failed to generate $extensionName API $version.\n\n", Console::FG_RED);
                $this->stderr((string) $e, Console::FG_RED);
                Yii::error($e);
            }
        }
        if (!empty($apiVersions) && is_dir($targetPath)) {
            file_put_contents("$targetPath/api.json", Json::encode($apiVersions));
        }

        return ExitCode::OK;
    }

    private $_extension;

    protected function findRenderer($template)
    {
        if ($template === 'extension') {
            return new ExtensionApiRenderer([
                'version' => $this->version,
                'extension' => $this->_extension,
            ]);
        }
        if ($template === 'json') {
            return new \yii\apidoc\templates\json\ApiRenderer();
        }
        return new ApiRenderer([
            'version' => $this->version,
        ]);
    }

    public function splitJson($target)
    {
        $json = file_get_contents("$target/types.json");
        FileHelper::createDirectory("$target/json");

        $types = Json::decode($json);

        // write types file:
        file_put_contents("$target/json/typeNames.json", Json::encode(
            array_values(array_map(function($type) {
                return [
                    'type' => $type['type'],
                    'name' => $type['name'],
                    'description' => isset($type['shortDescription']) ? $type['shortDescription'] : '',
                ];
            }, $types))
        ));

        // write class-member file:
        $members = [];
        foreach($types as $type) {

            $methods = isset($type['methods']) ? array_map(function($m) { $m['type'] = 'method'; return $m; }, $type['methods']) : [];
            $properties = isset($type['properties']) ? array_map(function($m) { $m['type'] = 'property'; return $m; }, $type['properties']) : [];
            $constants = isset($type['constants']) ? array_map(function($m) { $m['type'] = 'constant'; return $m; }, $type['constants']) : [];
            $events = isset($type['events']) ? array_map(function($m) { $m['type'] = 'event'; return $m; }, $type['events']) : [];

            foreach(array_merge($methods, $properties, $constants, $events) as $method) {

                if ($method['definedBy'] != $type['name']) {
                    continue;
                }

                $k = $method['type'].$method['name'];
                if (!isset($members[$k])) {
                    $members[$k] = [
                        'type' => $method['type'],
                        'name' => $method['name'],
                        'implemented' => [],
                    ];
                }
                $members[$k]['implemented'][] = $type['name'];
            }
        }
        file_put_contents("$target/json/typeMembers.json", Json::encode(array_values($members)));
    }

    public function writeJsonFiles1x($target, $types)
    {
        FileHelper::createDirectory("$target/json");

        // write types file:
        file_put_contents("$target/json/typeNames.json", Json::encode(
            array_values(array_map(function($type) {
                $classType = null;
                if ($type instanceof ClassDoc) {
                    $classType = 'class';
                } elseif ($type instanceof InterfaceDoc) {
                    $classType = 'interface';
                } elseif ($type instanceof TraitDoc) {
                    $classType = 'trait';
                }
                return [
                    'name' => $type->name,
                    'description' => $type->shortDescription,
                    'type' => $classType,
                ];
            }, $types))
        ));

        // write class-member file:
        $members = [];
        foreach($types as $type) {

            $methods = isset($type->methods) ? $type->methods : [];
            $properties = isset($type->properties) ? $type->properties : [];
            $constants = isset($type->constants) ? $type->constants : [];
            $events = isset($type->events) ? $type->events : [];

            foreach(array_merge($methods, $properties, $constants, $events) as $method) {

                if ($method->definedBy != $type->name) {
                    continue;
                }

                if ($method instanceof MethodDoc) {
                    $mtype = 'method';
                }
                if ($method instanceof PropertyDoc) {
                    $mtype = 'property';
                }
                if ($method instanceof ConstDoc) {
                    $mtype = 'const';
                }
                if ($method instanceof EventDoc) {
                    $mtype = 'event';
                }

                $k = $mtype . $method->name;
                if (!isset($members[$k])) {
                    $members[$k] = [
                        'type' => $mtype,
                        'name' => $method->name,
                        'implemented' => [],
                    ];
                }
                $members[$k]['implemented'][] = $type->name;
            }
        }
        file_put_contents("$target/json/typeMembers.json", Json::encode(array_values($members)));
    }
}
