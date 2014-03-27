<?php
namespace GearmanHandler;

use GearmanJob;

interface JobInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param GearmanJob $job
     * @return mixed
     */
    public function execute(GearmanJob $job);
}