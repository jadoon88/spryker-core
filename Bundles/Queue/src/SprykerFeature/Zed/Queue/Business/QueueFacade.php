<?php

namespace SprykerFeature\Zed\Queue\Business;

use Generated\Shared\Queue\QueueMessageInterface;
use SprykerEngine\Zed\Kernel\Business\AbstractFacade;
use SprykerEngine\Shared\Kernel\Messenger\MessengerInterface;

/**
 * @method QueueDependencyContainer getDependencyContainer()
 */
class QueueFacade extends AbstractFacade
{

    /**
     * @param string $queueName
     * @param QueueMessageInterface $queueMessage
     */
    public function publishMessage($queueName, QueueMessageInterface $queueMessage)
    {
        $this->getDependencyContainer()
            ->createQueueConnection($queueName)
            ->publish($queueMessage)
        ;
    }

    /**
     * @param string $queueName
     * @param MessengerInterface $messenger
     * @param int $timeout
     * @param int $fetchSize
     */
    public function startWorker(
        $queueName,
        MessengerInterface $messenger,
        $timeout = 10,
        $fetchSize = 10
    ) {
        $this->getDependencyContainer()
            ->createTaskWorker($queueName, $messenger)
            ->work($timeout, $fetchSize)
        ;
    }
}
