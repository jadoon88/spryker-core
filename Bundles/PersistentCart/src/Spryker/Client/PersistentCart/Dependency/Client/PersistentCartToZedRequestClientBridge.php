<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\PersistentCart\Dependency\Client;

use Spryker\Shared\Kernel\Transfer\TransferInterface;

class PersistentCartToZedRequestClientBridge implements PersistentCartToZedRequestClientInterface
{
    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedRequestClient
     */
    public function __construct($zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @param string $url
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $object
     * @param array|null $requestOptions
     *
     * @return \Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function call($url, TransferInterface $object, $requestOptions = null): TransferInterface
    {
        return $this->zedRequestClient->call($url, $object, $requestOptions);
    }

    /**
     * @return void
     */
    public function addFlashMessagesFromLastZedRequest(): void
    {
        $this->zedRequestClient->addFlashMessagesFromLastZedRequest();
    }

    /**
     * @return void
     */
    public function addResponseMessagesToMessenger(): void
    {
        $this->zedRequestClient->addResponseMessagesToMessenger();
    }
}
