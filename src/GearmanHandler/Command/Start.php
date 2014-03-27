<?php
namespace GearmanHandler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use GearmanHandler\Process;
use GearmanHandler\Daemon;
use GearmanHandler\Config;

class Start extends Command
{
    protected function configure()
    {
        $this->setName('start')
            ->setDescription('Start the gearman workers daemon')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Starting gearman-handler: ');

        $config = new Config;
        if ($configFile = $input->getOption('config')) {
            $configFile = realpath($configFile);
            if (is_file($configFile)) {
                $config->set(require $configFile);
            }
        }

        $process = new Process($config);
        if ($process->isRunning()) {
            $output->write('[ <error>Failed: Process is already runnning</error> ]', true);
            return;
        }

        (new Daemon($config))->run();

        $output->write('[ <fg=green>OK</fg=green> ]', true);
    }
}