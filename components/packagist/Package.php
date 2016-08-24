<?php
namespace app\components\packagist;

use yii\helpers\Url;

/**
 * Class Package
 * @package app\components\packagist
 */
class Package
{
    const URL_REMOTE = 'https://packagist.org/packages/%s';

    private $vendorName;
    private $packageName;
    private $description;
    private $repository;
    private $versions = [];
    private $downloads = 0;
    private $favers = 0;
    private $updatedAt;

    /**
     * @return mixed
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     * @return mixed
     */
    public function getPackageName()
    {
        return $this->packageName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getVendorName() . '/' . $this->getPackageName();
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return int
     */
    public function getFavers()
    {
        return $this->favers;
    }

    /**
     * @return int
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * @return array
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return mixed
     */
    public function getRepositoryHost()
    {
        return parse_url($this->getRepository(), PHP_URL_HOST);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return Url::to( [
            'extension/package',
            'vendorName' => $this->getVendorName(),
            'packageName' => $this->getPackageName()
        ]);
    }

    /**
     * Url to packagist.org
     *
     * @return string
     */
    public function getUrlRemote()
    {
        return sprintf(self::URL_REMOTE, $this->getName());
    }

    private function __construct()
    {
    }

    /**
     * @param array $data
     * @return Package|null
     */
    public static function createFromAPIData($data)
    {
        $package = new self();

        if (array_key_exists('name', $data)) {
            if (!preg_match('/^([\w\-\.]+)\/([\w\-\.]+)$/i', $data['name'], $matches)) {
                return null;
            } else {
                $package->vendorName = $matches[1];
                $package->packageName = $matches[2];
            }
        }

        if (array_key_exists('time', $data)) {
            $package->updatedAt = strtotime($data['time']);
        }

        foreach (['description', 'repository', 'versions', 'downloads', 'favers'] as $key) {
            if (array_key_exists($key, $data)) {
                $package->{$key} = $data[$key];
            }
        }

        return $package;
    }
}