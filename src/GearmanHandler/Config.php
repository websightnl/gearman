<?php
namespace GearmanHandler;

/**
 * Class Config
 * @package GearmanHandler
 */
class Config
{
    /**
     * @var string
     */
    private $gearmanHost = '127.0.0.1';

    /**
     * @var int
     */
    private $gearmanPort = 4730;

    /**
     * @var string
     */
    private $workerDir;

    /**
     * @var int
     */
    private $workerLifetime;

    /**
     * @var bool
     */
    private $autoUpdate = false;

    /**
     * @var string
     */
    private $user;

    /**
     * @param array $params
     */
    public function __construct(array $params = null)
    {
        if (null !== $params) {
            $this->set($params);
        }
    }

    /**
     * @param array|string $params
     * @param null|mixed $value
     */
    public function set($params, $value = null)
    {
        if (!is_array($params)) {
            $params = array($params => $value);
        }
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                switch ($key) {
                    case 'gearmanHost':
                    case 'gearman_host':
                        $this->setGearmanHost($value);
                        break;
                    case 'gearmanPort':
                    case 'gearman_port':
                        $this->setGearmanPort($value);
                        break;
                    case 'workerDir':
                    case 'worker_dir':
                        $this->setWorkerDir($value);
                        break;
                    case 'workerLifetime':
                    case 'worker_lifetime':
                        $this->setWorkerLifetime($value);
                        break;
                    case 'autoUpdate':
                    case 'auto_update':
                        $this->setAutoUpdate($value);
                        break;
                    case 'user':
                        $this->setUser($value);
                        break;
                }
            }
        }
    }

    /**
     * @param string $key
     * @return null|mixed
     */
    public function get($key)
    {
        switch ($key) {
            case 'gearmanHost':
            case 'gearman_host':
                return $this->getGearmanHost();
                break;
            case 'gearmanPort':
            case 'gearman_port':
                return $this->getGearmanPort();
                break;
            case 'workerDir':
            case 'worker_dir':
                return $this->getWorkerDir();
                break;
            case 'workerLifetime':
            case 'worker_lifetime':
                return $this->getWorkerLifetime();
                break;
            case 'autoUpdate':
            case 'auto_update':
                return $this->getAutoUpdate();
                break;
            case 'user':
                return $this->getUser();
                break;
        }
        return null;
    }

    /**
     * @param string $gearmanHost
     * @return $this
     */
    public function setGearmanHost($gearmanHost)
    {
        $this->gearmanHost = $gearmanHost;
        return $this;
    }

    /**
     * @return string
     */
    public function getGearmanHost()
    {
        return $this->gearmanHost;
    }

    /**
     * @param int $gearmanPort
     * @return $this
     */
    public function setGearmanPort($gearmanPort)
    {
        $this->gearmanPort = $gearmanPort;
        return $this;
    }

    /**
     * @return int
     */
    public function getGearmanPort()
    {
        return $this->gearmanPort;
    }

    /**
     * @param bool $autoUpdate
     * @return $this
     */
    public function setAutoUpdate($autoUpdate)
    {
        $this->autoUpdate = $autoUpdate;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAutoUpdate()
    {
        return $this->autoUpdate;
    }

    /**
     * @param string $workerDir
     * @return $this
     */
    public function setWorkerDir($workerDir)
    {
        $this->workerDir = $workerDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getWorkerDir()
    {
        return $this->workerDir;
    }

    /**
     * @param int $workerLifetime
     * @return $this
     */
    public function setWorkerLifetime($workerLifetime)
    {
        $this->workerLifetime = $workerLifetime;
        return $this;
    }

    /**
     * @return int
     */
    public function getWorkerLifetime()
    {
        return $this->workerLifetime;
    }

    /**
     * @param string $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }
}