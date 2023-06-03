<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\EventDispatcher\Swoole;

use Laminas\ServiceManager\ServiceManager;
use Mezzio\Swoole\Event\SwooleListenerProvider;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Psr\Container\ContainerInterface;

use function Shlinkio\Shlink\EventDispatcher\lazyListener;
use function Shlinkio\Shlink\EventDispatcher\resolveEnabledListenerChecker;

/**
 * @deprecated To be removed with Shlink 4.0.0
 */
class SwooleListenersProviderDelegator
{
    public function __invoke(ContainerInterface $container, string $s, callable $factory): SwooleListenerProvider
    {
        /** @var SwooleListenerProvider $provider */
        $provider = $factory();
        $asyncEvents = $container->get('config')['events']['async'] ?? [];
        $checker = resolveEnabledListenerChecker($container);

        foreach ($asyncEvents as $eventName => $listeners) {
            foreach ($listeners as $listenerName) {
                if (! $checker->shouldRegisterListener($eventName, $listenerName, true)) {
                    continue;
                }

                $provider->addListener($eventName, lazyListener($container, $listenerName));
                /** @var ServiceManager $container */
                $container->addDelegator($listenerName, DeferredServiceListenerDelegator::class);
            }
        }

        return $provider;
    }
}
