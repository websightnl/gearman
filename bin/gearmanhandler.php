<?php

if(!function_exists("posix_kill")){
    trigger_error("The function posix_kill was not found. Please ensure POSIX functions are installed");
}

if(!function_exists("pcntl_fork")){
    trigger_error("The function pcntl_fork was not found. Please ensure Process Control functions are installed");
}

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

$options = getopt("c:");

if (!empty($options['c'])) {
    $configFile = realpath($options['c']);
} else {
    $configFile = GearmanHandler\Config::getPath();
}

if (null === $configFile || !file_exists($configFile)) {
    $file = GearmanHandler\Config::getFile();

    $directories = [
        getcwd(),
        getcwd() . DIRECTORY_SEPARATOR . 'config'
    ];

    foreach ($directories as $directory) {
        $configFile = $directory . DIRECTORY_SEPARATOR . (null !== $file ? $file : 'gearman.php');

        if (file_exists($configFile)) {
            $exists = true;
            break;
        }
    }
}

if ((!isset($exists) || !file_exists($configFile)) || !is_readable($configFile)) {
    echo 'Configuration file [' . $configFile . '] does not exists or does not have read permission.' . "\n";
    exit(1);
}

GearmanHandler\Config::setPath($configFile);
new GearmanHandler\Daemon();