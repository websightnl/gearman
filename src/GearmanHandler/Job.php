<?php
namespace GearmanHandler;

use GearmanJob;

interface Job
{
    function execute(GearmanJob $job);
}