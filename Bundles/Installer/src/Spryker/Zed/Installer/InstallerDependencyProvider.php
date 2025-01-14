<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Installer;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\Installer\InstallerConfig getConfig()
 */
class InstallerDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const INSTALLER_PLUGINS = 'installer plugins';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container->set(static::INSTALLER_PLUGINS, function (Container $container) {
            return $this->getInstallerPlugins();
        });

        return $container;
    }

    /**
     * Overwrite on project level.
     *
     * @return array<\Spryker\Zed\Installer\Dependency\Plugin\InstallerPluginInterface>
     */
    public function getInstallerPlugins()
    {
        return [];
    }
}
