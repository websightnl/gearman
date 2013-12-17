<?php
namespace GearmanHandler;

use GearmanWorker;
use React\EventLoop\Factory as Loop;

class Daemon
{
    /** @var bool|resource $lock */
    private $lock = false;

    /** @var bool $kill */
    private $kill = false;

    /** @var \GearmanWorker $worker */
    private $worker;

    public function __destruct()
    {
        if (is_resource($this->lock)) {
            Process::release($this->lock);
        }
    }

    public function run($fork = true)
    {
        if ($fork) {
            $pid = pcntl_fork();
        }

        if (!$fork || (isset($pid) && $pid !== -1 && !$pid)) {
            $this->lock = Process::lock(posix_getpid());
            $this->signalHandlers();
            $this->createWorker();
            $this->createLoop();
        }
    }

    public function signalHandlers()
    {
        $root = $this;
        pcntl_signal(SIGUSR1, function() use ($root) {
            $root->setKill(true);
        });
    }

    private function createWorker()
    {
        $this->worker = new GearmanWorker();
        $this->worker->addServer(Config::getGearmanHost(), Config::getGearmanPort());
    }

    private function createLoop()
    {
        $root = $this;
        $worker = $this->worker;
        $loop = Loop::create();

        $loop->addPeriodicTimer(.05, function() use ($loop, $worker, $root) {
            pcntl_signal_dispatch();
            if ($root->getKill()) {
                $loop->stop();
            }
        });

        $loop->run();
    }

    /**
     * @return bool
     */
    public function getKill()
    {
        return $this->kill;
    }

    /**
     * @param $kill
     * @return $this
     */
    public function setKill($kill)
    {
        $this->kill = $kill;
        return $this;
    }
}