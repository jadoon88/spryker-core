<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\NavigationStorage\Business\Storage;

use ArrayObject;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\NavigationNodeLocalizedAttributesTransfer;
use Generated\Shared\Transfer\NavigationNodeStorageTransfer;
use Generated\Shared\Transfer\NavigationStorageTransfer;
use Generated\Shared\Transfer\NavigationTransfer;
use Generated\Shared\Transfer\NavigationTreeTransfer;
use Orm\Zed\NavigationStorage\Persistence\SpyNavigationStorage;
use Spryker\Zed\NavigationStorage\Dependency\Facade\NavigationStorageToNavigationInterface;
use Spryker\Zed\NavigationStorage\Dependency\Facade\NavigationStorageToStoreFacadeInterface;
use Spryker\Zed\NavigationStorage\Dependency\Service\NavigationStorageToUtilSanitizeServiceInterface;
use Spryker\Zed\NavigationStorage\Persistence\NavigationStorageQueryContainerInterface;
use Spryker\Zed\PropelOrm\Business\Transaction\DatabaseTransactionHandlerTrait;

class NavigationStorageWriter implements NavigationStorageWriterInterface
{
    use DatabaseTransactionHandlerTrait;

    /**
     * @var \Spryker\Zed\NavigationStorage\Dependency\Service\NavigationStorageToUtilSanitizeServiceInterface
     */
    protected $utilSanitizeService;

    /**
     * @var \Spryker\Zed\NavigationStorage\Dependency\Facade\NavigationStorageToNavigationInterface
     */
    protected $navigationFacade;

    /**
     * @var \Spryker\Zed\NavigationStorage\Persistence\NavigationStorageQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Spryker\Zed\NavigationStorage\Dependency\Facade\NavigationStorageToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @deprecated Use {@link \Spryker\Zed\SynchronizationBehavior\SynchronizationBehaviorConfig::isSynchronizationEnabled()} instead.
     *
     * @var bool
     */
    protected $isSendingToQueue = true;

    /**
     * @param \Spryker\Zed\NavigationStorage\Dependency\Service\NavigationStorageToUtilSanitizeServiceInterface $utilSanitizeService
     * @param \Spryker\Zed\NavigationStorage\Dependency\Facade\NavigationStorageToNavigationInterface $navigationFacade
     * @param \Spryker\Zed\NavigationStorage\Persistence\NavigationStorageQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\NavigationStorage\Dependency\Facade\NavigationStorageToStoreFacadeInterface $storeFacade
     * @param bool $isSendingToQueue
     */
    public function __construct(
        NavigationStorageToUtilSanitizeServiceInterface $utilSanitizeService,
        NavigationStorageToNavigationInterface $navigationFacade,
        NavigationStorageQueryContainerInterface $queryContainer,
        NavigationStorageToStoreFacadeInterface $storeFacade,
        $isSendingToQueue
    ) {
        $this->utilSanitizeService = $utilSanitizeService;
        $this->navigationFacade = $navigationFacade;
        $this->queryContainer = $queryContainer;
        $this->storeFacade = $storeFacade;
        $this->isSendingToQueue = $isSendingToQueue;
    }

    /**
     * @param array $navigationIds
     *
     * @return void
     */
    public function publish(array $navigationIds)
    {
        $navigationTreeTransfers = $this->getNavigationTreeTransfer($navigationIds);
        $spyNavigationStorageEntities = $this->findNavigationStorageEntitiesByNavigationIds($navigationIds);

        $this->storeData($navigationTreeTransfers, $spyNavigationStorageEntities);
    }

    /**
     * @param array $navigationIds
     *
     * @return void
     */
    public function unpublish(array $navigationIds)
    {
        $spyNavigationMenuTranslationStorageEntities = $this->findNavigationStorageEntitiesByNavigationIds($navigationIds);
        foreach ($spyNavigationMenuTranslationStorageEntities as $spyNavigationMenuTranslationStorageLocalizedEntities) {
            foreach ($spyNavigationMenuTranslationStorageLocalizedEntities as $spyNavigationMenuTranslationStorageLocalizedEntity) {
                $spyNavigationMenuTranslationStorageLocalizedEntity->delete();
            }
        }
    }

    /**
     * @param array<array<\Generated\Shared\Transfer\NavigationTreeTransfer>> $navigationTreeTransfers
     * @param array $spyNavigationMenuTranslationStorageEntities
     *
     * @return void
     */
    protected function storeData(array $navigationTreeTransfers, array $spyNavigationMenuTranslationStorageEntities)
    {
        foreach ($navigationTreeTransfers as $navigationTreeTransfer) {
            foreach ($navigationTreeTransfer as $localeName => $navigationTreeByLocaleTransfer) {
                if (isset($spyNavigationMenuTranslationStorageEntities[$navigationTreeByLocaleTransfer->getNavigation()->getIdNavigation()][$localeName])) {
                    $this->storeDataSet($navigationTreeByLocaleTransfer, $localeName, $spyNavigationMenuTranslationStorageEntities[$navigationTreeByLocaleTransfer->getNavigation()->getIdNavigation()][$localeName]);

                    continue;
                }

                $this->storeDataSet($navigationTreeByLocaleTransfer, $localeName);
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\NavigationTreeTransfer $navigationTreeByLocaleTransfer
     * @param string $localeName
     * @param \Orm\Zed\NavigationStorage\Persistence\SpyNavigationStorage|null $spyNavigationStorage
     *
     * @return void
     */
    protected function storeDataSet(NavigationTreeTransfer $navigationTreeByLocaleTransfer, $localeName, ?SpyNavigationStorage $spyNavigationStorage = null)
    {
        if ($spyNavigationStorage === null) {
            $spyNavigationStorage = new SpyNavigationStorage();
        }

        $newTransfer = $this->mapToNavigationStorageTransfer($navigationTreeByLocaleTransfer);
        $data = $this->utilSanitizeService->filterOutBlankValuesRecursively($newTransfer->toArray());

        $spyNavigationStorage->setFkNavigation($navigationTreeByLocaleTransfer->getNavigation()->getIdNavigation());
        $spyNavigationStorage->setNavigationKey($navigationTreeByLocaleTransfer->getNavigation()->getKey());
        $spyNavigationStorage->setLocale($localeName);
        $spyNavigationStorage->setData($data);
        $spyNavigationStorage->setIsSendingToQueue($this->isSendingToQueue);
        $spyNavigationStorage->save();
    }

    /**
     * @param array $navigationIds
     *
     * @return array<array<\Generated\Shared\Transfer\NavigationTreeTransfer>>
     */
    protected function getNavigationTreeTransfer(array $navigationIds)
    {
        $navigationTrees = [];
        $localeNames = $this->getSharedPersistenceLocaleNames();
        $locales = $this->queryContainer->queryLocalesWithLocaleNames($localeNames)->find()->getData();
        foreach ($navigationIds as $navigationId) {
            $navigationTransfer = new NavigationTransfer();
            $navigationTransfer->setIdNavigation($navigationId);
            foreach ($locales as $locale) {
                $localeTransfer = (new LocaleTransfer())->fromArray($locale->toArray(), true);
                $navigationTreeTransfer = $this->navigationFacade->findNavigationTree($navigationTransfer, $localeTransfer);
                if (!$navigationTreeTransfer) {
                    continue;
                }

                $navigationTrees[$navigationId][$localeTransfer->getLocaleName()] = $navigationTreeTransfer;
            }
        }

        return $navigationTrees;
    }

    /**
     * @return array<string>
     */
    protected function getSharedPersistenceLocaleNames(): array
    {
        $currentStoreTransfer = $this->storeFacade->getCurrentStore();
        $localeNames = $currentStoreTransfer->getAvailableLocaleIsoCodes();
        foreach ($this->storeFacade->getStoresWithSharedPersistence($currentStoreTransfer) as $storeTransfer) {
            $localeNames = array_merge($localeNames, $storeTransfer->getAvailableLocaleIsoCodes());
        }

        return array_unique($localeNames);
    }

    /**
     * @param array $navigationIds
     *
     * @return array
     */
    protected function findNavigationStorageEntitiesByNavigationIds(array $navigationIds)
    {
        $spyNavigationStorageEntities = $this->queryContainer->queryNavigationStorageByNavigationIds($navigationIds)->find();
        $navigationStorageEntitiesByIdAndLocale = [];
        foreach ($spyNavigationStorageEntities as $spyNavigationStorageEntity) {
            $navigationStorageEntitiesByIdAndLocale[$spyNavigationStorageEntity->getFkNavigation()][$spyNavigationStorageEntity->getLocale()] = $spyNavigationStorageEntity;
        }

        return $navigationStorageEntitiesByIdAndLocale;
    }

    /**
     * @param \Generated\Shared\Transfer\NavigationTreeTransfer $navigationTreeTransfer
     *
     * @return \Generated\Shared\Transfer\NavigationStorageTransfer
     */
    protected function mapToNavigationStorageTransfer(NavigationTreeTransfer $navigationTreeTransfer)
    {
        $navigationStorageTransfer = (new NavigationStorageTransfer())
            ->setName($navigationTreeTransfer->getNavigation()->getName())
            ->setKey($navigationTreeTransfer->getNavigation()->getKey())
            ->setIsActive($navigationTreeTransfer->getNavigation()->getIsActive())
            ->setId($navigationTreeTransfer->getNavigation()->getIdNavigation());

        $nodes = $this->mapToNavigationNodeStorageTransfer($navigationTreeTransfer->getNodes());
        $navigationStorageTransfer->setNodes($nodes);

        return $navigationStorageTransfer;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\NavigationTreeNodeTransfer> $navigationTreeNodeTransfers
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\NavigationNodeStorageTransfer>
     */
    protected function mapToNavigationNodeStorageTransfer(ArrayObject $navigationTreeNodeTransfers)
    {
        /** @var \ArrayObject<int, \Generated\Shared\Transfer\NavigationNodeStorageTransfer> $nodes */
        $nodes = new ArrayObject();
        foreach ($navigationTreeNodeTransfers as $navigationTreeNodeTransfer) {
            $navigationNodeLocalizedAttributes = $navigationTreeNodeTransfer->getNavigationNode()->getNavigationNodeLocalizedAttributes()->getIterator()->current();
            if (!$navigationNodeLocalizedAttributes instanceof NavigationNodeLocalizedAttributesTransfer) {
                continue;
            }

            $nodeTransfer = new NavigationNodeStorageTransfer();
            $nodeTransfer->setId($navigationTreeNodeTransfer->getNavigationNode()->getIdNavigationNode());
            $nodeTransfer->setTitle($navigationNodeLocalizedAttributes->getTitle());
            $nodeTransfer->setCssClass($navigationNodeLocalizedAttributes->getCssClass());
            $nodeTransfer->setUrl($this->getNavigationNodeUrl($navigationNodeLocalizedAttributes));
            $nodeTransfer->setNodeType($navigationTreeNodeTransfer->getNavigationNode()->getNodeType());
            $nodeTransfer->setIsActive($navigationTreeNodeTransfer->getNavigationNode()->getIsActive());
            $nodeTransfer->setValidFrom($navigationTreeNodeTransfer->getNavigationNode()->getValidFrom());
            $nodeTransfer->setValidTo($navigationTreeNodeTransfer->getNavigationNode()->getValidTo());

            if (count($navigationTreeNodeTransfer->getChildren()) !== 0) {
                $nodeTransfer->setChildren($this->mapToNavigationNodeStorageTransfer($navigationTreeNodeTransfer->getChildren()));
            }

            $nodes[] = $nodeTransfer;
        }

        return $nodes;
    }

    /**
     * @param \Generated\Shared\Transfer\NavigationNodeLocalizedAttributesTransfer $navigationNodeLocalizedAttributesTransfer
     *
     * @return string
     */
    protected function getNavigationNodeUrl(NavigationNodeLocalizedAttributesTransfer $navigationNodeLocalizedAttributesTransfer)
    {
        $url = $navigationNodeLocalizedAttributesTransfer->getLink() ?? '';
        $url = $navigationNodeLocalizedAttributesTransfer->getExternalUrl() ?? $url;
        $url = $navigationNodeLocalizedAttributesTransfer->getCategoryUrl() ?? $url;
        $url = $navigationNodeLocalizedAttributesTransfer->getCmsPageUrl() ?? $url;
        $url = $navigationNodeLocalizedAttributesTransfer->getUrl() ?? $url;

        return $url;
    }
}
