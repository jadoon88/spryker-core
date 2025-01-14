<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Client\MerchantStorage\Dependency\Client;

class MerchantStorageToStorageClientBridge implements MerchantStorageToStorageClientInterface
{
    /**
     * @var \Spryker\Client\Storage\StorageClientInterface
     */
    protected $storageClient;

    /**
     * @param \Spryker\Client\Storage\StorageClientInterface $storageClient
     */
    public function __construct($storageClient)
    {
        $this->storageClient = $storageClient;
    }

    /**
     * @param string $key
     *
     * @return array|null
     */
    public function get($key): ?array
    {
        return $this->storageClient->get($key);
    }

    /**
     * @param array<string> $keys
     *
     * @return array
     */
    public function getMulti(array $keys): array
    {
        return $this->storageClient->getMulti($keys);
    }
}
