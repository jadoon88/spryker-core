<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Store\Business;

use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Store\Configuration\StoreConfigurationProvider;
use Spryker\Shared\Store\Configuration\StoreConfigurationReader;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Store\Business\Model\StoreMapper;
use Spryker\Zed\Store\Business\Model\StoreReader;

/**
 * @method \Spryker\Zed\Store\StoreConfig getConfig()
 * @method \Spryker\Zed\Store\Persistence\StoreQueryContainerInterface getQueryContainer()
 */
class StoreBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Spryker\Zed\Store\Business\Model\StoreReaderInterface
     */
    public function createStoreReader()
    {
        return new StoreReader(
            $this->createStoreConfigurationProvider(),
            $this->getQueryContainer(),
            $this->createStoreMapper()
        );
    }

    /**
     * @return \Spryker\Zed\Store\Business\Model\StoreMapperInterface
     */
    protected function createStoreMapper()
    {
        return new StoreMapper($this->createStoreConfigurationReader());
    }

    /**
     * @return \Spryker\Shared\Store\Configuration\StoreConfigurationReaderInterface
     */
    protected function createStoreConfigurationReader()
    {
        return new StoreConfigurationReader($this->createStoreConfigurationProvider());
    }

    /**
     * @return \Spryker\Shared\Store\Configuration\StoreConfigurationProviderInterface
     */
    protected function createStoreConfigurationProvider()
    {
        return new StoreConfigurationProvider($this->getStore());
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     */
    protected function getStore()
    {
        return Store::getInstance();
    }
}
