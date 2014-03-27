<?php
namespace GearmanHandler\Tests;

use GearmanHandler\Daemon;
use GearmanHandler\Config;
use GearmanHandler\Worker;
use GearmanHandler\Process;
use GearmanHandler\Tests\Jobs\CreateFile;
use PHPUnit_Framework_TestCase;

class JobTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $config;

    public function setUp()
    {
        $this->config = new Config(require __DIR__ . "/_files/config.php");
    }

    public function tearDown()
    {
        if (file_exists(CreateFile::getFilePath())) {
            unlink(CreateFile::getFilePath());
        }
    }

    public function testRegisterJobs()
    {
        $daemon = new Daemon($this->config);
        $daemon->addCallback(function (Daemon $daemon) use (&$test) {
            $test = true;
            $daemon->setKill(true);
        });
        $daemon->run(false);
        $workers = $daemon->getRegisteredJobs();

        $this->assertEquals(1, count($workers));
        $this->assertEquals('\GearmanHandler\Tests\Jobs\CreateFile', $workers[0]);
    }

    public function testJob()
    {
        $daemon = new Daemon($this->config);
        $daemon->run();

        $worker = new Worker($this->config);
        $worker->execute('CreateFile');

        (new Process($this->config))->stop();
    }
}
