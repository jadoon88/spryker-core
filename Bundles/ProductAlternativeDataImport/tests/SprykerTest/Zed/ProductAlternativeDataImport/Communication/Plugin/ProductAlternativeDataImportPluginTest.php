<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Zed\ProductAlternativeDataImport\Communication\Plugin;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\DataImporterConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterReaderConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterReportTransfer;
use Spryker\Zed\DataImport\Business\Exception\DataImportException;
use Spryker\Zed\ProductAlternativeDataImport\Communication\Plugin\ProductAlternativeDataImportPlugin;
use Spryker\Zed\ProductAlternativeDataImport\ProductAlternativeDataImportConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductAlternativeDataImport
 * @group Communication
 * @group Plugin
 * @group ProductAlternativeDataImportPluginTest
 * Add your own group annotations below this line
 */
class ProductAlternativeDataImportPluginTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\ProductAlternativeDataImport\ProductAlternativeDataImportCommunicationTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testImportImportsProductAlternative(): void
    {
        // Arrange
        $this->tester->ensureDatabaseTableIsEmpty();
        $dataImportConfigurationTransfer = $this->getDataImportConfigurationTransfer('import/product_alternative.csv');
        $dataImportConfigurationTransfer->setThrowException(true);
        $productAlternativeDataImportPlugin = new ProductAlternativeDataImportPlugin();

        // Act
        $dataImporterReportTransfer = $productAlternativeDataImportPlugin->import($dataImportConfigurationTransfer);

        // Assert
        $this->assertInstanceOf(DataImporterReportTransfer::class, $dataImporterReportTransfer);
        $this->tester->assertDatabaseTableContainsData();
    }

    /**
     * @return void
     */
    public function testImportThrowsExceptionWhenProductNotFound(): void
    {
        // Arrange
        $this->tester->ensureDatabaseTableIsEmpty();
        $dataImportConfigurationTransfer = $this->getDataImportConfigurationTransfer(
            'import/product_alternative_with_invalid_sku.csv',
        );
        $dataImportConfigurationTransfer->setThrowException(true);

        // Assert
        $productAlternativeDataImportPlugin = new ProductAlternativeDataImportPlugin();
        $this->expectException(DataImportException::class);
        $this->expectExceptionMessage('Could not find product by sku "999999999"');

        $productAlternativeDataImportPlugin->import($dataImportConfigurationTransfer);
    }

    /**
     * @return void
     */
    public function testImportThrowsExceptionWhenProductHasNeitherConcreteNorAbstractAlternatives(): void
    {
        // Arrange
        $this->tester->ensureDatabaseTableIsEmpty();
        $dataImportConfigurationTransfer = $this->getDataImportConfigurationTransfer(
            'import/product_alternative_with_no_alternatives.csv',
        );
        $dataImportConfigurationTransfer->setThrowException(true);

        // Assert
        $productAlternativeDataImportPlugin = new ProductAlternativeDataImportPlugin();
        $this->expectException(DataImportException::class);
        $this->expectExceptionMessage('Product concrete with SKU "999999999" has neither concrete nor abstract alternative products');

        $productAlternativeDataImportPlugin->import($dataImportConfigurationTransfer);
    }

    /**
     * @return void
     */
    public function testImportThrowsExceptionWhenProductHasBothConcreteAndAbstractAlternatives(): void
    {
        // Arrange
        $this->tester->ensureDatabaseTableIsEmpty();
        $dataImportConfigurationTransfer = $this->getDataImportConfigurationTransfer(
            'import/product_alternative_with_both_alternatives.csv',
        );
        $dataImportConfigurationTransfer->setThrowException(true);

        // Assert
        $productAlternativeDataImportPlugin = new ProductAlternativeDataImportPlugin();
        $this->expectException(DataImportException::class);
        $this->expectExceptionMessage('Product concrete with SKU "999999999" has both a concrete and an abstract alternative products');

        $productAlternativeDataImportPlugin->import($dataImportConfigurationTransfer);
    }

    /**
     * @return void
     */
    public function testGetImportTypeReturnsTypeOfImporter(): void
    {
        // Assert
        $productAlternativeDataImportPlugin = new ProductAlternativeDataImportPlugin();

        $this->assertSame(
            ProductAlternativeDataImportConfig::IMPORT_TYPE_PRODUCT_ALTERNATIVE,
            $productAlternativeDataImportPlugin->getImportType(),
        );
    }

    /**
     * @param string $file
     *
     * @return \Generated\Shared\Transfer\DataImporterConfigurationTransfer
     */
    protected function getDataImportConfigurationTransfer(string $file): DataImporterConfigurationTransfer
    {
        $dataImporterReaderConfigurationTransfer = new DataImporterReaderConfigurationTransfer();
        $dataImporterReaderConfigurationTransfer->setFileName(codecept_data_dir() . $file);

        $dataImportConfigurationTransfer = new DataImporterConfigurationTransfer();
        $dataImportConfigurationTransfer->setReaderConfiguration($dataImporterReaderConfigurationTransfer);

        return $dataImportConfigurationTransfer;
    }
}
