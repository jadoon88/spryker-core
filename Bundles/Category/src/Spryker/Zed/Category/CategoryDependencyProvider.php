<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Category;

use Spryker\Zed\Category\Communication\Plugin\Category\MainChildrenPropagationCategoryStoreAssignerPlugin;
use Spryker\Zed\Category\Dependency\Facade\CategoryToEventFacadeBridge;
use Spryker\Zed\Category\Dependency\Facade\CategoryToLocaleBridge;
use Spryker\Zed\Category\Dependency\Facade\CategoryToTouchBridge;
use Spryker\Zed\Category\Dependency\Facade\CategoryToUrlBridge;
use Spryker\Zed\CategoryExtension\Dependency\Plugin\CategoryStoreAssignerPluginInterface;
use Spryker\Zed\Graph\Communication\Plugin\GraphPlugin;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\Category\CategoryConfig getConfig()
 */
class CategoryDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CATEGORY_QUERY_CONTAINER = 'category query container';

    public const FACADE_TOUCH = 'touch facade';
    public const FACADE_LOCALE = 'locale facade';
    public const FACADE_URL = 'url facade';
    public const FACADE_EVENT = 'facade event';

    public const PLUGIN_GRAPH = 'graph plugin';
    public const PLUGIN_STACK_RELATION_DELETE = 'delete relation plugin stack';
    public const PLUGIN_STACK_RELATION_UPDATE = 'update relation plugin stack';
    public const PLUGIN_PROPEL_CONNECTION = 'propel connection plugin';
    public const PLUGINS_CATEGORY_URL_PATH = 'PLUGINS_CATEGORY_URL_PATH';
    public const PLUGIN_CATEGORY_POST_CREATE = 'PLUGIN_CATEGORY_POST_CREATE';
    public const PLUGIN_CATEGORY_POST_UPDATE = 'PLUGIN_CATEGORY_POST_UPDATE';
    public const PLUGIN_CATEGORY_POST_READ = 'PLUGIN_CATEGORY_POST_READ';
    public const PLUGIN_CATEGORY_STORE_ASSIGNER = 'PLUGIN_CATEGORY_STORE_ASSIGNER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addTouchFacade($container);
        $container = $this->addLocaleFacade($container);
        $container = $this->addUrlFacade($container);
        $container = $this->addEventFacade($container);
        $container = $this->addGraphPlugin($container);
        $container = $this->addRelationDeletePluginStack($container);
        $container = $this->addRelationUpdatePluginStack($container);
        $container = $this->addCategoryUrlPathPlugins($container);
        $container = $this->addCategoryPostCreatePlugins($container);
        $container = $this->addCategoryPostUpdatePlugins($container);
        $container = $this->addCategoryPostReadPlugins($container);
        $container = $this->addCategoryStoreAssignerPlugin($container);

        return $container;
    }

    /**
     * @return \Spryker\Shared\Graph\GraphInterface
     */
    protected function createGraphPlugin()
    {
        return new GraphPlugin();
    }

    /**
     * @return \Spryker\Zed\CategoryExtension\Dependency\Plugin\CategoryRelationDeletePluginInterface[]
     */
    protected function getRelationDeletePluginStack()
    {
        return [];
    }

    /**
     * @return \Spryker\Zed\CategoryExtension\Dependency\Plugin\CategoryRelationUpdatePluginInterface[]
     */
    protected function getRelationUpdatePluginStack(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTouchFacade(Container $container)
    {
        $container->set(static::FACADE_TOUCH, function (Container $container) {
            return new CategoryToTouchBridge($container->getLocator()->touch()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addLocaleFacade(Container $container)
    {
        $container->set(static::FACADE_LOCALE, function (Container $container) {
            return new CategoryToLocaleBridge($container->getLocator()->locale()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUrlFacade(Container $container)
    {
        $container->set(static::FACADE_URL, function (Container $container) {
            return new CategoryToUrlBridge($container->getLocator()->url()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addEventFacade(Container $container)
    {
        $container->set(static::FACADE_EVENT, function (Container $container) {
            return new CategoryToEventFacadeBridge($container->getLocator()->event()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addGraphPlugin(Container $container)
    {
        $container->set(static::PLUGIN_GRAPH, function () {
            return $this->createGraphPlugin();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addRelationDeletePluginStack(Container $container)
    {
        $container->set(static::PLUGIN_STACK_RELATION_DELETE, $container->share(function () {
            return $this->getRelationDeletePluginStack();
        }));

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addRelationUpdatePluginStack(Container $container)
    {
        $container->set(static::PLUGIN_STACK_RELATION_UPDATE, $container->share(function () {
            return $this->getRelationUpdatePluginStack();
        }));

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCategoryUrlPathPlugins(Container $container)
    {
        $container->set(static::PLUGINS_CATEGORY_URL_PATH, $container->share(function () {
            return $this->getCategoryUrlPathPlugins();
        }));

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCategoryPostCreatePlugins(Container $container): Container
    {
        $container->set(static::PLUGIN_CATEGORY_POST_CREATE, function () {
            return $this->getCategoryPostCreatePlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCategoryPostUpdatePlugins(Container $container): Container
    {
        $container->set(static::PLUGIN_CATEGORY_POST_UPDATE, function () {
            return $this->getCategoryPostUpdatePlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCategoryPostReadPlugins(Container $container): Container
    {
        $container->set(static::PLUGIN_CATEGORY_POST_READ, function () {
            return $this->getCategoryPostReadPlugins();
        });

        return $container;
    }

    protected function addCategoryStoreAssignerPlugin(Container $container): Container
    {
        $container->set(static::PLUGIN_CATEGORY_STORE_ASSIGNER, function () {
            return $this->getCategoryStoreAssignerPlugin();
        });

        return $container;
    }

    /**
     * @return \Spryker\Zed\CategoryExtension\Dependency\Plugin\CategoryUrlPathPluginInterface[]
     */
    protected function getCategoryUrlPathPlugins(): array
    {
        return [];
    }

    /**
     * @return \Spryker\Zed\CategoryExtension\Dependency\Plugin\CategoryCreateAfterPluginInterface[]
     */
    protected function getCategoryPostCreatePlugins(): array
    {
        return [];
    }

    /**
     * @return \Spryker\Zed\CategoryExtension\Dependency\Plugin\CategoryUpdateAfterPluginInterface[]
     */
    protected function getCategoryPostUpdatePlugins(): array
    {
        return [];
    }

    /**
     * @return \Spryker\Zed\CategoryExtension\Dependency\Plugin\CategoryTransferExpanderPluginInterface[]
     */
    protected function getCategoryPostReadPlugins(): array
    {
        return [];
    }

    /**
     * @return \Spryker\Zed\CategoryExtension\Dependency\Plugin\CategoryStoreAssignerPluginInterface
     */
    protected function getCategoryStoreAssignerPlugin(): CategoryStoreAssignerPluginInterface
    {
        return new MainChildrenPropagationCategoryStoreAssignerPlugin();
    }
}
