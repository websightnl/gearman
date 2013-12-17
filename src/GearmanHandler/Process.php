<?php
namespace GearmanHandler;

class Process
{
    const PID_FILE = 'gearmanhandler.pid';

    /**
     * @return string
     */
    public static function getPIDFile()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::PID_FILE;
    }

    public static function stop()
    {
        $pid = (int)file_get_contents(self::getPIDFile());

        if ($pid) {
            posix_kill($pid, SIGUSR1);
        }
    }

    /**
     * @param int $pid
     * @return bool|resource
     */
    public static function lock($pid)
    {
        $fp = fopen(self::getPIDFile(), "w+");
        fwrite($fp, $pid);

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

        file_put_contents(self::getPIDFile(), "");
    }

    /**
     * @return bool
     */
    public static function isRunning()
    {
        $fp = fopen(self::getPIDFile(), "w+");

        if (!flock($fp, LOCK_SH | LOCK_NB)) {
            fclose($fp);
            return true;
        }

        fclose($fp);
        return false;
    }
}