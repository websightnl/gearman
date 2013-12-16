<?php
namespace Git\Tests;

use PHPUnit_Framework_TestCase;
use GearmanHandler\Autoloader;

class AutoloaderTest extends PHPUnit_Framework_TestCase
{
    public function testAutoload()
    {
        $this->assertNull(Autoloader::autoload('Foo'), 'GearmanHandler\\Autoloader::autoload() is trying to load classes outside of the GearmanHandler namespace');
        //$this->assertTrue(Autoloader::autoload('GearmanHandler'), 'GearmanHandler\\Autoloader::autoload() failed to autoload the GearmanHandler class');
    }
}
