<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\FileManagerGui\Communication\Table;

use Orm\Zed\FileManager\Persistence\Map\SpyMimeTypeTableMap;
use Orm\Zed\FileManager\Persistence\SpyMimeTypeQuery;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\FileManagerGui\Communication\Form\DeleteMimeTypeForm;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class MimeTypeSettingsTable extends AbstractTable
{
    protected const COL_ID_MIME_TYPE = SpyMimeTypeTableMap::COL_ID_MIME_TYPE;

    protected const COL_NAME = SpyMimeTypeTableMap::COL_NAME;

    protected const COL_COMMENT = SpyMimeTypeTableMap::COL_COMMENT;

    protected const COL_IS_ALLOWED = SpyMimeTypeTableMap::COL_IS_ALLOWED;

    /**
     * @var string
     */
    protected const COL_ACTIONS = 'Actions';

    /**
     * @var string
     */
    protected const TITLE_MIME_TYPE = 'MIME Type';

    /**
     * @var string
     */
    protected const TITLE_COMMENT = 'Comment';

    /**
     * @var string
     */
    protected const TITLE_IS_ALLOWED = 'Is allowed';

    /**
     * @var string
     */
    protected const TITLE_ACTIONS = 'Actions';

    /**
     * @var string
     */
    protected const REQUEST_ID_MIME_TYPE = 'id-mime-type';

    /**
     * @var string
     */
    protected const ROUTE_EDIT = 'mime-type/edit';

    /**
     * @var string
     */
    protected const ROUTE_DELETE = 'mime-type/delete';

    /**
     * @var \Orm\Zed\FileManager\Persistence\SpyMimeTypeQuery
     */
    protected $mimeTypeQuery;

    /**
     * @param \Orm\Zed\FileManager\Persistence\SpyMimeTypeQuery $mimeTypeQuery
     */
    public function __construct(SpyMimeTypeQuery $mimeTypeQuery)
    {
        $this->mimeTypeQuery = $mimeTypeQuery;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            static::COL_NAME => static::TITLE_MIME_TYPE,
            static::COL_COMMENT => static::TITLE_COMMENT,
            static::COL_IS_ALLOWED => static::TITLE_IS_ALLOWED,
            static::COL_ACTIONS => static::TITLE_ACTIONS,
        ]);

        $config->setSortable([
            static::COL_NAME,
            static::COL_COMMENT,
        ]);

        $config->setSearchable([
            static::COL_NAME,
            static::COL_COMMENT,
        ]);

        $config->setRawColumns([
            static::COL_IS_ALLOWED,
            static::COL_ACTIONS,
        ]);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $data = [];
        $queryResults = $this->runQuery($this->mimeTypeQuery, $config);

        foreach ($queryResults as $mimeType) {
            $data[] = $this->mapResults($mimeType);
        }

        return $data;
    }

    /**
     * @param array $item
     *
     * @return array
     */
    protected function mapResults(array $item)
    {
        $actions = implode(' ', $this->buildLinks($item));

        return [
            static::COL_NAME => $item[static::COL_NAME],
            static::COL_COMMENT => $item[static::COL_COMMENT],
            static::COL_IS_ALLOWED => $this->addCheckbox($item),
            static::COL_ACTIONS => $actions,
        ];
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function addCheckbox(array $item)
    {
        return sprintf(
            "<input id='mime_type_is_allowed_%s' class='mime_type_is_allowed' type='checkbox' data-id='%s' %s/>",
            $item[static::COL_ID_MIME_TYPE],
            $item[static::COL_ID_MIME_TYPE],
            $item[static::COL_IS_ALLOWED] ? "checked='checked'" : '',
        );
    }

    /**
     * @param array $item
     *
     * @return array<string>
     */
    protected function buildLinks(array $item)
    {
        $buttons = [];

        $buttons[] = $this->generateEditButton(
            Url::generate(static::ROUTE_EDIT, [
                static::REQUEST_ID_MIME_TYPE => $item[static::COL_ID_MIME_TYPE],
            ]),
            'Edit',
        );
        $buttons[] = $this->generateRemoveButton(
            Url::generate(static::ROUTE_DELETE, [
                static::REQUEST_ID_MIME_TYPE => $item[static::COL_ID_MIME_TYPE],
            ]),
            'Delete',
            [],
            DeleteMimeTypeForm::class,
        );

        return $buttons;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function setDefaultSortField(TableConfiguration $config)
    {
        $config->setDefaultSortField(static::COL_ID_MIME_TYPE, TableConfiguration::SORT_ASC);
    }
}
