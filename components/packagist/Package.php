<?php
namespace app\components\packagist;

use yii\helpers\Url;

class Package
{
    private $vendor;
    private $name;
    private $description;
    private $repository;
    private $versions = [];
    private $downloads = 0;
    private $favers = 0;
    private $updatedAt;

    /**
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
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

    public function getUrl()
    {
        return Url::to( [
            'package',
            'vendorName' => $this->getVendor(),
            'packageName' => $this->getName(),
        ]);
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
            if (!preg_match('/^([a-z\d\-_]+)\/([a-z\d\-_]+)$/i', $data['name'], $matches)) {
                return null;
            } else {
                $package->vendor = $matches[1];
                $package->name = $matches[2];
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