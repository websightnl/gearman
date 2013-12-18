<?php
namespace GearmanHandler;

use InvalidArgumentException;

/**
 * Class Config
 * @package GearmanHandler
 */
class Config
{
    /** @var string $config_file */
    private static $config_file;

    /** @var string $gearman_host */
    private static $gearman_host = '127.0.0.1';

    /** @var int $gearman_port */
    private static $gearman_port = 4730;

    /** @var string $worker_dir */
    private static $worker_dir;

    /** @var int $worker_lifetime */
    private static $worker_lifetime;

    /** @var bool $auto_update */
    private static $auto_update = false;

    /** @var string $user */
    private static $user;

    private static function setConfigs()
    {
        $configs = require self::$config_file;
        if (is_array($configs)) {
            foreach ($configs as $key => $value) {
                switch ($key) {
                    case 'gearman_host':
                        self::setGearmanHost($value);
                        break;
                    case 'gearman_port':
                        self::setGearmanPort($value);
                        break;
                    case 'worker_dir':
                        self::setWorkerDir($value);
                        break;
                    case 'worker_lifetime':
                        self::setWorkerLifetime($value);
                        break;
                    case 'auto_update':
                        self::setAutoUpdate($value);
                        break;
                    case 'user':
                        self::setUser($value);
                        break;
                }
            }
        }
    }

    /**
     * @param string $config_file
     * @throws \InvalidArgumentException
     */
    public static function setConfigFile($config_file)
    {
        $config_file = realpath($config_file);

        if (null === $config_file || !file_exists($config_file)) {
            throw new InvalidArgumentException('Configuration file [' . $config_file . '] does not exists or does not have read permission');
        }

        self::$config_file = $config_file;
        self::setConfigs();
    }

    /**
     * @return string
     */
    public static function getConfigFile()
    {
        return self::$config_file;
    }

    /**
     * @param string $gearman_host
     */
    public static function setGearmanHost($gearman_host)
    {
        self::$gearman_host = $gearman_host;
    }

    /**
     * @return string
     */
    public static function getGearmanHost()
    {
        return self::$gearman_host;
    }

    /**
     * @param int $gearman_port
     */
    public static function setGearmanPort($gearman_port)
    {
        self::$gearman_port = $gearman_port;
    }

    /**
     * @return int
     */
    public static function getGearmanPort()
    {
        return self::$gearman_port;
    }

    /**
     * @param boolean $auto_update
     */
    public static function setAutoUpdate($auto_update)
    {
        self::$auto_update = $auto_update;
    }

    /**
     * @return boolean
     */
    public static function getAutoUpdate()
    {
        return self::$auto_update;
    }

    /**
     * @param string $worker_dir
     */
    public static function setWorkerDir($worker_dir)
    {
        self::$worker_dir = $worker_dir;
    }

    /**
     * @return string
     */
    public static function getWorkerDir()
    {
        return self::$worker_dir;
    }

    /**
     * @param int $worker_lifetime
     */
    public static function setWorkerLifetime($worker_lifetime)
    {
        self::$worker_lifetime = $worker_lifetime;
    }

    /**
     * @return int
     */
    public static function getWorkerLifetime()
    {
        return self::$worker_lifetime;
    }

    /**
     * @param string $user
     */
    public static function setUser($user)
    {
        self::$user = $user;
    }

    /**
     * @return string
     */
    public static function getUser()
    {
        return self::$user;
    }
}