PHP Gearman Handler
===================

[![Build Status](https://travis-ci.org/gabrielbull/php-gearman-handler.svg?branch=master)](https://travis-ci.org/gabrielbull/php-gearman-handler)

Better doc will be coming.

## Boostrap

File ``/path/to/bootstrap.php``

```php
use GearmanHandler\Application;

$app = new Application();
$app->add(new JobExample());
$app->run();
```

## Create job

```php
use GearmanHandler\JobInterface;
use GearmanJob;

class JobExample implements JobInterface
{
    public function getName()
    {
        return 'JobExample';
    }

    public function execute(GearmanJob $job)
    {
        // To something
    }
}
```

## Start workers daemon

```shell
php vendor/bin/gearman start --bootstrap="/path/to/bootstrap.php" --host="127.0.0.1" --port=4730
```

## Worker usage

```php
use GearmanHandler\Config;
use GearmanHandler\Worker;

$config = new Config([
    'gearman_host' => '127.0.0.1',
    'gearman_port' => 4730,
    'jobs_dir' => '/path/to/jobs'
]);
$worker = new Worker($config);
$worker->execute('JobExample', ['data' => 'value']);
```