<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Money\Communication\Plugin\ServiceProvider;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\MoneyTransfer;
use Silex\Application;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Money\Communication\Plugin\ServiceProvider\TwigMoneyServiceProvider;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * @deprecated Will be removed without replacement.
 *
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Money
 * @group Communication
 * @group Plugin
 * @group ServiceProvider
 * @group TwigMoneyServiceProviderTest
 * Add your own group annotations below this line
 */
class TwigMoneyServiceProviderTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\Money\MoneyCommunicationTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testRegisterShouldAddFilterToTwig(): void
    {
        $moneyServiceProvider = new TwigMoneyServiceProvider();
        $application = new Application();
        $application['twig'] = function () {
            return new Environment(new FilesystemLoader());
        };

        $moneyServiceProvider->register($application);
    }

    /**
     * @return void
     */
    public function testBootShouldDoNothing(): void
    {
        $moneyServiceProvider = new TwigMoneyServiceProvider();
        $application = new Application();
        $moneyServiceProvider->boot($application);
    }

    /**
     * @dataProvider formatTestData
     *
     * @param mixed $input
     * @param string $expected
     * @param string $locale
     * @param bool $withSymbol
     *
     * @return void
     */
    public function testFilterExecution($input, string $expected, string $locale, bool $withSymbol = true): void
    {
        $moneyServiceProvider = new TwigMoneyServiceProvider();
        $application = new Application();
        $application['twig'] = function () {
            return new Environment(new FilesystemLoader());
        };
        $moneyServiceProvider->register($application);

        /** @var \Twig\Environment $twig */
        $twig = $application['twig'];
        $filter = $twig->getFilter('money');

        $callable = $filter->getCallable();

        $this->getLocaleFacade()->setCurrentLocale((new LocaleTransfer())->setLocaleName($locale));

        $result = $callable($input, $withSymbol);
        $this->assertSame($expected, $result);
    }

    /**
     * @return array
     */
    public function formatTestData(): array
    {
        return [
            [$this->createDeMoneyTransfer(), '10,00 €', 'de_DE'],
            [$this->createDeMoneyTransfer(), '10,00', 'de_DE', false],
            [10.00, '10,00 €', 'de_DE'],
            [1000, '10,00 €', 'de_DE'],
            ['1000', '10,00 €', 'de_DE'],
            [$this->createDeMoneyTransfer(), '€10.00', 'en_US'],
            [10.00, '€10.00', 'en_US'],
            [1000, '€10.00', 'en_US'],
            ['1000', '€10.00', 'en_US'],
            [$this->createJpyMoneyTransfer(), '¥1,000', 'en_US'],
            //[$this->createJpyMoneyTransfer(), '1,000', 'en_US', false], // TODO: this case is broken because intl extension can give different results in different environments.
            [$this->createJpyMoneyTransfer(), '1.000 ¥', 'de_DE'],
            //[$this->createJpyMoneyTransfer(), '1.000', 'de_DE', false], // TODO: this case is broken because intl extension can give different results in different environments.
        ];
    }

    /**
     * @return \Generated\Shared\Transfer\MoneyTransfer
     */
    protected function createDeMoneyTransfer(): MoneyTransfer
    {
        $moneyTransfer = new MoneyTransfer();
        $moneyTransfer->setAmount(1000);
        $currencyTransfer = new CurrencyTransfer();
        $currencyTransfer->setCode('EUR');
        $moneyTransfer->setCurrency($currencyTransfer);

        return $moneyTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\MoneyTransfer
     */
    protected function createJpyMoneyTransfer(): MoneyTransfer
    {
        $moneyTransfer = new MoneyTransfer();
        $moneyTransfer->setAmount(1000);
        $currencyTransfer = new CurrencyTransfer();
        $currencyTransfer->setCode('JPY');
        $moneyTransfer->setCurrency($currencyTransfer);

        return $moneyTransfer;
    }

    /**
     * @return \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    protected function getLocaleFacade(): LocaleFacadeInterface
    {
        return $this->tester->getLocator()->locale()->facade();
    }
}
