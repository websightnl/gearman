<?php
namespace GearmanHandler;

use GearmanClient;

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
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->setConfig($config);
    }

    /**
     * @param string $name
     * @param mixed $data
     * @param int $priority
     * @param string $unique
     */
    public function background($name, $data = null, $priority = self::NORMAL, $unique = null)
    {
        switch ($priority) {
            case self::NORMAL:
                $this->getClient()->doBackground($name, self::serialize($data), $unique);
                break;
            case self::LOW:
                $this->getClient()->doLowBackground($name, self::serialize($data), $unique);
                break;
            case self::HIGH:
                $this->getClient()->doHighBackground($name, self::serialize($data), $unique);
                break;
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
        switch ($priority) {
            case self::NORMAL:
                $this->getClient()->doNormal($name, self::serialize($data), $unique);
                break;
            case self::LOW:
                $this->getClient()->doLow($name, self::serialize($data), $unique);
                break;
            case self::HIGH:
                $this->getClient()->doHigh($name, self::serialize($data), $unique);
                break;
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
}