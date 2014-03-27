<?php
namespace GearmanHandler\Tests;

use GearmanHandler\Config;
use PHPUnit_Framework_TestCase;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    private $configValues;

    public function setUp()
    {
        $this->configValues = require __DIR__ . '/_files/config.php';
    }

    public function testConstruct()
    {
        $config = new Config();
        $this->assertEquals('127.0.0.1', $config->getGearmanHost());
        $this->assertEquals(4730, $config->getGearmanPort());
        $this->assertNull($config->getJobsDir());
        $this->assertNull($config->getWorkerLifetime());
        $this->assertFalse($config->getAutoUpdate());

        $config = new Config(['gearman_host' => 'test1']);
        $this->assertEquals('test1', $config->getGearmanHost());
    }

    public function testSetAndGet()
    {
        $config = new Config();
        $config->set($this->configValues);
        $this->assertEquals($this->configValues['gearman_host'], $config->get('gearman_host'));
        $this->assertEquals($this->configValues['gearman_port'], $config->get('gearman_port'));
        $this->assertEquals($this->configValues['jobs_dir'], $config->get('jobs_dir'));
        $this->assertEquals($this->configValues['worker_lifetime'], $config->get('worker_lifetime'));
        $this->assertEquals($this->configValues['auto_update'], $config->get('auto_update'));
    }

    public function testSettersGetters()
    {
        $config = new Config();

        $config->setGearmanHost('test');
        $this->assertEquals('test', $config->getGearmanHost());

        $config->setGearmanPort(6000);
        $this->assertEquals(6000, $config->getGearmanPort());

        $config->setJobsDir('test');
        $this->assertEquals('test', $config->getJobsDir());

        $config->setWorkerLifetime(6000);
        $this->assertEquals(6000, $config->getWorkerLifetime());

        $config->setAutoUpdate(true);
        $this->assertEquals(true, $config->getAutoUpdate());
    }
}
