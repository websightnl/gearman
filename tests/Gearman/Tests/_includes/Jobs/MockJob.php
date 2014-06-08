<?php
namespace Sinergi\Gearman\Tests\Jobs;

use Sinergi\Gearman\JobInterface;
use GearmanJob;

class MockJob implements JobInterface
{
    public function getName()
    {
        return 'MockJob';
    }

    public function execute(GearmanJob $job = null)
    {
    }
}