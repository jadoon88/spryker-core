<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Category\Persistence;

use Generated\Shared\Transfer\CategoryCollectionTransfer;
use Generated\Shared\Transfer\CategoryCriteriaTransfer;
use Generated\Shared\Transfer\CategoryNodeFilterTransfer;
use Generated\Shared\Transfer\CategoryNodeUrlFilterTransfer;
use Generated\Shared\Transfer\CategoryTransfer;
use Generated\Shared\Transfer\CategoryUrlPathCriteriaTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\NodeCollectionTransfer;
use Generated\Shared\Transfer\NodeTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;

interface CategoryRepositoryInterface
{
    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Generated\Shared\Transfer\CategoryCollectionTransfer
     */
    public function getAllCategoryCollection(LocaleTransfer $localeTransfer): CategoryCollectionTransfer;

    /**
     * @param int $idCategoryNode
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return string
     */
    public function getNodePath(int $idCategoryNode, LocaleTransfer $localeTransfer);

    /**
     * @param int $idNode
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return string
     */
    public function getCategoryNodePath(int $idNode, LocaleTransfer $localeTransfer): string;

    /**
     * @param string $nodeName
     * @param \Generated\Shared\Transfer\CategoryTransfer $categoryTransfer
     *
     * @return bool
     */
    public function checkSameLevelCategoryByNameExists(string $nodeName, CategoryTransfer $categoryTransfer): bool;

    /**
     * @param int $idCategory
     *
     * @return \Generated\Shared\Transfer\CategoryTransfer|null
     */
    public function findCategoryById(int $idCategory): ?CategoryTransfer;

    /**
     * @param int $idCategoryNode
     *
     * @return int[]
     */
    public function getChildCategoryNodeIdsByCategoryNodeId(int $idCategoryNode): array;

    /**
     * @param int $idCategoryNode
     *
     * @return int[]
     */
    public function getParentCategoryNodeIdsByCategoryNodeId(int $idCategoryNode): array;

    /**
     * @param \Generated\Shared\Transfer\CategoryCriteriaTransfer $categoryCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\CategoryTransfer|null
     */
    public function findCategoryByCriteria(CategoryCriteriaTransfer $categoryCriteriaTransfer): ?CategoryTransfer;

    /**
     * @param \Generated\Shared\Transfer\CategoryTransfer $categoryTransfer
     * @param \Generated\Shared\Transfer\CategoryCriteriaTransfer $categoryCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\NodeTransfer[][]
     */
    public function getCategoryNodeChildNodesCollectionIndexedByParentNodeId(
        CategoryTransfer $categoryTransfer,
        CategoryCriteriaTransfer $categoryCriteriaTransfer
    ): array;

    /**
     * @param \Generated\Shared\Transfer\CategoryNodeUrlFilterTransfer $categoryNodeFilterTransfer
     *
     * @return \Generated\Shared\Transfer\UrlTransfer[]
     */
    public function getCategoryNodeUrls(CategoryNodeUrlFilterTransfer $categoryNodeFilterTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\CategoryUrlPathCriteriaTransfer $categoryUrlPathCriteriaTransfer
     *
     * @return array
     */
    public function getCategoryUrlPathParts(CategoryUrlPathCriteriaTransfer $categoryUrlPathCriteriaTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\CategoryNodeFilterTransfer $categoryNodeFilterTransfer
     *
     * @return \Generated\Shared\Transfer\NodeCollectionTransfer
     */
    public function getCategoryNodesByCriteria(CategoryNodeFilterTransfer $categoryNodeFilterTransfer): NodeCollectionTransfer;

    /**
     * @param int $idCategory
     *
     * @return \Generated\Shared\Transfer\StoreRelationTransfer
     */
    public function getCategoryStoreRelationByIdCategory(int $idCategory): StoreRelationTransfer;

    /**
     * @param int $idCategoryNode
     *
     * @return \Generated\Shared\Transfer\NodeTransfer|null
     */
    public function findCategoryNodeByIdCategoryNode(int $idCategoryNode): ?NodeTransfer;

    /**
     * @param int $idCategory
     * @param int $idStore
     *
     * @return bool
     */
    public function isParentCategoryHasRelationToStore(int $idCategory, int $idStore): bool;
}
