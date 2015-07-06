<?php
namespace Sinergi\Gearman;

use GearmanJob;

interface JobInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param GearmanJob|null $job
     * @return mixed
     */
    public function execute(GearmanJob $job = null);
}
