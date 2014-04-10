<?php
namespace GearmanHandler;

use GearmanWorker;
use React\EventLoop\Factory as Loop;
use React\EventLoop\StreamSelectLoop;
use React\EventLoop\LibEventLoop;
use Exception;
use Closure;

declare(ticks = 1);

class Application
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var array
     */
    private $callbacks = [];

    /**
     * @var StreamSelectLoop|LibEventLoop
     */
    private $loop;

    /**
     * @var bool|resource
     */
    private $lock = false;

    /**
     * @var bool
     */
    private $kill = false;

    /**
     * @var GearmanWorker
     */
    private $worker;

    /**
     * @var array
     */
    private $jobs = [];

    /**
     * @param Config $config
     * @param StreamSelectLoop|LibEventLoop $loop
     * @param Process $process
     */
    public function __construct(Config $config = null, Process $process = null, $loop = null)
    {
        if (null === $config) {
            $config = Config::getInstance();
        }
        $this->setConfig($config);

        if (null !== $process) {
            $this->setProcess($process);
        }
        if ($loop instanceof StreamSelectLoop || $loop instanceof StreamSelectLoop) {
            $this->setLoop($loop);
        }
        $this->createWorker();
    }

    public function __destruct()
    {
        if (is_resource($this->lock)) {
            $this->getProcess()->release($this->lock);
        }
    }

    /**
     * @param bool $fork
     * @throws \Exception
     */
    public function run($fork = true)
    {
        $pidFile = $this->getProcess()->getPidFile();
        $lockFile = $this->getProcess()->getLockFile();
        if (is_file($pidFile) && is_writable($pidFile)) {
            unlink($pidFile);
        }
        if (is_file($lockFile) && is_writable($lockFile)) {
            unlink($lockFile);
        }

        $user = $this->getConfig()->getUser();
        if ($user) {
            $user = posix_getpwnam($user);
            posix_setgid($user['gid']);
            posix_setuid($user['uid']);
            if (posix_geteuid() != $user['uid']) {
                throw new Exception("Unable to change user to {$user['uid']}");
            }
        }

        if ($fork) {
            $pid = pcntl_fork();
        }

        if (!$fork || (isset($pid) && $pid !== -1 && !$pid)) {
            $this->getProcess()->setPid(posix_getpid());

            if (isset($pid) && $pid !== -1 && !$pid) {
                $parantPid = posix_getppid();
                if ($parantPid) {
                    posix_kill(posix_getppid(), SIGUSR2);
                }
            }

            $this->lock = $this->getProcess()->lock();
            $this->signalHandlers();
            $this->createLoop();
        } elseif ($fork && isset($pid) && $pid) {
            $wait = true;

            pcntl_signal(SIGUSR2, function () use (&$wait) {
                $wait = false;
            });

            while ($wait) {
                pcntl_waitpid($pid, $status, WNOHANG);
                pcntl_signal_dispatch();
            }
        }
    }

    /**
     * @return $this
     */
    private function signalHandlers()
    {
        $root = $this;
        pcntl_signal(SIGUSR1, function () use ($root) {
            $root->setKill(true);
        });
        return $this;
    }

    /**
     * @return $this
     */
    private function createWorker()
    {
        $this->worker = new GearmanWorker();
        $this->worker->addServer($this->getConfig()->getGearmanHost(), $this->getConfig()->getGearmanPort());
        return $this;
    }

    /**
     * @return $this
     */
    private function createLoop()
    {
        $worker = $this->worker;

        $worker->setTimeout(10);

        $callbacks = $this->getCallbacks();

        while ($worker->work() || $worker->returnCode() == GEARMAN_TIMEOUT) {
            if ($this->getKill()) {
                break;
            }

            pcntl_signal_dispatch();

            if (count($callbacks)) {
                foreach ($callbacks as $callback) {
                    $callback($this);
                }
            }

            usleep(50000);

            if ($worker->returnCode() === GEARMAN_TIMEOUT) {
                continue;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @param JobInterface $job
     * @return $this
     */
    public function add(JobInterface $job)
    {
        $this->jobs[] = $job;
        $this->worker->addFunction($job->getName(), [$job, 'execute']);
        return $this;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function addCallback(Closure $callback)
    {
        $this->callbacks[] = $callback;
        return $this;
    }

    /**
     * @return array
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * @param StreamSelectLoop|LibEventLoop $loop
     * @return $this
     */
    public function setLoop($loop)
    {
        $this->loop = $loop;
        return $this;
    }

    /**
     * @return LibEventLoop|StreamSelectLoop
     */
    public function getLoop()
    {
        if (null === $this->loop) {
            $this->setLoop(Loop::create());
        }
        return $this->loop;
    }

    /**
     * @return bool
     */
    public function getKill()
    {
        return $this->kill;
    }

    /**
     * @param $kill
     * @return $this
     */
    public function setKill($kill)
    {
        $this->kill = $kill;
        return $this;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if (null === $this->config) {
            $this->createConfig();
        }
        return $this->config;
    }

    /**
     * @return $this
     */
    private function createConfig()
    {
        return $this->setConfig(new Config);
    }

    /**
     * @param Process $process
     * @return $this
     */
    public function setProcess(Process $process)
    {
        $this->process = $process;
        return $this;
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        if (null === $this->process) {
            $this->createProcess();
        }
        return $this->process;
    }

    /**
     * @return $this
     */
    private function createProcess()
    {
        return $this->setProcess(new Process($this->getConfig()));
    }
}