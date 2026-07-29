<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\EventDispatcher\Dispatcher;

use League\Event\EventDispatcher;
use League\Event\PrioritizedListenerRegistry;
use Psr\Container\ContainerInterface;

use function Shlinkio\Shlink\EventDispatcher\lazyListener;
use function Shlinkio\Shlink\EventDispatcher\resolveEnabledListenerChecker;

class SyncEventDispatcherFactory
{
    public const SYNC_DISPATCHER = __NAMESPACE__ . '\SyncEventDispatcher';

    public function __invoke(ContainerInterface $container): EventDispatcher
    {
        $provider = new PrioritizedListenerRegistry();
        /** @var array $eventsConfig */
        $eventsConfig = $container->get('config')['events'] ?? [];
        /** @var array<string, string[]> $regularEvents */
        $regularEvents = $eventsConfig['regular'] ?? [];

        $this->registerEvents($provider, $container, $regularEvents);

        return new EventDispatcher($provider);
    }

    /**
     * @param array<string, string[]> $events
     */
    private function registerEvents(
        PrioritizedListenerRegistry $provider,
        ContainerInterface $container,
        array $events,
    ): void {
        $checker = resolveEnabledListenerChecker($container);

        foreach ($events as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                if (!$checker->shouldRegisterListener($eventName, $listener, false)) {
                    continue;
                }

                $provider->subscribeTo($eventName, lazyListener($container, $listener));
            }
        }
    }
}
