<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsGui;

use Spryker\Zed\CmsGui\Communication\Exception\MissingStoreRelationFormTypePluginException;
use Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToCmsBridge;
use Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToCmsGlossaryFacadeBridge;
use Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToLocaleBridge;
use Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToUrlBridge;
use Spryker\Zed\CmsGui\Dependency\QueryContainer\CmsGuiToCmsQueryContainerBridge;
use Spryker\Zed\CmsGui\Dependency\Service\CmsGuiToUtilEncodingBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Communication\Form\FormTypeInterface;
use Spryker\Zed\Kernel\Communication\Plugin\Pimple;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\CmsGui\CmsGuiConfig getConfig()
 */
class CmsGuiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_LOCALE = 'locale facade';

    /**
     * @var string
     */
    public const FACADE_CMS = 'locale cms';

    /**
     * @var string
     */
    public const FACADE_URL = 'url facade';

    /**
     * @var string
     */
    public const FACADE_GLOSSARY = 'glossary facade';

    /**
     * @var string
     */
    public const FACADE_CMS_CONTENT_WIDGET = 'content widget facade';

    /**
     * @var string
     */
    public const QUERY_CONTAINER_CMS = 'cms query container';

    /**
     * @var string
     */
    public const SERVICE_UTIL_ENCODING = 'util encoding service';

    /**
     * @var string
     */
    public const TWIG_ENVIRONMENT = 'twig environment';

    /**
     * @uses \Spryker\Zed\Twig\Communication\Plugin\Application\TwigApplicationPlugin::SERVICE_TWIG
     *
     * @var string
     */
    public const SERVICE_TWIG = 'twig';

    /**
     * @var string
     */
    public const PLUGINS_CMS_PAGE_TABLE_EXPANDER = 'PLUGINS_CMS_PAGE_TABLE_EXPANDER';

    /**
     * @var string
     */
    public const PLUGINS_CREATE_GLOSSARY_EXPANDER = 'PLUGINS_CREATE_GLOSSARY_EXPANDER';

    /**
     * @var string
     */
    public const PLUGIN_STORE_RELATION_FORM_TYPE = 'PLUGIN_STORE_RELATION_FORM_TYPE';

    /**
     * @var string
     */
    public const PLUGINS_CMS_GLOSSARY_BEFORE_SAVE = 'PLUGINS_CMS_GLOSSARY_BEFORE_SAVE';

    /**
     * @var string
     */
    public const PLUGINS_CMS_GLOSSARY_AFTER_FIND = 'PLUGINS_CMS_GLOSSARY_AFTER_FIND';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container->set(static::FACADE_LOCALE, function (Container $container) {
            return new CmsGuiToLocaleBridge($container->getLocator()->locale()->facade());
        });

        $container->set(static::FACADE_CMS, function (Container $container) {
            return new CmsGuiToCmsBridge($container->getLocator()->cms()->facade());
        });

        $container->set(static::FACADE_GLOSSARY, function (Container $container) {
            return new CmsGuiToCmsGlossaryFacadeBridge($container->getLocator()->glossary()->facade());
        });

        $container->set(static::QUERY_CONTAINER_CMS, function (Container $container) {
            return new CmsGuiToCmsQueryContainerBridge($container->getLocator()->cms()->queryContainer());
        });

        $container->set(static::FACADE_URL, function (Container $container) {
            return new CmsGuiToUrlBridge($container->getLocator()->url()->facade());
        });

        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new CmsGuiToUtilEncodingBridge($container->getLocator()->utilEncoding()->service());
        });

        $container->set(static::TWIG_ENVIRONMENT, function (Container $container) {
            return $container->getApplicationService(static::SERVICE_TWIG);
        });

        $container = $this->addCmsPageTableExpanderPlugins($container);
        $container = $this->addCreateGlossaryExpanderPlugins($container);
        $container = $this->addStoreRelationFormTypePlugin($container);
        $container = $this->addCmsGlossaryBeforeSavePlugins($container);
        $container = $this->addCmsGlossaryAfterFindPlugins($container);

        return $container;
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return \Twig\Environment
     */
    protected function getTwigEnvironment()
    {
        $pimplePlugin = new Pimple();

        return $pimplePlugin->getApplication()['twig'];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCmsPageTableExpanderPlugins(Container $container)
    {
        $container->set(static::PLUGINS_CMS_PAGE_TABLE_EXPANDER, function (Container $container) {
            return $this->getCmsPageTableExpanderPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\CmsGui\Dependency\Plugin\CmsPageTableExpanderPluginInterface>
     */
    protected function getCmsPageTableExpanderPlugins()
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCreateGlossaryExpanderPlugins(Container $container)
    {
        $container->set(static::PLUGINS_CREATE_GLOSSARY_EXPANDER, function (Container $container) {
            return $this->getCreateGlossaryExpanderPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\CmsGui\Dependency\Plugin\CmsPageTableExpanderPluginInterface>
     */
    protected function getCreateGlossaryExpanderPlugins()
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreRelationFormTypePlugin(Container $container): Container
    {
        $container->set(static::PLUGIN_STORE_RELATION_FORM_TYPE, function () {
            return $this->getStoreRelationFormTypePlugin();
        });

        return $container;
    }

    /**
     * @throws \Spryker\Zed\CmsGui\Communication\Exception\MissingStoreRelationFormTypePluginException
     *
     * @return \Spryker\Zed\Kernel\Communication\Form\FormTypeInterface
     */
    protected function getStoreRelationFormTypePlugin(): FormTypeInterface
    {
        throw new MissingStoreRelationFormTypePluginException(
            sprintf(
                'Missing instance of %s! You need to configure StoreRelationFormType ' .
                'in your own CmsGuiDependencyProvider::getStoreRelationFormTypePlugin() ' .
                'to be able to manage cms pages.',
                FormTypeInterface::class,
            ),
        );
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCmsGlossaryAfterFindPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_CMS_GLOSSARY_AFTER_FIND, function () {
            return $this->getCmsGlossaryAfterFindPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\CmsGuiExtension\Dependency\Plugin\CmsGlossaryAfterFindPluginInterface>
     */
    protected function getCmsGlossaryAfterFindPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCmsGlossaryBeforeSavePlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_CMS_GLOSSARY_BEFORE_SAVE, function () {
            return $this->getCmsGlossaryBeforeSavePlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\CmsGuiExtension\Dependency\Plugin\CmsGlossaryBeforeSavePluginInterface>
     */
    protected function getCmsGlossaryBeforeSavePlugins(): array
    {
        return [];
    }
}
