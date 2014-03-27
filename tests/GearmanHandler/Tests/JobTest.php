<?php
namespace GearmanHandler\Tests;

use GearmanHandler\Daemon;
use GearmanHandler\Config;
use GearmanHandler\Worker;
use GearmanHandler\Process;
use GearmanHandler\Tests\Workers\CreateFile;
use PHPUnit_Framework_TestCase;

class JobTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $config;

    public function setUp()
    {
        $this->config = new Config(require_once __DIR__ . "/_files/config.php");
    }

    public function tearDown()
    {
        if (file_exists(CreateFile::getFilePath())) {
            unlink(__DIR__ . '/__files/worker_test');
        }
    }

    public function testRegisterJobs()
    {
        $daemon = new Daemon();
        $daemon->addCallback(function (Daemon $daemon) use (&$test) {
            $test = true;
            $daemon->setKill(true);
        });
        $daemon->run(false);
        $workers = $daemon->getRegisteredJobs();

        $this->assertEquals(1, count($workers));
        $this->assertEquals('\GearmanHandler\Tests\Workers\CreateFile', $workers[0]);
    }

    public function testJob()
    {
        $daemon = new Daemon();
        $daemon->run();

        Worker::execute('CreateFile');

        Process::stop();
    }
}
