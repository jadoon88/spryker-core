<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Store;

use Codeception\Actor;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class StoreCommunicationTester extends Actor
{
    use _generated\StoreCommunicationTesterActions;

    /**
     * @param array $storeNameReferenceMap
     *
     * @return void
     */
    public function mockStoreNameReferenceMap(array $storeNameReferenceMap): void
    {
        $this->mockConfigMethod('getStoreNameReferenceMap', $storeNameReferenceMap);
    }
}
