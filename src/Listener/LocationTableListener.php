<?php

namespace Youbuwei\IPLocation\Listener;

use Hyperf\Contract\ContainerInterface;
use Hyperf\Framework\Event\BootApplication;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Youbuwei\IPLocation\LocalLocationTable;

class LocationTableListener implements ListenerInterface
{
    protected static bool $IS_LOADED = false;

    public function __construct(
        protected ContainerInterface $container
    ) {
    }

    /**
     * @return string[]
     */
    public function listen(): array
    {
        return [
            BootApplication::class
        ];
    }

    /**
     * @param object $event
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(object $event): void
    {
        /**
         * @var $localLocation LocalLocationTable
         */
        $localLocation = $this->container->get(LocalLocationTable::class);
        if (self::$IS_LOADED === false) {
            $localLocation->loadData();
            self::$IS_LOADED = true;
        }
    }
}