<?php

namespace app\controllers;

use app\apidoc\ApiRenderer;
use app\components\object\ClassType;
use app\models\Doc;
use app\models\Extension;
use app\models\search\SearchActiveRecord;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\HttpCache;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnsupportedMediaTypeHttpException;

/**
 * ApiController provides the framework API documentation
 *
 * API documentation is provided in HTML format for all versions of Yii.
 *
 * Version 2.0 provides also json
 */
class ApiController extends BaseController
{
    public $sectionTitle = 'API Documentation for Yii';
    public $searchScope = SearchActiveRecord::SEARCH_API;


    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'only' => ['view', 'index', 'entry', 'class-members'],
                'formats' => [
                    'text/html' => Response::FORMAT_HTML,
                    'application/xhtml+xml' => Response::FORMAT_HTML,
                    'application/json' => Response::FORMAT_JSON,
                    'json' => Response::FORMAT_JSON,
                ],
            ],
            [
                'class' => HttpCache::class,
                'only' => ['class-members'],
                'cacheControlHeader' => 'public, max-age=86400',
                'lastModified' => function() {
                    return strtotime('yesterday 00:00:00');
                },
            ],
        ];
    }


    public function actionIndex($version)
    {
        return $this->actionView($version, 'index');
    }

    public function actionExtensionIndex($vendorName, $name, $version)
    {
        return $this->actionExtensionView($vendorName, $name, $version, 'index');
    }

    public function actionView($version, $section)
    {
        $versions = Yii::$app->params['versions']['api'];
        // TODO: remove next line
        unset($versions[0]);
        if (!in_array($version, $versions)) {
            return $this->api404($section, $version);
        }

        if (!preg_match('/^[\d.]+$/', $version)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }

        if ($version[0] !== '3' && !preg_match('/^[\w\-]+$/', $section)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }

        switch (Yii::$app->response->format) {
            case Response::FORMAT_HTML:

                $file = null;
                $title = '';
                $packages = [];
                if ($version[0] === '1') {
                    $this->sectionTitle = "API Documentation for Yii $version";
                    $file = Yii::getAlias("@app/data/api-$version/api/$section.html");
                    if (!is_file($file)) {
                        // if class is not found as a file, try to find name in a different case and redirect. Throws 404 otherwise.
                        return $this->actionRedirect($section, '1.1');
                    }
                    $packages = unserialize(file_get_contents(Yii::getAlias("@app/data/api-$version/api/packages.txt")));
                    $view = 'view1x';
                    $title = $section !== 'index' ? $section : '';
                } elseif ($version[0] === '2') {
                    $this->sectionTitle = "API Documentation for Yii $version";
                    $file = Yii::getAlias("@app/data/api-$version/$section.html");
                    $view = 'view2x';
                    $titles = require Yii::getAlias("@app/data/api-$version/titles.php");
                    $titleKey = $section . '.html';
                    if (isset($titles[$titleKey])) {
                        $title = $titles[$titleKey];
                    }
                } elseif ($version[0] === '3') {
                    $this->sectionTitle = ["API Documentation for Yii $version" => ['index', 'version' => $version]];

                    $view = 'view3x';
                    $sectionPaths = explode('/', $section);
                    $mainSection = $sectionPaths[0];
                    $subSection = $sectionPaths[1] ?? null;

                    if ($mainSection !== 'index') {
                        $this->sectionTitle["Package $mainSection"] = [
                            'view',
                            'version' => $version,
                            'section' => $mainSection,
                        ];

                        $subSection ??= 'index';
                        $file = Yii::getAlias("@app/data/api-$version/$mainSection/$subSection.html");
                        $titles = require Yii::getAlias("@app/data/api-$version/$mainSection/titles.php");
                        $titleKey = $subSection . '.html';
                        if (isset($titles[$titleKey])) {
                            $title = $titles[$titleKey];
                        }
                    }
                }

                if (($version[0] !== '3' || $section !== 'index') && !is_file($file)) {
                    return $this->api404($section, $version);
                }

                if ($section === 'index') {
                    $urlParams = ['version' => $version];
                    $docUrl = Url::to(array_merge(['api/index'], $urlParams));
                } else {
                    $urlParams = ['version' => $version, 'section' => $section];
                    $docUrl = Url::to(array_merge(['api/view'], $urlParams));
                }
                $doc = Doc::getObject(ClassType::API, implode('/', $urlParams), $docUrl, $title);

                return $this->render($view, [
                    'content' => $file ? file_get_contents($file) : '',
                    'section' => $section,
                    'versions' => $versions,
                    'version' => $version,
                    'title' => $title,
                    'packages' => $packages,
                    'doc' => $doc,
                    'extension' => null,
                ]);

                break;
            case Response::FORMAT_JSON:
                if ($version[0] !== '3' && $section !== 'index') {
                    throw new NotFoundHttpException();
                }

                $result = [];

                if ($version[0] === '3') {
                    $typeNamesPath = Yii::getAlias("@app/data/api-$version/$section/json/typeNames.json");
                    $result['package'] = $section;
                } else {
                    $typeNamesPath = Yii::getAlias("@app/data/api-$version/json/typeNames.json");
                }

                $apiRenderer = new ApiRenderer([
                    'version' => $version,
                ]);

                $classes = Json::decode(file_get_contents($typeNamesPath));
                foreach ($classes as $i => $class) {
                    $classes[$i]['url'] = Yii::$app->request->hostInfo . $apiRenderer->generateApiUrl($class['name']);
                }

                return array_merge($result, [
                    'classes' => $classes,
                    'version' => $version,
                    'count' => count($classes),
                ]);
        }
        throw new UnsupportedMediaTypeHttpException;
    }

    public function actionExtensionView($vendorName, $name, $version, $section)
    {
        if (($extension = Extension::find()->where(['name' => "$vendorName/$name"])->active()->one()) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if (!preg_match('/^[\w\-]+$/', $section)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }
        if (!preg_match('/^[\d.]+$/', $version)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }

        $this->sectionTitle = [
            $extension->name => $extension->getUrl(),
            'API Documentation' => $extension->getUrl('doc', ['type' => 'api']),
        ];

        if (!$extension->hasApiDoc($version)) {
            return $this->extension404($extension, $version, $section);
        }

        switch (Yii::$app->response->format) {
            case Response::FORMAT_HTML:

                $title = '';
                $file = Yii::getAlias("@app/data/extensions/{$extension->name}/api-$version/$section.html");
                $titles = require Yii::getAlias("@app/data/extensions/{$extension->name}/api-$version/titles.php");
                $titleKey = $section . '.html';
                if (isset($titles[$titleKey])) {
                    $title = $titles[$titleKey];
                }
                if (!is_file($file)) {
                    return $this->extension404($extension, $version, $section);
                }

                return $this->render('view2x', [
                    'content' => file_get_contents($file),
                    'section' => $section,
                    'versions' => $extension->getApiVersions(),
                    'version' => $version,
                    'title' => $title,
                    'extension' => $extension,
                ]);

                break;
        }
        throw new UnsupportedMediaTypeHttpException;
    }

    private function api404($section, $version)
    {
        // try to find the file in another version
        $alternativeVersions = Yii::$app->params['versions']['api'];
        rsort($alternativeVersions, SORT_NATURAL);
        $alternatives = [];
        $alternativeIndices = [];
        foreach ($alternativeVersions as $altVersion) {
            if ($section !== 'index') {
                if ($altVersion[0] === '1') {
                    $file = Yii::getAlias("@app/data/api-$altVersion/api/$section.html");
                } else {
                    $file = Yii::getAlias("@app/data/api-$altVersion/$section.html");
                }
                if (is_file($file)) {
                    $alternatives[$altVersion] = ['api/view', 'version' => $altVersion, 'section' => $section];
                }
            }
            $alternativeIndices[$altVersion] = ['api/index', 'version' => $altVersion];
        }

        // if class is not found, show a better 404
        Yii::$app->response->statusCode = 404;
        return $this->render('error-404', [
            'alternatives' => $alternatives,
            'alternativeVersions' => $alternativeIndices,
        ]);

    }

    /**
     * @param Extension $extension
     * @param $version
     * @param $section
     * @return string
     */
    private function extension404($extension, $version, $section)
    {
        list($extensionVendor, $extensionName) = explode('/', $extension->name, 2);
        // try to find the file in another version
        $alternativeVersions = $extension->getApiVersions();
        rsort($alternativeVersions, SORT_NATURAL);
        $alternatives = [];
        $alternativeIndices = [];
        foreach ($alternativeVersions as $altVersion) {
            if ($section !== 'index') {
                $file = Yii::getAlias("@app/data/extensions/{$extension->name}/api-$altVersion/$section.html");
                if (is_file($file)) {
                    $alternatives[$altVersion] = ['api/extension-view', 'version' => $altVersion, 'section' => $section, 'name' => $extensionName, 'vendorName' => $extensionVendor];
                }
            }
            $alternativeIndices[$altVersion] = ['api/extension-index', 'version' => $altVersion, 'name' => $extensionName, 'vendorName' => $extensionVendor];
        }

        // if class is not found, show a better 404
        Yii::$app->response->statusCode = 404;
        return $this->render('error-404', [
            'extension' => $extension,
            'alternatives' => $alternatives,
            'alternativeVersions' => $alternativeIndices,
        ]);

    }

    /**
     * For application/json request, provide all classes of 1.1 and 2.0
     * For Html, just redirect to latest docs.
     */
    public function actionEntry()
    {
        switch (Yii::$app->response->format) {
            case Response::FORMAT_HTML:
                // TODO: change version
                return $this->redirect(['index', 'version' => '2.0']); // Found, latest docs url is not permanent

            case Response::FORMAT_JSON:

                // apply HTTP cache to JSON formatted API data to allow browsers to store these (improve search speed)
                $httpCache = Yii::createObject([
                    'class' => HttpCache::class,
                    'cacheControlHeader' => 'public, max-age=86400',
                    'lastModified' => function() {
                        return strtotime('yesterday 00:00:00');
                    },
                ]);
                // tell browser that the cached content varies by Content-Type.
                Yii::$app->response->headers->add('Vary', 'Accept');
                if (!$httpCache->beforeAction($this->action)) {
                    // HTTP 304 Not Modified
                    return;
                }

                // 2.0 classes
                $apiRenderer = new ApiRenderer([
                    'version' => '2.0',
                ]);

                $classes = Json::decode(file_get_contents(Yii::getAlias('@app/data/api-2.0/json/typeNames.json')));
                foreach($classes as $i => $class) {
                    $classes[$i]['url'] = Yii::$app->request->hostInfo . $apiRenderer->generateApiUrl($class['name']);
                    $classes[$i]['version'] = '2.0';
                }

                // 1.1 classes
                $classes1 = Json::decode(file_get_contents(Yii::getAlias('@app/data/api-1.1/json/typeNames.json')));
                foreach($classes1 as $i => $class) {
                    $classes1[$i]['url'] = Yii::$app->params['api.baseUrl'] . "/1.1/{$class['name']}";
                    $classes1[$i]['version'] = '1.1';
                }

                return [
                    'classes' => array_merge($classes, $classes1),
                ];
        }
        throw new UnsupportedMediaTypeHttpException;
    }

    /**
     * For application/json request, provide all class members of 1.1 and 2.0
     */
    public function actionClassMembers()
    {
        switch (Yii::$app->response->format) {
            case Response::FORMAT_JSON:

                // 2.0 class members
                $apiRenderer = new ApiRenderer([
                    'version' => '2.0',
                ]);

                $members = Json::decode(file_get_contents(Yii::getAlias('@app/data/api-2.0/json/typeMembers.json')));
                foreach($members as $m => $member) {
                    $hash = $member['name'] . ($member['type'] === 'method' ? '()' : '') . '-detail';
                    foreach($members[$m]['implemented'] as $i => $impl) {
                        $members[$m]['implemented'][$i] = [
                            'name' => $impl,
                            'url' => Yii::$app->request->hostInfo . $apiRenderer->generateApiUrl($impl) . "#$hash",
                        ];
                    }
                    $members[$m]['version'] = '2.0';
                }

                // 1.1 classes
                $members1 = Json::decode(file_get_contents(Yii::getAlias('@app/data/api-1.1/json/typeMembers.json')));
                foreach($members1 as $m => $member) {
                    $hash = $member['name'] . '-detail';
                    foreach($members1[$m]['implemented'] as $i => $impl) {
                        $members1[$m]['implemented'][$i] = [
                            'name' => $impl,
                            'url' => Yii::$app->params['api.baseUrl'] . "/1.1/{$impl}#$hash"
                        ];
                    }
//                    $members1[$i]['url'] = ;
                    $members1[$m]['version'] = '1.1';
                }

                return [
                    'members' => array_merge($members, $members1),
                ];
        }
        throw new UnsupportedMediaTypeHttpException;
    }

    /**
     * This action redirects old urls to the new location.
     *
     * - https://www.yiiframework.com/doc-2.0/*.html URLs are redirected to 2.0 apidoc
     * - https://www.yiiframework.com/doc/api/ClassName are redirected to 1.1 apidoc
     */
    public function actionRedirect($section, $version = null)
    {
        if (preg_match('/^[\w\-]+$/', $section)) {

            // check 2.0 apidoc
            if ($version === null || $version[0] === '2') {
                $file = Yii::getAlias("@app/data/api-2.0/$section.html");
                if (is_file($file)) {
                    return $this->redirect(['view', 'version' => '2.0', 'section' => $section], 301); // Moved Permanently
                }
            }
            // check 1.1 apidoc, case insensitive search
            if ($version === null || $version[0] === '1') {
                foreach (FileHelper::findFiles(Yii::getAlias('@app/data/api-1.1/api'), ['only' => ['*.html']]) as $file) {
                    $baseName = basename($file, '.html');
                    if (strcasecmp($baseName, $section) === 0) {
                        return $this->redirect(['view', 'version' => '1.1', 'section' => $baseName], 301); // Moved Permanently
                    }
                }
            }
            // check extension classes e.g. /doc-2.0/yii-imagine-baseimage.html
            $extensions = Extension::find()->where("name LIKE 'yiisoft/yii2-%'")->all();
            foreach($extensions as $extension) {
                $versions = $extension->getApiVersions();
                arsort($versions);
                foreach($versions as $version) {
                    if (is_file(Yii::getAlias("@app/data/extensions/{$extension->name}/api-$version/$section.html"))) {
                        list($vendorName, $extensionName) = explode('/', $extension->name);
                        return $this->redirect(['extension-view', 'version' => $version, 'section' => $section, 'vendorName' => $vendorName, 'name' => $extensionName], 301); // Moved Permanently
                    }
                }
            }


        }
        return $this->api404($section, $version);
    }
}
