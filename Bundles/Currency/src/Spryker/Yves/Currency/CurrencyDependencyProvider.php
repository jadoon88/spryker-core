<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Currency;

use Spryker\Shared\Currency\Dependency\Internationalization\CurrencyToInternationalizationBridge;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Currency\Dependency\Client\CurrencyToMessengerClientBridge;
use Spryker\Yves\Currency\Dependency\Client\CurrencyToSessionBridge;
use Spryker\Yves\Currency\Dependency\Client\CurrencyToZedRequestClientBridge;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;

class CurrencyDependencyProvider extends AbstractBundleDependencyProvider
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
    public const CLIENT_MESSENGER = 'CLIENT_MESSENGER';

    /**
     * @var string
     */
    public const PLUGINS_CURRENCY_POST_CHANGE = 'CURRENCY_POST_CHANGE_PLUGINS';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container = $this->addStore($container);
        $container = $this->addInternationalization($container);
        $container = $this->addSessionClient($container);
        $container = $this->addCurrencyPostChangePlugins($container);
        $container = $this->addZedRequestClient($container);
        $container = $this->addMessengerClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addStore(Container $container)
    {
        $container->set(static::STORE, function () {
            return Store::getInstance();
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addInternationalization(Container $container)
    {
        $container->set(static::INTERNATIONALIZATION, function () {
            return new CurrencyToInternationalizationBridge();
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addSessionClient(Container $container)
    {
        $container->set(static::CLIENT_SESSION, function (Container $container) {
            return new CurrencyToSessionBridge($container->getLocator()->session()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addZedRequestClient(Container $container)
    {
        $container->set(static::CLIENT_ZED_REQUEST, function (Container $container) {
            return new CurrencyToZedRequestClientBridge($container->getLocator()->zedRequest()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addMessengerClient(Container $container)
    {
        $container->set(static::CLIENT_MESSENGER, function (Container $container) {
            return new CurrencyToMessengerClientBridge($container->getLocator()->messenger()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCurrencyPostChangePlugins(Container $container)
    {
        $container->set(static::PLUGINS_CURRENCY_POST_CHANGE, function () {
            return $this->getCurrencyPostChangePlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Yves\Currency\Dependency\CurrencyPostChangePluginInterface>
     */
    protected function getCurrencyPostChangePlugins()
    {
        return [];
    }
}
