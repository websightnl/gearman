<?php
namespace Sinergi\Gearman\Tests;

use PHPUnit_Framework_TestCase;
use Sinergi\Gearman\Application;
use Sinergi\Gearman\Process;
use Sinergi\Gearman\Config;
use Sinergi\Gearman\Tests\Job\MockJob;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $config;

    public function setUp()
    {
        $this->config = new Config(require __DIR__ . "/config.php");
    }

    public function testProcessGetsStarted()
    {
        $test = false;

        $application = new Application($this->config);
        $application->add(new MockJob());
        $application->addCallback(function (Application $application) use (&$test) {
            $test = true;
            $application->setKill(true);
        });
        $application->run(false);

        $this->assertTrue($test);
    }

    public function testApplication()
    {
        $process = new Process($this->config);
        $application = new Application($this->config, $process);
        $application->add(new MockJob());
        $application->run();

        $test = $process->isRunning();

        $this->assertTrue($test);

        $process->stop();
    }
}
