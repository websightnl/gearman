<?php
namespace GearmanHandler;

class Process
{
    const PID_FILE = 'gearmanhandler.pid';

    /**
     * @return bool
     */
    public static function isRunning()
    {
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::PID_FILE;

        $fp = @fopen($file, "w+");

        if (!flock($fp, LOCK_SH | LOCK_NB)) {
            fclose($fp);
            return true;
        }

        fclose($fp);
        return false;
    }
}