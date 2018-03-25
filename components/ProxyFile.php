<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\validators\UrlValidator;
use yii\web\Response;

class ProxyFile extends Component
{
    /**
     * @var string
     */
    public $secretKey;
    /**
     * @var string
     */
    public $storagePath = '@runtime/storageProxyFile';
    /**
     * @var string
     */
    public $storageUrl = '/storageProxyFile';

    public function init()
    {
        if ($this->secretKey === null) {
            $this->secretKey = Yii::$app->request->cookieValidationKey;
        }

        if (empty($this->secretKey)) {
            throw new InvalidArgumentException('SecretKey is empty.');
        }

        parent::init();
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getConvertUrl($url)
    {
        if (!$this->validateUrl($url)) {
             return $url;
        }

        $data = base64_encode(\Yii::$app->security->hashData($url, $this->secretKey));
        $url = Url::to(['/site/proxy-file', 'data' => $data]);

        return $url;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function validateUrl($url)
    {
        return (new UrlValidator(['skipOnEmpty' => false]))->validate($url) &&
            StringHelper::startsWith($url, 'http://') &&
            !StringHelper::startsWith($url, Yii::$app->params['siteAbsoluteUrl']);
    }

    /**
     * @param string $data
     *
     * @return Response|bool
     */
    public function sendFile($data)
    {
        $fileUrl = \Yii::$app->security->validateData(base64_decode($data), $this->secretKey);
        if ($fileUrl === false) {
            return false;
        }

        if (!$this->validateUrl($fileUrl)) {
            return false;
        }

        $fileData = null;
        try {
            $fileData = file_get_contents($fileUrl);
        } catch (\Exception $ex) {
            // ignore exception
        }

        if (!$fileData) {
            return false;
        }

        $hash = md5($fileUrl);
        $hashPath = implode('/', array_slice(str_split($hash, 2), 0, 2));
        $fileBasePath = "/{$hashPath}/{$hash}";

        $filePath = Yii::getAlias($this->storagePath) . $fileBasePath;

        $fileExists = file_exists($filePath);
        if (!$fileExists) {
            FileHelper::createDirectory(dirname($filePath));
            if (file_put_contents($filePath, $fileData) !== false) {
                $fileExists = true;
            }
        }

        if ($fileExists) {
            $fileUrl = Yii::getAlias($this->storageUrl) . $fileBasePath;
            Yii::$app->response->xSendFile($fileUrl, null, ['xHeader' => 'X-Accel-Redirect']);

            return true;
        }

        return false;
    }
}
