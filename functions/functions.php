<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\EventDispatcher;

use Psr\Container\ContainerInterface;
use Shlinkio\Shlink\EventDispatcher\Listener\DummyEnabledListenerChecker;
use Shlinkio\Shlink\EventDispatcher\Listener\EnabledListenerCheckerInterface;
use Shlinkio\Shlink\EventDispatcher\Util\RequestIdProviderInterface;
use Spiral\RoadRunner\Jobs\JobsInterface;

function lazyListener(ContainerInterface $container, string $listenerServiceName): callable
{
    return new Listener\LazyEventListener($container, $listenerServiceName);
}

function roadRunnerTaskListener(
    JobsInterface $jobs,
    string $listenerServiceName,
    RequestIdProviderInterface $requestIdProvider,
): callable {
    return new RoadRunner\RoadRunnerTaskListener($jobs, $listenerServiceName, $requestIdProvider);
}

function resolveEnabledListenerChecker(ContainerInterface $container): EnabledListenerCheckerInterface
{
    if (! $container->has(EnabledListenerCheckerInterface::class)) {
        return new DummyEnabledListenerChecker();
    }

    $checker = $container->get(EnabledListenerCheckerInterface::class);
    if ($checker instanceof EnabledListenerCheckerInterface) {
        return $checker;
    }

    return new DummyEnabledListenerChecker();
}
