<?php
namespace GearmanHandler\Tests;

use GearmanHandler\Daemon;
use GearmanHandler\Process;
use PHPUnit_Framework_TestCase;

class DaemonTest extends PHPUnit_Framework_TestCase
{
    public function testTest()
    {
        $daemon = new Daemon();
        $daemon->run();

        Process::stop();
    }
}
