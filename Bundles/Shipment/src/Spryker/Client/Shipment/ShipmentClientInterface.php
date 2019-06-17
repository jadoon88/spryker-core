<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Shipment;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentGroupCollectionTransfer;

interface ShipmentClientInterface
{
    /**
     * Specification:
     * - Retrieves active shipment methods for Quote level shipment.
     * - Calculates shipment method delivery time using ShipmentMethodDeliveryTimePluginInterface plugin.
     * - Selects shipment method price for the provided currency and current store.
     * - Overrides shipment method price using ShipmentMethodPricePluginInterface plugin.
     * - Excludes shipment methods which do not have a valid price as a result.
     * - Excludes shipment methods which do not fulfill ShipmentMethodAvailabilityPluginInterface plugin requirements.
     *
     * @api
     *
     * @deprecated Use getAvailableMethodsByShipment() instead.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ShipmentMethodsTransfer
     */
    public function getAvailableMethods(QuoteTransfer $quoteTransfer);

    /**
     * Specification:
     * - Retrieves active shipment methods for every shipment in all shipment groups of the QuoteTransfer.
     * - Calculates shipment method delivery time using ShipmentMethodDeliveryTimePluginInterface plugin.
     * - Selects shipment method price for the provided currency and current store.
     * - Overrides shipment method price using ShipmentMethodPricePluginInterface plugin.
     * - Excludes shipment methods which do not have a valid price as a result.
     * - Excludes shipment methods which do not fulfill ShipmentMethodAvailabilityPluginInterface plugin requirements.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ShipmentGroupCollectionTransfer
     */
    public function getAvailableMethodsByShipment(QuoteTransfer $quoteTransfer): ShipmentGroupCollectionTransfer;
}
