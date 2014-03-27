<?php
namespace GearmanHandler\Tests\Commands;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use GearmanHandler\Command\Start as StartCommand;

class StartTest extends PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new StartCommand());

        $command = $application->find('start');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);
    }
}