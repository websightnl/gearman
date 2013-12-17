<?php
namespace GearmanHandler\Tests;

use GearmanHandler\Config;
use PHPUnit_Framework_TestCase;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    private $configFile;
    private $configValues;

    public function setUp()
    {
        $this->configFile = __DIR__ . '/__files/config.php';
        $this->configValues = require $this->configFile;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFileNotExists()
    {
        Config::setConfigFile($this->configFile . '.test');
    }

    public function testSetFile()
    {
        Config::setConfigFile($this->configFile);
    }

    /**
     * @depends testSetFile
     */
    public function testGetConfigs()
    {
        $this->assertEquals($this->configValues['gearman_host'], Config::getGearmanHost());
        $this->assertEquals($this->configValues['gearman_port'], Config::getGearmanPort());
        $this->assertEquals($this->configValues['worker_dir'], Config::getWorkerDir());
        $this->assertEquals($this->configValues['worker_lifetime'], Config::getWorkerLifetime());
        $this->assertEquals($this->configValues['auto_update'], Config::getAutoUpdate());
    }

    public function testSetConfigs()
    {
        Config::setGearmanHost('test');
        $this->assertEquals('test', Config::getGearmanHost());
        Config::setGearmanPort(6000);
        $this->assertEquals(6000, Config::getGearmanPort());
        Config::setWorkerDir('test');
        $this->assertEquals('test', Config::getWorkerDir());
        Config::setWorkerLifetime(6000);
        $this->assertEquals(6000, Config::getWorkerLifetime());
        Config::getAutoUpdate(true);
        $this->assertEquals(true, Config::getAutoUpdate());
    }
}
