<?php
namespace GearmanHandler\Tests;

use GearmanHandler\Daemon;
use GearmanHandler\Config;
use GearmanHandler\Worker;
use GearmanHandler\Process;
use PHPUnit_Framework_TestCase;

class WorkerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Config::setConfigFile(__DIR__ . "/__files/config.php");
    }

    public function testRegisterWorkers()
    {
        $daemon = new Daemon();
        $daemon->addCallback(function(Daemon $daemon) use (&$test) {
            $test = true;
            $daemon->setKill(true);
        });
        $daemon->run(false);
        $workers = $daemon->getRegisteredWorkers();

        $this->assertEquals(1, count($workers));
        $this->assertEquals('\GearmanHandler\Tests\Workers\CreateFile', $workers[0]);
    }

    public function testWorker()
    {
        $daemon = new Daemon();
        $daemon->run();

        Worker::background('CreateFile');

        Process::stop();
    }
}
