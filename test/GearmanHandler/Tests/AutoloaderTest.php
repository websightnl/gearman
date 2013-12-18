<?php
namespace GearmanHandler\Tests;

use PHPUnit_Framework_TestCase;
use GearmanHandler\Autoloader;

class AutoloaderTest extends PHPUnit_Framework_TestCase
{
    public function testAutoload()
    {
        /*$declared = get_declared_classes();
        $declaredCount = count($declared);
        Autoloader::autoload('Foo');
        $this->assertEquals($declaredCount, count(get_declared_classes()), 'GearmanHandler\\Autoloader::autoload() is trying to load classes outside of the GearmanHandler namespace');
        Autoloader::autoload('GearmanHandler\\Daemon');
        $this->assertTrue(in_array('GearmanHandler\\Daemon', get_declared_classes()), 'GearmanHandler\\Autoloader::autoload() failed to autoload the GearmanHandler\\Daemon class');*/
    }
}
