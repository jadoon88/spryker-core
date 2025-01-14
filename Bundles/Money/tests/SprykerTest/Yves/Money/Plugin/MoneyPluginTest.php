<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Yves\Money\Plugin;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\MoneyTransfer;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Money\Plugin\MoneyPlugin;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Yves
 * @group Money
 * @group Plugin
 * @group MoneyPluginTest
 * Add your own group annotations below this line
 */
class MoneyPluginTest extends Unit
{
    /**
     * @var int
     */
    public const AMOUNT_INTEGER = 1000;

    public const AMOUNT_FLOAT = 10.00;

    /**
     * @var string
     */
    public const AMOUNT_STRING = '1000';

    /**
     * @var string
     */
    public const CURRENCY_EUR = 'EUR';

    /**
     * @var string
     */
    public const LOCALE_DE_DE = 'de_DE';

    /**
     * @var string
     */
    public const LOCALE_EN_US = 'en_US';

    /**
     * @return void
     */
    public function testFromIntegerShouldReturnMoneyTransfer(): void
    {
        $moneyPlugin = new MoneyPlugin();
        $moneyTransfer = $moneyPlugin->fromInteger(static::AMOUNT_INTEGER);

        $this->assertInstanceOf(MoneyTransfer::class, $moneyTransfer);
        $this->assertSame(static::AMOUNT_STRING, $moneyTransfer->getAmount());
    }

    /**
     * @return void
     */
    public function testFromFloatShouldReturnMoneyTransfer(): void
    {
        $moneyPlugin = new MoneyPlugin();
        $moneyTransfer = $moneyPlugin->fromFloat(static::AMOUNT_FLOAT);

        $this->assertInstanceOf(MoneyTransfer::class, $moneyTransfer);
        $this->assertSame(static::AMOUNT_STRING, $moneyTransfer->getAmount());
    }

    /**
     * @return void
     */
    public function testFromStringShouldReturnMoneyTransfer(): void
    {
        $moneyPlugin = new MoneyPlugin();
        $moneyTransfer = $moneyPlugin->fromString(static::AMOUNT_STRING);

        $this->assertInstanceOf(MoneyTransfer::class, $moneyTransfer);
        $this->assertSame(static::AMOUNT_STRING, $moneyTransfer->getAmount());
    }

    /**
     * @return void
     */
    public function testGetMoneyShouldReturnMoneyTransferWithConfiguredDefaultCurrency(): void
    {
        $moneyPlugin = new MoneyPlugin();
        $moneyTransfer = $moneyPlugin->fromInteger(static::AMOUNT_INTEGER);

        $this->assertInstanceOf(MoneyTransfer::class, $moneyTransfer);
        $this->assertSame(static::AMOUNT_STRING, $moneyTransfer->getAmount());

        $this->assertSame(static::CURRENCY_EUR, $moneyTransfer->getCurrency()->getCode());
    }

    /**
     * @return void
     */
    public function testGetMoneyShouldReturnMoneyTransferWithPassedCurrency(): void
    {
        $moneyPlugin = new MoneyPlugin();

        $isoCode = 'USD';
        $moneyTransfer = $moneyPlugin->fromInteger(static::AMOUNT_INTEGER, $isoCode);
        $this->assertInstanceOf(MoneyTransfer::class, $moneyTransfer);
        $this->assertSame(static::AMOUNT_STRING, $moneyTransfer->getAmount());

        $this->assertSame($isoCode, $moneyTransfer->getCurrency()->getCode());
    }

    /**
     * @return void
     */
    public function testFormatWithSymbolShouldReturnFormattedStringWithCurrencySymbol(): void
    {
        Store::getInstance()->setCurrentLocale(static::LOCALE_DE_DE);

        $moneyPlugin = new MoneyPlugin();
        $moneyTransfer = $moneyPlugin->fromInteger(static::AMOUNT_INTEGER, static::CURRENCY_EUR);

        $this->assertSame('10,00 €', $moneyPlugin->formatWithSymbol($moneyTransfer));
    }

    /**
     * @return void
     */
    public function testFormatWithoutSymbolShouldReturnFormattedStringWithoutCurrencySymbol(): void
    {
        $moneyPlugin = new MoneyPlugin();
        $moneyTransfer = $moneyPlugin->fromInteger(static::AMOUNT_INTEGER, static::CURRENCY_EUR);

        $this->assertSame('10,00', $moneyPlugin->formatWithoutSymbol($moneyTransfer));
    }

    /**
     * @dataProvider parseData
     *
     * @param string $value
     * @param string $isoCode
     * @param string $expectedAmount
     *
     * @return void
     */
    public function testParseShouldReturnMoneyTransfer(string $value, string $isoCode, string $expectedAmount): void
    {
        $moneyPlugin = new MoneyPlugin();
        $currencyTransfer = new CurrencyTransfer();
        $currencyTransfer->setCode($isoCode);

        $this->assertSame($expectedAmount, $moneyPlugin->parse($value, $currencyTransfer)->getAmount());
    }

    /**
     * @return array
     */
    public function parseData(): array
    {
        return [
            ['10,00 €', 'EUR', '1000'],
            ['10,99 €', 'EUR', '1099'],
            ['10,999 €', 'EUR', '1100'],
            ['1000 ¥', 'JPY', '1000'],
            ['1099 ¥', 'JPY', '1099'],
        ];
    }

    /**
     * @return void
     */
    public function testConvertIntegerToDecimalShouldReturnFloat(): void
    {
        $moneyPlugin = new MoneyPlugin();
        $converted = $moneyPlugin->convertIntegerToDecimal(1000);
        $this->assertIsFloat($converted);
        $this->assertSame(10.00, $converted);
    }

    /**
     * @return void
     */
    public function testConvertDecimalToIntegerShouldReturnInt(): void
    {
        $moneyPlugin = new MoneyPlugin();
        $converted = $moneyPlugin->convertDecimalToInteger(10.00);
        $this->assertIsInt($converted);
        $this->assertSame(1000, $converted);
    }
}
