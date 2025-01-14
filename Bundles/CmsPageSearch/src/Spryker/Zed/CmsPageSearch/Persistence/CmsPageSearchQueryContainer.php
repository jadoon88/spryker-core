<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsPageSearch\Persistence;

use Orm\Zed\Cms\Persistence\Map\SpyCmsPageTableMap;
use Orm\Zed\Cms\Persistence\Map\SpyCmsVersionTableMap;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;

/**
 * @method \Spryker\Zed\CmsPageSearch\Persistence\CmsPageSearchPersistenceFactory getFactory()
 */
class CmsPageSearchQueryContainer extends AbstractQueryContainer implements CmsPageSearchQueryContainerInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array $localeNames
     *
     * @return \Orm\Zed\Locale\Persistence\SpyLocaleQuery
     */
    public function queryLocalesWithLocaleNames(array $localeNames)
    {
        return $this->getFactory()
            ->getLocaleQueryContainer()
            ->queryLocales()
            ->filterByLocaleName_In($localeNames);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array $cmsPageIds
     *
     * @return \Orm\Zed\Cms\Persistence\SpyCmsPageQuery
     */
    public function queryCmsPageVersionByIds(array $cmsPageIds)
    {
        /** @var literal-string $where */
        $where = sprintf('%s = (%s)', SpyCmsVersionTableMap::COL_VERSION, $this->getMaxVersionSubQuery());

        /** @phpstan-var \Orm\Zed\Cms\Persistence\SpyCmsPageQuery */
        return $this->getFactory()
            ->getCmsQueryContainer()
            ->queryPages()
            ->filterByIdCmsPage_In($cmsPageIds)
            ->joinWithSpyUrl()
            ->joinWith('SpyUrl.SpyLocale')
            ->joinWithSpyCmsVersion()
            ->where($where)
            ->joinWithSpyCmsPageStore(Criteria::LEFT_JOIN)
            ->useSpyCmsPageStoreQuery()
                ->joinWithSpyStore()
            ->endUse();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array $cmsPageIds
     *
     * @return \Orm\Zed\Cms\Persistence\SpyCmsPageQuery
     */
    public function queryCmsPageByIds(array $cmsPageIds)
    {
        return $this->getFactory()
            ->getCmsQueryContainer()
            ->queryPages()
            ->filterByIdCmsPage_In($cmsPageIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array $cmsPageIds
     *
     * @return \Orm\Zed\CmsPageSearch\Persistence\SpyCmsPageSearchQuery
     */
    public function queryCmsPageSearchEntities(array $cmsPageIds)
    {
        return $this->getFactory()
            ->createSpyCmsPageSearchQuery()
            ->filterByFkCmsPage_In($cmsPageIds);
    }

    /**
     * @return string
     */
    protected function getMaxVersionSubQuery()
    {
        $maxVersionQuery = $this->getFactory()
            ->getCmsQueryContainer()
            ->queryAllCmsVersions()
            ->addSelfSelectColumns()
            ->clearSelectColumns()
            ->withColumn(sprintf('MAX(%s)', SpyCmsVersionTableMap::COL_VERSION))
            ->where(sprintf('%s = %s', SpyCmsVersionTableMap::COL_FK_CMS_PAGE, SpyCmsPageTableMap::COL_ID_CMS_PAGE));

        $queryParams = [];
        $queryString = $maxVersionQuery->createSelectSql($queryParams);

        return $queryString;
    }
}
