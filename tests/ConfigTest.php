<?php
namespace Sinergi\Gearman\Tests;

use PHPUnit_Framework_TestCase;
use Sinergi\Gearman\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    private $configValues;

    public function setUp()
    {
        $this->configValues = require __DIR__ . '/config.php';
    }

    public function testGetInstanceReturnsConfig()
    {
        $config = Config::getInstance();

        $this->assertInstanceOf('Sinergi\Gearman\Config', $config);
    }


    public function testGetInstanceReturnsTheSameInstance()
    {
        $config = Config::getInstance();

        $this->assertSame($config, Config::getInstance());
    }

    public function testDefaults()
    {
        $config = new Config();

        $this->assertFalse($config->getAutoUpdate());
        $this->assertNull($config->getBootstrap());
        $this->assertNull($config->getClass());
        $this->assertNull($config->getEnvVariables());
        $this->assertNull($config->getServer());
        $this->assertInternalType('array', $config->getServers());
        $this->assertCount(0, $config->getServers());
        $this->assertNull($config->getUser());
        $this->assertSame(0, $config->getWorkerLifetime());
    }

    public function testSetAndGet()
    {
        $config = new Config();
        $config->set($this->configValues);
        $this->assertEquals($this->configValues['auto_update'], $config->get('auto_update'));
    }

    public function testSettersGetters()
    {
        $config = new Config();

        $config->setBootstrap('test');
        $this->assertEquals('test', $config->getBootstrap());

        $config->setWorkerLifetime(6000);
        $this->assertEquals(6000, $config->getWorkerLifetime());

        $config->setAutoUpdate(true);
        $this->assertEquals(true, $config->getAutoUpdate());
    }
}
