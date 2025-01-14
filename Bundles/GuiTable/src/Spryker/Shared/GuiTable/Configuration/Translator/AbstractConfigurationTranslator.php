<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\GuiTable\Configuration\Translator;

use ArrayObject;
use Generated\Shared\Transfer\GuiTableBatchActionTransfer;
use Generated\Shared\Transfer\GuiTableColumnConfigurationTransfer;
use Generated\Shared\Transfer\GuiTableConfigurationTransfer;
use Generated\Shared\Transfer\GuiTableFilterTransfer;
use Generated\Shared\Transfer\GuiTableRowActionTransfer;
use Spryker\Shared\GuiTable\Configuration\Builder\GuiTableConfigurationBuilderInterface;

abstract class AbstractConfigurationTranslator implements ConfigurationTranslatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\GuiTableConfigurationTransfer $guiTableConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableConfigurationTransfer
     */
    public function translateConfiguration(
        GuiTableConfigurationTransfer $guiTableConfigurationTransfer
    ): GuiTableConfigurationTransfer {
        $guiTableConfigurationTransfer = $this->translateColumns($guiTableConfigurationTransfer);
        $guiTableConfigurationTransfer = $this->translateFilters($guiTableConfigurationTransfer);
        $guiTableConfigurationTransfer = $this->translateRowActions($guiTableConfigurationTransfer);
        $guiTableConfigurationTransfer = $this->translateBatchActions($guiTableConfigurationTransfer);
        $guiTableConfigurationTransfer = $this->translateSearch($guiTableConfigurationTransfer);
        $guiTableConfigurationTransfer = $this->translateTitle($guiTableConfigurationTransfer);
        $guiTableConfigurationTransfer = $this->translateEditableCreateConfiguration($guiTableConfigurationTransfer);
        $guiTableConfigurationTransfer = $this->translateEditableUpdateConfiguration($guiTableConfigurationTransfer);
        $guiTableConfigurationTransfer = $this->translateEditableColumns($guiTableConfigurationTransfer);

        return $guiTableConfigurationTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableConfigurationTransfer $guiTableConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableConfigurationTransfer
     */
    protected function translateColumns(
        GuiTableConfigurationTransfer $guiTableConfigurationTransfer
    ): GuiTableConfigurationTransfer {
        /** @var \ArrayObject<int, \Generated\Shared\Transfer\GuiTableColumnConfigurationTransfer> $translatedGuiTableColumnConfigurationTransfers */
        $translatedGuiTableColumnConfigurationTransfers = new ArrayObject();

        foreach ($guiTableConfigurationTransfer->getColumns() as $guiTableColumnConfigurationTransfer) {
            $translatedGuiTableColumnConfigurationTransfers[] = $this->translateColumn($guiTableColumnConfigurationTransfer);
        }

        $guiTableConfigurationTransfer->setColumns($translatedGuiTableColumnConfigurationTransfers);

        return $guiTableConfigurationTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableColumnConfigurationTransfer $guiTableColumnConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableColumnConfigurationTransfer
     */
    protected function translateColumn(
        GuiTableColumnConfigurationTransfer $guiTableColumnConfigurationTransfer
    ): GuiTableColumnConfigurationTransfer {
        $columnTitle = $guiTableColumnConfigurationTransfer->getTitle();

        if ($columnTitle) {
            $guiTableColumnConfigurationTransfer->setTitle($this->translate($columnTitle));
        }

        return $guiTableColumnConfigurationTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableConfigurationTransfer $guiTableConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableConfigurationTransfer
     */
    protected function translateFilters(
        GuiTableConfigurationTransfer $guiTableConfigurationTransfer
    ): GuiTableConfigurationTransfer {
        $guiTableFiltersConfigurationTransfer = $guiTableConfigurationTransfer->getFilters();

        if (!$guiTableFiltersConfigurationTransfer || !$guiTableFiltersConfigurationTransfer->getIsEnabled()) {
            return $guiTableConfigurationTransfer;
        }

        /** @var \ArrayObject<int, \Generated\Shared\Transfer\GuiTableFilterTransfer> $translatedGuiTableFilterTransfers */
        $translatedGuiTableFilterTransfers = new ArrayObject();

        foreach ($guiTableFiltersConfigurationTransfer->getItems() as $guiTableFilterTransfer) {
            $translatedGuiTableFilterTransfers[] = $this->translateFilter($guiTableFilterTransfer);
        }

        $guiTableFiltersConfigurationTransfer->setItems($translatedGuiTableFilterTransfers);
        $guiTableConfigurationTransfer->setFilters($guiTableFiltersConfigurationTransfer);

        return $guiTableConfigurationTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableFilterTransfer $guiTableFilterTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableFilterTransfer
     */
    protected function translateFilter(GuiTableFilterTransfer $guiTableFilterTransfer): GuiTableFilterTransfer
    {
        $filterTitle = $guiTableFilterTransfer->getTitle();

        if ($filterTitle) {
            $guiTableFilterTransfer->setTitle($this->translate($filterTitle));
        }

        if ($guiTableFilterTransfer->getType() === GuiTableConfigurationBuilderInterface::FILTER_TYPE_SELECT) {
            /** @var \Generated\Shared\Transfer\SelectGuiTableFilterTypeOptionsTransfer $selectTypeOptions */
            $selectTypeOptions = $guiTableFilterTransfer->getTypeOptions();
            foreach ($selectTypeOptions->getValues() as $selectOption) {
                $translatedTitle = $this->translate($selectOption->getTitleOrFail());
                $selectOption->setTitle($translatedTitle);
            }
        }

        return $guiTableFilterTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableConfigurationTransfer $guiTableConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableConfigurationTransfer
     */
    protected function translateRowActions(
        GuiTableConfigurationTransfer $guiTableConfigurationTransfer
    ): GuiTableConfigurationTransfer {
        $guiTableRowActionsConfigurationTransfer = $guiTableConfigurationTransfer->getRowActions();

        if (!$guiTableRowActionsConfigurationTransfer || !$guiTableRowActionsConfigurationTransfer->getIsEnabled()) {
            return $guiTableConfigurationTransfer;
        }

        /** @var \ArrayObject<int, \Generated\Shared\Transfer\GuiTableRowActionTransfer> $translatedGuiTableRowActionTransfers */
        $translatedGuiTableRowActionTransfers = new ArrayObject();

        foreach ($guiTableRowActionsConfigurationTransfer->getActions() as $guiTableRowActionTransfer) {
            $translatedGuiTableRowActionTransfers[] = $this->translateRowAction($guiTableRowActionTransfer);
        }

        $guiTableRowActionsConfigurationTransfer->setActions($translatedGuiTableRowActionTransfers);
        $guiTableConfigurationTransfer->setRowActions($guiTableRowActionsConfigurationTransfer);

        return $guiTableConfigurationTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableRowActionTransfer $guiTableRowActionTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableRowActionTransfer
     */
    protected function translateRowAction(GuiTableRowActionTransfer $guiTableRowActionTransfer): GuiTableRowActionTransfer
    {
        $rowActionTitle = $guiTableRowActionTransfer->getTitle();

        if ($rowActionTitle) {
            $guiTableRowActionTransfer->setTitle($this->translate($rowActionTitle));
        }

        return $guiTableRowActionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableConfigurationTransfer $guiTableConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableConfigurationTransfer
     */
    protected function translateBatchActions(
        GuiTableConfigurationTransfer $guiTableConfigurationTransfer
    ): GuiTableConfigurationTransfer {
        $guiTableBatchActionsConfigurationTransfer = $guiTableConfigurationTransfer->getBatchActions();

        if (!$guiTableBatchActionsConfigurationTransfer || !$guiTableBatchActionsConfigurationTransfer->getIsEnabled()) {
            return $guiTableConfigurationTransfer;
        }

        /** @var \ArrayObject<int, \Generated\Shared\Transfer\GuiTableBatchActionTransfer> $translatedGuiTableBatchActionTransfers */
        $translatedGuiTableBatchActionTransfers = new ArrayObject();

        foreach ($guiTableBatchActionsConfigurationTransfer->getActions() as $guiTableBatchActionTransfer) {
            $translatedGuiTableBatchActionTransfers[] = $this->translateBatchAction($guiTableBatchActionTransfer);
        }

        $guiTableBatchActionsConfigurationTransfer->setActions($translatedGuiTableBatchActionTransfers);
        $guiTableConfigurationTransfer->setBatchActions($guiTableBatchActionsConfigurationTransfer);

        return $guiTableConfigurationTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableBatchActionTransfer $guiTableBatchActionTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableBatchActionTransfer
     */
    protected function translateBatchAction(GuiTableBatchActionTransfer $guiTableBatchActionTransfer): GuiTableBatchActionTransfer
    {
        $batchActionTitle = $guiTableBatchActionTransfer->getTitle();

        if ($batchActionTitle) {
            $guiTableBatchActionTransfer->setTitle($this->translate($batchActionTitle));
        }

        return $guiTableBatchActionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableConfigurationTransfer $guiTableConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableConfigurationTransfer
     */
    protected function translateSearch(
        GuiTableConfigurationTransfer $guiTableConfigurationTransfer
    ): GuiTableConfigurationTransfer {
        $guiTableSearchConfigurationTransfer = $guiTableConfigurationTransfer->getSearch();

        if (!$guiTableSearchConfigurationTransfer || !$guiTableSearchConfigurationTransfer->getIsEnabled()) {
            return $guiTableConfigurationTransfer;
        }

        $search = $guiTableSearchConfigurationTransfer->getSearch();

        foreach ($search as $key => $searchOption) {
            $search[$key] = $this->translate($searchOption);
        }

        $guiTableSearchConfigurationTransfer->setSearch($search);
        $guiTableConfigurationTransfer->setSearch($guiTableSearchConfigurationTransfer);

        return $guiTableConfigurationTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableConfigurationTransfer $guiTableConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableConfigurationTransfer
     */
    protected function translateTitle(
        GuiTableConfigurationTransfer $guiTableConfigurationTransfer
    ): GuiTableConfigurationTransfer {
        $guiTableTitleConfigurationTransfer = $guiTableConfigurationTransfer->getTitle();

        if (
            !$guiTableTitleConfigurationTransfer
            || !$guiTableTitleConfigurationTransfer->getIsEnabled()
            || !$guiTableTitleConfigurationTransfer->getTitle()
        ) {
            return $guiTableConfigurationTransfer;
        }

        $guiTableTitleConfigurationTransfer->setTitle($this->translate(
            $guiTableTitleConfigurationTransfer->getTitleOrFail(),
        ));
        $guiTableConfigurationTransfer->setTitle($guiTableTitleConfigurationTransfer);

        return $guiTableConfigurationTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableConfigurationTransfer $guiTableConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableConfigurationTransfer
     */
    protected function translateEditableCreateConfiguration(
        GuiTableConfigurationTransfer $guiTableConfigurationTransfer
    ): GuiTableConfigurationTransfer {
        $guiTableEditableConfigurationTransfer = $guiTableConfigurationTransfer->getEditable();

        if (!$guiTableEditableConfigurationTransfer) {
            return $guiTableConfigurationTransfer;
        }

        $guiTableEditableCreateConfigurationTransfer = $guiTableEditableConfigurationTransfer->getCreate();

        if (!$guiTableEditableCreateConfigurationTransfer) {
            return $guiTableConfigurationTransfer;
        }

        $guiTableEditableAddButtonTransfer = $guiTableEditableCreateConfigurationTransfer->getAddButton();

        if ($guiTableEditableAddButtonTransfer) {
            $title = $guiTableEditableAddButtonTransfer->getTitle();

            if ($title) {
                $guiTableEditableAddButtonTransfer->setTitle(
                    $this->translate($title),
                );

                $guiTableEditableCreateConfigurationTransfer->setAddButton($guiTableEditableAddButtonTransfer);
            }
        }

        $guiTableEditableCancelButtonTransfer = $guiTableEditableCreateConfigurationTransfer->getCancelButton();

        if ($guiTableEditableCancelButtonTransfer) {
            $title = $guiTableEditableCancelButtonTransfer->getTitle();

            if ($title) {
                $guiTableEditableCancelButtonTransfer->setTitle(
                    $this->translate($title),
                );

                $guiTableEditableCreateConfigurationTransfer->setCancelButton($guiTableEditableCancelButtonTransfer);
            }
        }

        $guiTableEditableConfigurationTransfer->setCreate($guiTableEditableCreateConfigurationTransfer);

        return $guiTableConfigurationTransfer->setEditable($guiTableEditableConfigurationTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableConfigurationTransfer $guiTableConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableConfigurationTransfer
     */
    protected function translateEditableUpdateConfiguration(
        GuiTableConfigurationTransfer $guiTableConfigurationTransfer
    ): GuiTableConfigurationTransfer {
        $guiTableEditableConfigurationTransfer = $guiTableConfigurationTransfer->getEditable();

        if (!$guiTableEditableConfigurationTransfer) {
            return $guiTableConfigurationTransfer;
        }

        $guiTableEditableUpdateConfigurationTransfer = $guiTableEditableConfigurationTransfer->getUpdate();

        if (!$guiTableEditableUpdateConfigurationTransfer) {
            return $guiTableConfigurationTransfer;
        }

        $guiTableEditableSaveButtonTransfer = $guiTableEditableUpdateConfigurationTransfer->getSaveButton();

        if ($guiTableEditableSaveButtonTransfer) {
            $title = $guiTableEditableSaveButtonTransfer->getTitle();

            if ($title) {
                $guiTableEditableSaveButtonTransfer->setTitle(
                    $this->translate($title),
                );

                $guiTableEditableUpdateConfigurationTransfer->setSaveButton($guiTableEditableSaveButtonTransfer);
            }
        }

        $guiTableEditableCancelButtonTransfer = $guiTableEditableUpdateConfigurationTransfer->getCancelButton();

        if ($guiTableEditableCancelButtonTransfer) {
            $title = $guiTableEditableCancelButtonTransfer->getTitle();

            if ($title) {
                $guiTableEditableCancelButtonTransfer->setTitle(
                    $this->translate($title),
                );

                $guiTableEditableUpdateConfigurationTransfer->setCancelButton($guiTableEditableCancelButtonTransfer);
            }
        }

        $guiTableEditableConfigurationTransfer->setUpdate($guiTableEditableUpdateConfigurationTransfer);

        return $guiTableConfigurationTransfer->setEditable($guiTableEditableConfigurationTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\GuiTableConfigurationTransfer $guiTableConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\GuiTableConfigurationTransfer
     */
    protected function translateEditableColumns(
        GuiTableConfigurationTransfer $guiTableConfigurationTransfer
    ): GuiTableConfigurationTransfer {
        $guiTableEditableConfigurationTransfer = $guiTableConfigurationTransfer->getEditable();

        if (!$guiTableEditableConfigurationTransfer) {
            return $guiTableConfigurationTransfer;
        }

        $guiTableColumnConfigurationTransfers = $guiTableEditableConfigurationTransfer->getColumns();

        if ($guiTableColumnConfigurationTransfers->count() < 1) {
            return $guiTableConfigurationTransfer;
        }

        /** @var \ArrayObject<int, \Generated\Shared\Transfer\GuiTableColumnConfigurationTransfer> $translatedGuiTableColumnConfigurationTransfers */
        $translatedGuiTableColumnConfigurationTransfers = new ArrayObject();
        foreach ($guiTableColumnConfigurationTransfers as $guiTableColumnConfigurationTransfer) {
            $translatedGuiTableColumnConfigurationTransfers[] = $this->translateColumn($guiTableColumnConfigurationTransfer);
        }

        $guiTableEditableConfigurationTransfer->setColumns($translatedGuiTableColumnConfigurationTransfers);

        return $guiTableConfigurationTransfer->setEditable($guiTableEditableConfigurationTransfer);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    abstract protected function translate(string $key): string;
}
