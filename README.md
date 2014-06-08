Gearman
=======

[![Build Status](https://travis-ci.org/sinergi/gearman.svg?branch=master)](https://travis-ci.org/sinergi/gearman)

PHP library for dispatching, handling and managing Gearman Workers

_**Todo:** Add support for tasks, only jobs are handled right now._
_**Todo:** Tests are working but could cover more._

## Config

The library uses a Config class to share configuration between classes.

#### Example

```php
use Sinergi\Gearman\Config;

$config = (new Config())
    ->addServer(127.0.0.1, 4730)
    ->setUser('apache');
```

#### Example using array

```php
use Sinergi\Gearman\Config;

$config = new Config([
    'servers' => ['127.0.0.1:4730', '127.0.0.1:4731'],
    'user' => 'apache'
]);
```

#### Paramaters

 * __string__ ``bootstrap`` Path to the bootstrap file
 * __Server[]__ ``servers`` Collection of Gearman Servers
 * __string__ ``user`` The user under which the Gearman Workers will run
 * __bool__ ``autoUpdate`` Use for development only, automatically update workers before doing a job or task 

## Boostrap

File ``/path/to/bootstrap.php``

```php
use Sinergi\Gearman\Application;

$app = new Application();
$app->add(new JobExample());
$app->run();
```

## Job example

```php
use Sinergi\Gearman\JobInterface;
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

## Dispatcher usage

To send tasks and jobs to the Workers, use the Distpacher like this:

```php
use Sinergi\Gearman\Dispatcher;

$dispatcher = new Dispatcher($config);
$dispatcher->execute('JobExample', ['data' => 'value']);
```

## Start workers daemon

Starts the Workers as a daemon. You can use something like supervisord to make sure the Workers are always running.

#### Single server

```shell
php vendor/bin/gearman start --bootstrap="/path/to/bootstrap.php" --host="127.0.0.1" --port=4730
```

#### Multiple servers

```shell
php vendor/bin/gearman start --bootstrap="/path/to/bootstrap.php" --servers="127.0.0.1:4730,127.0.0.1:4731"
```

#### List of commands

 * `start`
 * `stop`
 * `restart`