<?php
namespace GearmanHandler\Tests\Workers;

use GearmanJob;
use GearmanHandler\Job;

class CreateFile implements Job
{
    public static function getName()
    {
        return 'CreateFile';
    }

    public static function execute(GearmanJob $job)
    {
        file_put_contents(__DIR__ . '/../worker_test', 'true');
    }
}