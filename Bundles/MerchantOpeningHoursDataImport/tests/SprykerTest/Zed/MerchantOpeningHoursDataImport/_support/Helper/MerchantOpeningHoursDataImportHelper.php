<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Zed\MerchantOpeningHoursDataImport\Helper;

use Codeception\Module;
use Orm\Zed\MerchantOpeningHours\Persistence\SpyMerchantOpeningHoursDateScheduleQuery;
use Orm\Zed\MerchantOpeningHours\Persistence\SpyMerchantOpeningHoursWeekdayScheduleQuery;

class MerchantOpeningHoursDataImportHelper extends Module
{
    /**
     * @return void
     */
    public function assertMerchantOpeningHoursDateScheduleDatabaseTablesContainsData(): void
    {
        $merchantOpeningHoursDateScheduleQuery = $this->getMerchantOpeningHoursDateScheduleQuery();

        $this->assertTrue(
            $merchantOpeningHoursDateScheduleQuery->find()->count() > 0,
            'Expected at least one entry in the database table but database table is empty.'
        );
    }

    /**
     * @return void
     */
    public function assertMerchantOpeningHoursWeekdayScheduleDatabaseTablesContainsData(): void
    {
        $merchantOpeningHoursWeekdayScheduleQuery = $this->getMerchantOpeningHoursWeekdayScheduleQuery();

        $this->assertTrue(
            $merchantOpeningHoursWeekdayScheduleQuery->count() > 0,
            'Expected at least one entry in the database table but database table is empty.'
        );
    }

    /**
     * @return \Orm\Zed\MerchantOpeningHours\Persistence\SpyMerchantOpeningHoursDateScheduleQuery
     */
    public function getMerchantOpeningHoursDateScheduleQuery(): SpyMerchantOpeningHoursDateScheduleQuery
    {
        return SpyMerchantOpeningHoursDateScheduleQuery::create();
    }

    /**
     * @return \Orm\Zed\MerchantOpeningHours\Persistence\SpyMerchantOpeningHoursWeekdayScheduleQuery
     */
    public function getMerchantOpeningHoursWeekdayScheduleQuery(): SpyMerchantOpeningHoursWeekdayScheduleQuery
    {
        return SpyMerchantOpeningHoursWeekdayScheduleQuery::create();
    }
}
