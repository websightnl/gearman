<?php
namespace GearmanHandler;

use GearmanJob;

interface Job
{
    /**
     * @return string
     */
    static function getName();

    static function execute(GearmanJob $job);
}