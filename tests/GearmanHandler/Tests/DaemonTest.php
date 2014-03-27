<?php
namespace GearmanHandler\Tests;

use GearmanHandler\Daemon;
use GearmanHandler\Process;
use PHPUnit_Framework_TestCase;
use GearmanHandler\Config;

class DaemonTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $config;

    public function setUp()
    {
        $this->config = new Config(require __DIR__ . "/_files/config.php");
    }

    public function testProcessGetsStarted()
    {
        $test = false;

        $daemon = new Daemon($this->config);
        $daemon->addCallback(function (Daemon $daemon) use (&$test) {
            $test = true;
            $daemon->setKill(true);
        });
        $daemon->run(false);

        $this->assertTrue($test);
    }

    public function testDaemon()
    {
        $process = new Process($this->config);
        $daemon = new Daemon($this->config, $process);
        $daemon->run();

        $test = $process->isRunning();

        $this->assertTrue($test);

        $process->stop();
    }
}
