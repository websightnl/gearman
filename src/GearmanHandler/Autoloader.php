<?php
namespace GearmanHandler;

/**
 * Autoloads GearmanHandler classes
 *
 * @package gearman-handler
 */
class Autoloader
{
    const PACKAGE_NAMESPACE = 'GearmanHandler';

    /**
     * Register the autoloader
     *
     * @return  void
     */
    public static function register()
    {
        spl_autoload_register([new self, 'autoload']);
    }

    /**
     * Autoloader
     *
     * @param   string
     * @return  mixed
     */
    public static function autoload($class)
    {
        if (0 === stripos($class, self::PACKAGE_NAMESPACE)) {
            $file = str_replace('\\', '/', substr($class, strlen(self::PACKAGE_NAMESPACE)));
            $file = realpath(__DIR__ . (empty($file) ? '' : '/') . $file . '.php');
            if (is_file($file)) {
                require_once $file;
                return true;
            }
        }
        return null;
    }
}