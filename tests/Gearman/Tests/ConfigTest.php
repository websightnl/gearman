<?php
namespace Sinergi\Gearman\Tests;

use PHPUnit_Framework_TestCase;
use Sinergi\Gearman\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    private $configValues;

    public function setUp()
    {
        $this->configValues = require __DIR__ . '/_files/config.php';
    }

    public function testSingleton()
    {
        $config = Config::getInstance();
        $this->assertInstanceOf('Sinergi\Gearman\Config', $config);

        $config = new Config();
        $config->setUser('test1');

        $config = Config::getInstance();
        $this->assertInstanceOf('Sinergi\Gearman\Config', $config);
        $this->assertEquals('test1', $config->getUser());
    }

    public function testConstruct()
    {
        $config = new Config();
        $this->assertNull($config->getBootstrap());
        $this->assertFalse($config->getAutoUpdate());

        $config = new Config(['user' => 'test1']);
        $this->assertEquals('test1', $config->getUser());
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
