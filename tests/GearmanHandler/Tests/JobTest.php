<?php
namespace GearmanHandler\Tests;

use GearmanHandler\Application;
use GearmanHandler\Config;
use GearmanHandler\Tests\Jobs\MockJob;
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
        $application = new Application($this->config);
        $application->addCallback(function (Application $application) use (&$test) {
            $test = true;
            $application->setKill(true);
        });
        $application->run(false);
        $workers = $application->getJobs();

        $this->assertEquals(1, count($workers));
        $this->assertEquals('\GearmanHandler\Tests\Jobs\CreateFile', $workers[0]);
    }

    public function testJob()
    {
        $application = new Application($this->config);
        $application->add(new MockJob());
        $application->run();

        $worker = new Worker($this->config);
        $worker->execute('CreateFile');

        (new Process($this->config))->stop();
    }
}
