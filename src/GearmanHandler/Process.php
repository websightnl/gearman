<?php
namespace GearmanHandler;

class Process
{
    const PID_FILE = 'gearmanhandler.pid';
    const LOCK_FILE = 'gearmanhandler.lock';

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->setConfig($config);
    }

    /**
     * @return string
     */
    public function getPidFile()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::PID_FILE;
    }

    /**
     * @return string
     */
    public function getLockFile()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::LOCK_FILE;
    }

    public function stop()
    {
        if (file_exists($file = $this->getPidFile())) {
            $pid = (int)file_get_contents($this->getPidFile());
        }

        if (isset($pid) && $pid) {
            posix_kill($pid, SIGUSR1);
        }

        if (file_exists($file = $this->getPidFile()) && is_writable($file)) {
            unlink($file);
        }
    }

    /**
     * @param string $pid
     */
    public function setPid($pid)
    {
        file_put_contents($this->getPidFile(), $pid);
    }

    /**
     * @return bool|resource
     */
    public function lock()
    {
        $fp = fopen($this->getLockFile(), "w+");

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            return $fp;
        }

        return false;
    }

    /**
     * @param resource $fp
     */
    public function release($fp)
    {
        flock($fp, LOCK_UN);
        fclose($fp);

        if (file_exists($file = $this->getLockFile()) && is_writable($file)) {
            unlink($file);
        }
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        $fp = fopen($this->getLockFile(), "w+");

        if (!flock($fp, LOCK_SH | LOCK_NB)) {
            fclose($fp);
            return true;
        }

        fclose($fp);
        return false;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}