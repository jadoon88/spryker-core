<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\FileManagerGui\Communication\Form\DataProvider;

use Generated\Shared\Transfer\FileLocalizedAttributesTransfer;
use Generated\Shared\Transfer\FileTransfer;
use Generated\Shared\Transfer\MimeTypeTransfer;
use Spryker\Zed\FileManagerGui\Communication\Form\FileForm;
use Spryker\Zed\FileManagerGui\Dependency\Facade\FileManagerGuiToFileManagerFacadeInterface;
use Spryker\Zed\FileManagerGui\Dependency\Facade\FileManagerGuiToLocaleFacadeInterface;

class FileFormDataProvider
{
    /**
     * @var \Spryker\Zed\FileManagerGui\Dependency\Facade\FileManagerGuiToLocaleFacadeInterface
     */
    protected $localeFacade;

    /**
     * @var \Spryker\Zed\FileManagerGui\Dependency\Facade\FileManagerGuiToFileManagerFacadeInterface
     */
    protected $fileManagerFacade;

    /**
     * @param \Spryker\Zed\FileManagerGui\Dependency\Facade\FileManagerGuiToLocaleFacadeInterface $localeFacade
     * @param \Spryker\Zed\FileManagerGui\Dependency\Facade\FileManagerGuiToFileManagerFacadeInterface $fileManagerFacade
     */
    public function __construct(
        FileManagerGuiToLocaleFacadeInterface $localeFacade,
        FileManagerGuiToFileManagerFacadeInterface $fileManagerFacade
    ) {
        $this->localeFacade = $localeFacade;
        $this->fileManagerFacade = $fileManagerFacade;
    }

    /**
     * @param int|null $idFile
     *
     * @return \Generated\Shared\Transfer\FileTransfer
     */
    public function getData($idFile = null)
    {
        if ($idFile === null) {
            return $this->createEmptyFileTransfer();
        }

        $fileTransfer = $this->fileManagerFacade->findFileByIdFile($idFile)->getFile();
        if ($fileTransfer === null) {
            return $this->createEmptyFileTransfer();
        }

        $this->setLocalizedAttributesLocales($fileTransfer);

        return $fileTransfer;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions()
    {
        return [
            FileForm::OPTION_AVAILABLE_LOCALES => $this->getAvailableLocales(),
            FileForm::OPTION_ALLOWED_MIME_TYPES => $this->getAllowedMimeTypes(),
        ];
    }

    /**
     * @return array<\Generated\Shared\Transfer\LocaleTransfer>
     */
    protected function getAvailableLocales()
    {
        return $this->localeFacade
            ->getLocaleCollection();
    }

    /**
     * @return array
     */
    protected function getAllowedMimeTypes()
    {
        /** @var array<\Generated\Shared\Transfer\MimeTypeTransfer> $mimeTypes */
        $mimeTypes = $this->fileManagerFacade
            ->findAllowedMimeTypes()
            ->getItems()
            ->getArrayCopy();

        return array_map(function (MimeTypeTransfer $mimeTypeTransfer) {
            return $mimeTypeTransfer->getName();
        }, $mimeTypes);
    }

    /**
     * @return \Generated\Shared\Transfer\FileTransfer
     */
    protected function createEmptyFileTransfer()
    {
        $fileTransfer = new FileTransfer();

        foreach ($this->getAvailableLocales() as $locale) {
            $fileLocalizedAttribute = new FileLocalizedAttributesTransfer();
            $fileLocalizedAttribute->setLocale($locale);

            $fileTransfer->addLocalizedAttributes($fileLocalizedAttribute);
        }

        return $fileTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\FileTransfer $fileTransfer
     *
     * @return void
     */
    protected function setLocalizedAttributesLocales(FileTransfer $fileTransfer)
    {
        $locales = $this->getTransformedAvailableLocales($this->getAvailableLocales());

        foreach ($fileTransfer->getLocalizedAttributes() as $attribute) {
            if (isset($locales[$attribute->getFkLocale()])) {
                $attribute->setLocale($locales[$attribute->getFkLocale()]);
            }
        }
    }

    /**
     * @param array $locales
     *
     * @return array
     */
    protected function getTransformedAvailableLocales(array $locales)
    {
        $transformed = [];

        foreach ($locales as $locale) {
            $transformed[$locale->getIdLocale()] = $locale;
        }

        return $transformed;
    }
}
