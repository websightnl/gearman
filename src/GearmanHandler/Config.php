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
}