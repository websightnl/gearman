<?php
namespace GearmanHandler\Tests;

use GearmanHandler\Daemon;
use GearmanHandler\Process;
use PHPUnit_Framework_TestCase;
use GearmanHandler\Config;

class DaemonTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Config::setConfigFile(__DIR__ . "/__files/config.php");
    }

    public function testProcessGetsStarted()
    {
        $test = false;

        $daemon = new Daemon();
        $daemon->addCallback(function (Daemon $daemon) use (&$test) {
            $test = true;
            $daemon->setKill(true);
        });
        $daemon->run(false);

        $this->assertTrue($test);
    }

    public function testDaemon()
    {
        $daemon = new Daemon();
        $daemon->run();

        $test = Process::isRunning();
        Process::stop();

        $this->assertTrue($test);
    }
}
