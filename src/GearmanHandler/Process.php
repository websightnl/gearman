<?php
namespace GearmanHandler;

class Process
{
    const PID_FILE = 'gearmanhandler.pid';
    const LOCK_FILE = 'gearmanhandler.lock';

    /**
     * @return string
     */
    public static function getPidFile()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::PID_FILE;
    }

    /**
     * @return string
     */
    public static function getLockFile()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::LOCK_FILE;
    }

    public static function stop()
    {
        if (file_exists($file = self::getPidFile())) {
            $pid = (int)file_get_contents(self::getPidFile());
        }

        if (isset($pid) && $pid) {
            posix_kill($pid, SIGUSR1);
        }

        if (file_exists($file = self::getPidFile()) && is_writable($file)) {
            unlink($file);
        }
    }

    /**
     * @param string $pid
     */
    public static function setPid($pid)
    {
        file_put_contents(self::getPidFile(), $pid);
    }

    /**
     * @return bool|resource
     */
    public static function lock()
    {
        $fp = fopen(self::getLockFile(), "w+");

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            return $fp;
        }

        return false;
    }

    /**
     * @param resource $fp
     */
    public static function release($fp)
    {
        flock($fp, LOCK_UN);
        fclose($fp);

        if (file_exists($file = self::getLockFile()) && is_writable($file)) {
            unlink($file);
        }
    }

    /**
     * @return bool
     */
    public static function isRunning()
    {
        $fp = fopen(self::getLockFile(), "w+");

        if (!flock($fp, LOCK_SH | LOCK_NB)) {
            fclose($fp);
            return true;
        }

        fclose($fp);
        return false;
    }
}