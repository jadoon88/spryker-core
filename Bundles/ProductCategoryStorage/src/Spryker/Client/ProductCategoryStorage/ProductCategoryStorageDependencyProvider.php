<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductCategoryStorage;

use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;
use Spryker\Client\ProductCategoryStorage\Dependency\Client\ProductCategoryStorageToStorageClientBridge;
use Spryker\Client\ProductCategoryStorage\Dependency\Service\ProductCategoryStorageToSynchronizationServiceBridge;

/**
 * @method \Spryker\Client\ProductCategoryStorage\ProductCategoryStorageConfig getConfig()
 */
class ProductCategoryStorageDependencyProvider extends AbstractDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_STORAGE = 'CLIENT_STORAGE';

    /**
     * @var string
     */
    public const SERVICE_SYNCHRONIZATION = 'SERVICE_SYNCHRONIZATION';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container)
    {
        $container = $this->addStorageClient($container);
        $container = $this->addSynchronizationService($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function addStorageClient(Container $container)
    {
        $container->set(static::CLIENT_STORAGE, function (Container $container) {
            return new ProductCategoryStorageToStorageClientBridge($container->getLocator()->storage()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function addSynchronizationService(Container $container)
    {
        $container->set(static::SERVICE_SYNCHRONIZATION, function (Container $container) {
            return new ProductCategoryStorageToSynchronizationServiceBridge($container->getLocator()->synchronization()->service());
        });

        return $container;
    }
}
