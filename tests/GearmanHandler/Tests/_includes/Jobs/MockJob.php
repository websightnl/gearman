<?php
namespace GearmanHandler\Tests\Jobs;

use GearmanHandler\JobInterface;
use GearmanJob;

class MockJob implements JobInterface
{
    public function getName()
    {
        return 'MockJob';
    }

    public function execute(GearmanJob $job)
    {
    }
}