<?php
namespace Sinergi\Gearman;

interface BootstrapInterface
{
    public function run(Application $application);
}