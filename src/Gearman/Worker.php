<?php
namespace Sinergi\Gearman;

use GearmanException;
use GearmanWorker;
use Psr\Log\LoggerInterface;
use Sinergi\Gearman\Exception\ServerConnectionException;

class Worker
{
    /**
     * @var GearmanWorker
     */
    private $worker;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Config $config
     * @param null|LoggerInterface $logger
     * @throws ServerConnectionException
     */
    public function __construct(Config $config, LoggerInterface $logger = null)
    {
        $this->setConfig($config);
        if (null !== $logger) {
            $this->logger = $logger;
        }

        $this->worker = new GearmanWorker();
        $servers = $this->getConfig()->getServers();
        $exceptions = [];
        foreach ($servers as $server) {
            try {
                $this->worker->addServer($server->getHost(), $server->getPort());
            } catch (GearmanException $e) {
                $message = 'Unable to connect to Gearman Server ' . $server->getHost() . ':' . $server->getPort();
                if (null !== $this->logger) {
                    $this->logger->info($message);
                }
                $exceptions[] = $message;
            }
        }

        if (count($exceptions)) {
            foreach ($exceptions as $exception) {
                throw new ServerConnectionException($exception);
            }
        }
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
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
     * @return GearmanWorker
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @param GearmanWorker $worker
     * @return $this
     */
    public function setWorker(GearmanWorker $worker)
    {
        $this->worker = $worker;
        return $this;
    }
}