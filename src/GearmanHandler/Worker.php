<?php
namespace GearmanHandler;

use GearmanClient;

class Worker
{
    const NORMAL = 0;
    const LOW = 1;
    const HIGH = 2;

    /** @var \GearmanClient $client */
    private static $client;

    /**
     * @param GearmanClient|null $client
     */
    public static function setClient(GearmanClient $client = null)
    {
        if (null === $client) {
            self::$client = new GearmanClient;
        }

        self::$client->addServer(Config::getGearmanHost(), Config::getGearmanPort());
    }

    /**
     * @param string $name
     * @param mixed $data
     * @param int $priority
     * @param string $unique
     */
    public static function background($name, $data = null, $priority = self::NORMAL, $unique = null)
    {
        if (null === self::$client) {
            self::setClient();
        }

        switch($priority) {
            case self::NORMAL:
                self::$client->doBackground($name, self::serialize($data), $unique);
                break;
            case self::LOW:
                self::$client->doLowBackground($name, self::serialize($data), $unique);
                break;
            case self::HIGH:
                self::$client->doHighBackground($name, self::serialize($data), $unique);
                break;
        }
    }

    /**
     * @param string $name
     * @param mixed $data
     * @param int $priority
     * @param string $unique
     */
    public static function execute($name, $data = null, $priority = self::NORMAL, $unique = null)
    {
        if (null === self::$client) {
            self::setClient();
        }

        switch($priority) {
            case self::NORMAL:
                self::$client->doNormal($name, self::serialize($data), $unique);
                break;
            case self::LOW:
                self::$client->doLow($name, self::serialize($data), $unique);
                break;
            case self::HIGH:
                self::$client->doHigh($name, self::serialize($data), $unique);
                break;
        }
    }

    /**
     * @param mixed $data
     * @return string
     */
    private static function serialize($data = [])
    {
        return serialize($data);
    }
}