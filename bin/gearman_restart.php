<?php

if (!function_exists("posix_kill")) {
    trigger_error("The function posix_kill was not found. Please ensure POSIX functions are installed", E_USER_ERROR);
}

if (!function_exists("pcntl_fork")) {
    trigger_error("The function pcntl_fork was not found. Please ensure Process Control functions are installed", E_USER_ERROR);
}

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
}

use Sinergi\Gearman\Application;
use Sinergi\Gearman\Process;

if (isset($_SERVER['argv'][1])) {
    $serialized = $_SERVER['argv'][1];

    if (is_file($serialized)) {
        $application = file_get_contents($serialized);
        if (!empty($application)) {
            $application = unserialize($application);
        }
        unlink($serialized);
    }

    if (!$application instanceof Application) {
        $application = new Application();
    }

    $process = $application->getProcess();

    unlink($process->getPidFile());
    $process->release();

    $int = 0;
    while ($int < 1000) {
        if (file_exists($process->getPidFile())) {
            usleep(1000);
            $int++;
        } elseif (file_exists($process->getLockFile())) {
            $process->release();
            usleep(1000);
            $int++;
        } else {
            $int = 1000;
        }
    }

    $application->setProcess(new Process($application->getConfig(), $application->getLogger()));
    $application->run(false, true);
}