<?php
namespace GearmanHandler\Tests;

use GearmanHandler\Daemon;
use GearmanHandler\Process;
use GearmanHandler\Config;
use PHPUnit_Framework_TestCase;

class WorkerTest extends PHPUnit_Framework_TestCase
{
    public function testConfig()
    {
        Config::setConfigFile(__DIR__ . "/__files/config.php");

        $daemon = new Daemon();
        $daemon->run();

        Process::stop();
    }
}
