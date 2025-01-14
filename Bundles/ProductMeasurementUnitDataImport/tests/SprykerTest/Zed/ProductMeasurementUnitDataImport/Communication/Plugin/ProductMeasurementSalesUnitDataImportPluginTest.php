<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Zed\ProductMeasurementUnitDataImport\Communication\Plugin;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\DataImporterReportTransfer;
use Spryker\Zed\DataImport\DataImportDependencyProvider;
use Spryker\Zed\ProductMeasurementUnitDataImport\Communication\Plugin\ProductMeasurementBaseUnitDataImportPlugin;
use Spryker\Zed\ProductMeasurementUnitDataImport\Communication\Plugin\ProductMeasurementSalesUnitDataImportPlugin;
use Spryker\Zed\ProductMeasurementUnitDataImport\Communication\Plugin\ProductMeasurementSalesUnitStoreDataImportPlugin;
use Spryker\Zed\ProductMeasurementUnitDataImport\Communication\Plugin\ProductMeasurementUnitDataImportPlugin;
use Spryker\Zed\ProductMeasurementUnitDataImport\ProductMeasurementUnitDataImportConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductMeasurementUnitDataImport
 * @group Communication
 * @group Plugin
 * @group ProductMeasurementSalesUnitDataImportPluginTest
 * Add your own group annotations below this line
 */
class ProductMeasurementSalesUnitDataImportPluginTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\ProductMeasurementUnitDataImport\ProductMeasurementUnitDataImportCommunicationTester
     */
    protected $tester;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->setDependency(
            DataImportDependencyProvider::DATA_IMPORTER_PLUGINS,
            [
                new ProductMeasurementUnitDataImportPlugin(),
                new ProductMeasurementBaseUnitDataImportPlugin(),
                new ProductMeasurementSalesUnitDataImportPlugin(),
                new ProductMeasurementSalesUnitStoreDataImportPlugin(),
            ],
        );
    }

    /**
     * @return void
     */
    public function testImportImportsData(): void
    {
        $this->tester->ensureMeasurementSalesUnitStoreIsEmpty();
        $this->tester->ensureMeasurementSalesUnitIsEmpty();

        $dataDir = codecept_data_dir();
        $this->tester->importMeasurementUnitData($dataDir);
        $this->tester->importMeasurementBaseUnitData($dataDir);
        $dataImporterReportTransfer = $this->tester->importMeasurementSalesUnitData($dataDir);

        $this->assertInstanceOf(DataImporterReportTransfer::class, $dataImporterReportTransfer);

        $this->tester->assertMeasurementSalesUnitContainsData();
    }

    /**
     * @return void
     */
    public function testGetImportTypeReturnsTypeOfImporter(): void
    {
        $productMeasurementSalesUnitDataImportPlugin = new ProductMeasurementSalesUnitDataImportPlugin();
        $this->assertSame(
            ProductMeasurementUnitDataImportConfig::IMPORT_TYPE_PRODUCT_MEASUREMENT_SALES_UNIT,
            $productMeasurementSalesUnitDataImportPlugin->getImportType(),
        );
    }
}
