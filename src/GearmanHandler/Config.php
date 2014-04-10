<?php
namespace GearmanHandler;

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
    private $bootstrap;

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
     * @var Config
     */
    private static $instance;

    /**
     * gets the instance via lazy initialization (created on first usage)
     *
     * @return self
     */
    public static function getInstance()
    {

        if (null === static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * @param array $params
     */
    public function __construct(array $params = null)
    {
        static::$instance = $this;
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
                    case 'bootstrap':
                        $this->setBootstrap($value);
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
            case 'bootstrap':
                return $this->getBootstrap();
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
     * @param string $bootstrap
     * @return $this
     */
    public function setBootstrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;
        return $this;
    }

    /**
     * @return string
     */
    public function getBootstrap()
    {
        return $this->bootstrap;
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