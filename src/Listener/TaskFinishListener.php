<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\EventDispatcher\Listener;

use Mezzio\Swoole\Event\TaskFinishEvent;
use Psr\Log\LoggerInterface;

class TaskFinishListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(TaskFinishEvent $event): void
    {
        $this->logger->notice('Task {taskId} has finished', ['taskId' => $event->getTaskId()]);
    }
}
