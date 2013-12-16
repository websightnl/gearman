<?php
namespace GearmanHandler;

/**
 * Class Config
 * @package GearmanHandler
 */
class Config
{
    /** @var string $file */
    private static $file;

    /** @var string $dir */
    private static $dir;

    /** @var string host */
    private static $host = '127.0.0.1';

    /** @var int port */
    private static $port = 4730;

    /**
     * @param string $file
     */
    public static function setFile($file)
    {
        self::$file = $file;
    }

    /**
     * @return string
     */
    public static function getFile()
    {
        return self::$file;
    }

    /**
     * @param string $dir
     */
    public static function setDir($dir)
    {
        self::$dir = $dir;
    }

    /**
     * @return string
     */
    public static function getDir()
    {
        return self::$dir;
    }

    /**
     * @param string $path
     */
    public static function setPath($path)
    {
        self::$dir = dirname($path);
        self::$file = basename($path);
    }

    /**
     * @return string
     */
    public static function getPath()
    {
        $path = self::getDir() . DIRECTORY_SEPARATOR . self::getFile();
        if ($path !== DIRECTORY_SEPARATOR) {
            return $path;
        }
        return null;
    }

    /**
     * @param string $host
     */
    public static function setHost($host)
    {
        self::$host = $host;
    }

    /**
     * @return string
     */
    public static function getHost()
    {
        return self::$host;
    }

    /**
     * @param int $port
     */
    public static function setPort($port)
    {
        self::$port = $port;
    }

    /**
     * @return int
     */
    public static function getPort()
    {
        return self::$port;
    }
}