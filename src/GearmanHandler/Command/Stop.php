<?php
namespace GearmanHandler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GearmanHandler\Process;

class Stop extends Command
{
    protected function configure()
    {
        $this->setName('stop')
            ->setDescription('Stop the gearman workers daemon');
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
    }
}