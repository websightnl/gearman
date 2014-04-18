<?php
namespace GearmanHandler;

use GearmanClient;
use Psr\Log\LoggerInterface;

class Worker
{
    const NORMAL = 0;
    const LOW = 1;
    const HIGH = 2;

    /**
     * @var GearmanClient
     */
    private $client;

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
     * @param LoggerInterface|null $logger
     */
    public function __construct(Config $config, LoggerInterface $logger = null)
    {
        $this->setConfig($config);
        if (null !== $logger) {
            $this->setLogger($logger);
        }
    }

    /**
     * @param string $name
     * @param mixed $data
     * @param int $priority
     * @param string $unique
     */
    public function background($name, $data = null, $priority = self::NORMAL, $unique = null)
    {
        if (null !== $this->logger) {
            $this->logger->debug("Execute background job \"{$name}\" to GearmanClient");
        }

        $jobHandle = null;
        switch ($priority) {
            case self::NORMAL:
                $jobHandle = $this->getClient()->doBackground($name, self::serialize($data), $unique);
                break;
            case self::LOW:
                $jobHandle = $this->getClient()->doLowBackground($name, self::serialize($data), $unique);
                break;
            case self::HIGH:
                $jobHandle = $this->getClient()->doHighBackground($name, self::serialize($data), $unique);
                break;
        }

        if (null !== $this->logger) {
            $this->logger->debug("Job handle for \"{$name}\" is {$jobHandle}");
        }
    }

    /**
     * @param string $name
     * @param mixed $data
     * @param int $priority
     * @param string $unique
     */
    public function execute($name, $data = null, $priority = self::NORMAL, $unique = null)
    {
        if (null !== $this->logger) {
            $this->logger->debug("Execute job \"{$name}\" to GearmanClient");
        }

        $jobHandle = null;
        switch ($priority) {
            case self::NORMAL:
                $jobHandle = $this->getClient()->doNormal($name, self::serialize($data), $unique);
                break;
            case self::LOW:
                $jobHandle = $this->getClient()->doLow($name, self::serialize($data), $unique);
                break;
            case self::HIGH:
                $jobHandle = $this->getClient()->doHigh($name, self::serialize($data), $unique);
                break;
        }

        if (null !== $this->logger) {
            $this->logger->debug("Job handle for \"{$name}\" is {$jobHandle}");
        }
    }

    /**
     * @param mixed $data
     * @return string
     */
    private function serialize($data = [])
    {
        return serialize($data);
    }

    /**
     * @param GearmanClient|null $client
     * @return $this
     */
    public function setClient(GearmanClient $client = null)
    {
        if (null !== $this->logger) {
            $this->logger->debug("Added GearmanClient server {$this->getConfig()->getGearmanHost()}:{$this->getConfig()->getGearmanPort()}");
        }

        $client->addServer($this->getConfig()->getGearmanHost(), $this->getConfig()->getGearmanPort());
        $this->client = $client;
        return $this;
    }

    /**
     * @return GearmanClient|null
     */
    public function getClient()
    {
        if (null === $this->client) {
            $this->createClient();
        }
        return $this->client;
    }

    /**
     * @return $this
     */
    private function createClient()
    {
        return $this->setClient(new GearmanClient);
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
        return $this->config;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
}