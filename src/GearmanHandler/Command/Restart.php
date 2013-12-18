<?php
namespace GearmanHandler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use GearmanHandler\Process;
use GearmanHandler\Daemon;
use GearmanHandler\Config;

class Restart extends Command
{
    protected function configure()
    {
        $this->setName('restart')
            ->setDescription('Restart the gearman workers daemon')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Stoping gearman-handler: ');

        Process::stop();

        $output->write('OK', true);

        $output->write('Starting gearman-handler: ');

        if ($config = $input->getOption('config')) {
            Config::setConfigFile(realpath($config));
        }

        (new Daemon)->run();

        $output->write('OK', true);
    }
}