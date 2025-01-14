<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\CmsPageDataImport\Business\CmsPage;

use Spryker\Zed\CmsPageDataImport\Business\DataSet\CmsPageDataSet;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\AddLocalesStep;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class PlaceholderExtractorStep implements DataImportStepInterface
{
    /**
     * @var array<string>
     */
    protected $placeholderNames;

    /**
     * @param array<string> $attributeNames
     */
    public function __construct(array $attributeNames)
    {
        $this->placeholderNames = $attributeNames;
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $localizedPlaceholder = [];
        foreach ($dataSet[AddLocalesStep::KEY_LOCALES] as $localeName => $idLocale) {
            $placeholder = [];

            foreach ($this->placeholderNames as $placeholderName) {
                $key = str_replace('placeholder.', '', $placeholderName);
                $placeholder[$key] = $dataSet[$placeholderName . '.' . $localeName];
            }

            $localizedPlaceholder[$idLocale] = $placeholder;
        }

        $dataSet[CmsPageDataSet::KEY_PLACEHOLDER] = $localizedPlaceholder;
    }
}
