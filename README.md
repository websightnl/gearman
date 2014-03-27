PHP Gearman Handler
===================

[![Build Status](https://travis-ci.org/gabrielbull/php-gearman-handler.svg)](https://travis-ci.org/gabrielbull/php-gearman-handler)

Better doc will be coming.

## Config

File ``/path/to/config.php``

```php
return [
    'gearman_host' => '127.0.0.1',
    'gearman_port' => 4730,
    'worker_dir' => '/path/to/workers',
    'user' => 'apache'
];
```

## Create worker

```php
class WorkerExample implements \GearmanHandler\Job
{
    public static function getName()
    {
        return 'WorkerExample';
    }

    public static function execute(\GearmanJob $job)
    {
        // To something
    }
}
```

## Start workers daemon

```shell
php vendor/bin/gearman start -c /path/to/config.php
```

## Worker usage

```php
GearmanHandler\Config::setConfigFile('/path/to/config.php');
GearmanHandler\Worker::execute('WorkerName', ['data' => 'value']);
```