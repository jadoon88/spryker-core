<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Currency;

use Spryker\Client\Currency\Dependency\Client\CurrencyToSessionBridge;
use Spryker\Client\Currency\Dependency\Client\CurrencyToStoreClientBridge;
use Spryker\Client\Currency\Dependency\Client\CurrencyToZedRequestClientBridge;
use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;
use Spryker\Shared\Currency\Dependency\Internationalization\CurrencyToInternationalizationBridge;
use Spryker\Shared\Kernel\Store;

class CurrencyDependencyProvider extends AbstractDependencyProvider
{
    /**
     * @var string
     */
    public const STORE = 'store';

    /**
     * @var string
     */
    public const INTERNATIONALIZATION = 'internationalization';

    /**
     * @var string
     */
    public const CLIENT_SESSION = 'CLIENT_SESSION';

    /**
     * @var string
     */
    public const CLIENT_ZED_REQUEST = 'CLIENT_ZED_REQUEST';

    /**
     * @var string
     */
    public const CLIENT_STORE = 'CLIENT_STORE';

    /**
     * @var string
     */
    public const PLUGINS_CURRENCY_POST_CHANGE = 'PLUGINS_CURRENCY_POST_CHANGE';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container)
    {
        $container = $this->addCurrencyPostChangePlugins($container);
        $container = $this->addStore($container);
        $container = $this->addInternationalization($container);
        $container = $this->addSessionClient($container);
        $container = $this->addZedRequestClient($container);
        $container = $this->addStoreClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addSessionClient(Container $container)
    {
        $container->set(static::CLIENT_SESSION, function (Container $container) {
            return new CurrencyToSessionBridge($container->getLocator()->session()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addStore(Container $container)
    {
        $container->set(static::STORE, function () {
            return Store::getInstance();
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addInternationalization(Container $container)
    {
        $container->set(static::INTERNATIONALIZATION, function () {
            return new CurrencyToInternationalizationBridge();
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addZedRequestClient(Container $container): Container
    {
        $container->set(static::CLIENT_ZED_REQUEST, function (Container $container) {
            return new CurrencyToZedRequestClientBridge($container->getLocator()->zedRequest()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addStoreClient(Container $container): Container
    {
        $container->set(static::CLIENT_STORE, function (Container $container) {
            return new CurrencyToStoreClientBridge($container->getLocator()->store()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addCurrencyPostChangePlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_CURRENCY_POST_CHANGE, function () {
            return $this->getCurrencyPostChangePlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Client\CurrencyExtension\Dependency\CurrencyPostChangePluginInterface>
     */
    protected function getCurrencyPostChangePlugins(): array
    {
        return [];
    }
}
