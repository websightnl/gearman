<?php
namespace GearmanHandler;

use GearmanWorker;
use React\EventLoop\Factory as Loop;

class Daemon
{
    /** @var \GearmanWorker $worker */
    private $worker;

    public function __construct()
    {
    }

    /**
     *
     */
    public function run()
    {
        $this->createWorker();
        $this->createLoop();
    }

    /**
     *
     */
    private function createWorker()
    {
        $this->worker = new GearmanWorker();
        $this->worker->addServer(Config::getHost(), Config::getPort());
    }

    /**
     *
     */
    private function createLoop()
    {
        $worker = $this->worker;
        $loop = Loop::create();
        $loop->addPeriodicTimer(.05, function() use ($loop, $worker) {
            $loop->stop();
        });
        $loop->run();
    }
}