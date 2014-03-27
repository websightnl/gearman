<?php
namespace GearmanHandler;

use GearmanWorker;
use React\EventLoop\Factory as Loop;
use React\EventLoop\StreamSelectLoop;
use React\EventLoop\LibEventLoop;
use Exception;
use Closure;

declare(ticks = 1);

class Daemon
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
    private $registeredJobs = [];

    /**
     * @param Config $config
     * @param StreamSelectLoop|LibEventLoop $loop
     * @param Process $process
     */
    public function __construct(Config $config = null, Process $process = null, $loop = null)
    {
        if (null !== $config) {
            $this->setConfig($config);
        }
        if (null !== $process) {
            $this->setProcess($process);
        }
        if ($loop instanceof StreamSelectLoop || $loop instanceof StreamSelectLoop) {
            $this->setLoop($loop);
        }
    }

    public function __destruct()
    {
        if (is_resource($this->lock)) {
            $this->getProcess()->release($this->lock);
        }
    }

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
            $this->createWorker();
            $this->registerJobs();
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

    public function signalHandlers()
    {
        $root = $this;
        pcntl_signal(SIGUSR1, function () use ($root) {
            $root->setKill(true);
        });
    }

    private function createWorker()
    {
        $this->worker = new GearmanWorker();
        $this->worker->addServer($this->getConfig()->getGearmanHost(), $this->getConfig()->getGearmanPort());
    }

    private function createLoop()
    {
        $worker = $this->worker;

        $worker->setTimeout(10);

        $callbacks = $this->getCallbacks();

        while ($worker->work() || $worker->returnCode() == GEARMAN_TIMEOUT) {
            if ($this->getKill()) {
                exit;
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
    }

    /**
     * @return array
     */
    public function getRegisteredJobs()
    {
        return $this->registeredJobs;
    }

    /**
     * @param null|string $dir
     * @throws \Exception
     */
    private function registerJobs($dir = null)
    {
        if (null === $dir) {
            $dir = $this->getConfig()->getJobsDir();
        }

        if (null !== $dir && is_dir($dir)) {
            foreach (scandir($dir) as $file) {
                if ($file !== '.' && $file !== '..' && is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                    $file = $dir . DIRECTORY_SEPARATOR . $file;
                    $this->registerJobs($file);
                } elseif (strtolower(substr($file, -4)) === '.php') {
                    $file = $dir . DIRECTORY_SEPARATOR . $file;
                    $className = $this->getClassNameFromFile($file);
                    $className = (!empty($className[0]) ? $className[0] . '\\' : '') . $className[1];

                    require_once $file;

                    $class = new $className;

                    if (!$class instanceof JobInterface) {
                        throw new Exception('Class ' . $className . ' does not implements GearmanHandler\\JobInterface interface');
                    } else {
                        $this->registeredJobs[] = $className;
                        $this->worker->addFunction($class->getName(), [$className, 'execute']);
                    }
                }
            }
        }
    }

    /**
     * @param string $file
     * @return array
     */
    private function getClassNameFromFile($file)
    {
        $fileContent = file_get_contents($file);
        $class = $namespace = '';
        $i = 0;
        $tokens = token_get_all($fileContent);
        for (; $i < count($tokens); $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= '\\' . $tokens[$j][1];
                    } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j] === '{') {
                        $class = $tokens[$i + 2][1];
                    }
                }
            }
        }
        return [$namespace, $class];
    }

    /**
     * @param callable $callback
     */
    public function addCallback(Closure $callback)
    {
        $this->callbacks[] = $callback;
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