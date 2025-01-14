<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Cache\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @deprecated Use {@link \Spryker\Zed\Cache\Communication\Console\EmptyAllCachesConsole} instead.
 *
 * @method \Spryker\Zed\Cache\Business\CacheFacadeInterface getFacade()
 * @method \Spryker\Zed\Cache\Communication\CacheCommunicationFactory getFactory()
 */
class DeleteAllCachesConsole extends Console
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'cache:delete-all';

    /**
     * @var string
     */
    public const DESCRIPTION = 'Deletes all cache files from /data/{Store}/cache for all stores';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(static::COMMAND_NAME);
        $this->setDescription(static::DESCRIPTION);
        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dirs = $this->getFacade()->deleteAllFiles();
        $this->info('Removed cache files', true);
        $this->displayDeleted($dirs, $output);

        $dirs = $this->getFacade()->deleteAllAutoloaderFiles();
        $this->info('Removed autoloader cache files', true);
        $this->displayDeleted($dirs, $output);

        return static::CODE_SUCCESS;
    }

    /**
     * @param array $dirs
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function displayDeleted(array $dirs, OutputInterface $output)
    {
        foreach ($dirs as $dir) {
            $output->writeln($dir);
        }
    }
}
