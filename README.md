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
    'jobs_dir' => '/path/to/jobs',
    'user' => 'apache'
];
```

## Create job

```php
class JobExample implements \GearmanHandler\JobInterface
{
    public function getName()
    {
        return 'JobExample';
    }

    public function execute(\GearmanJob $job)
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
$config = new GearmanHandler\Config([
    'gearman_host' => '127.0.0.1',
    'gearman_port' => 4730,
    'jobs_dir' => '/path/to/jobs'
]);
$worker = new GearmanHandler\Worker($config);
$worker->execute('JobExample', ['data' => 'value']);
```