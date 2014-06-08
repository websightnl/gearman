<?php
namespace Sinergi\Gearman\Tests;

use Sinergi\Gearman\Application;
use Sinergi\Gearman\Config;
use Sinergi\Gearman\Tests\Jobs\MockJob;
use Sinergi\Gearman\Worker;
use Sinergi\Gearman\Process;
use Sinergi\Gearman\Tests\Jobs\CreateFile;
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
        $application->add(new MockJob());
        $application->add(new CreateFile());
        $application->addCallback(function (Application $application) use (&$test) {
            $test = true;
            $application->setKill(true);
        });
        $application->run(false);
        $workers = $application->getJobs();

        $this->assertEquals(2, count($workers));
        $this->assertInstanceOf('Sinergi\Gearman\Tests\Jobs\MockJob', $workers[0]);
        $this->assertInstanceOf('Sinergi\Gearman\Tests\Jobs\CreateFile', $workers[1]);
    }
}
