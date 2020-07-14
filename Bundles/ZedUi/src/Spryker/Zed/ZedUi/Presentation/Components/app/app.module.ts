import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { CustomElementModule } from '@spryker/web-components';
import { NotificationModule, NotificationComponent } from '@spryker/notification';
import { LocaleModule, LocaleSwitcherComponent } from '@spryker/locale';
import { EnLocaleModule, EN_LOCALE } from '@spryker/locale/locales/en';
import { DeLocaleModule } from '@spryker/locale/locales/de';

import { LayoutCenteredComponent } from './layout-centered/layout-centered.component';
import { LayoutCenteredModule } from './layout-centered/layout-centered.module';
import { HeaderComponent } from './header/header.component';
import { HeaderModule } from './header/header.module';
import { LayoutMainModule } from './layout-main/layout-main.module';
import { LayoutMainComponent } from './layout-main/layout-main.component';
import { MerchantLayoutCenteredModule } from './merchant-layout-centered/merchant-layout-centered.module';
import { MerchantLayoutCenteredComponent } from './merchant-layout-centered/merchant-layout-centered.component';
import { MerchantLayoutMainModule } from './merchant-layout-main/merchant-layout-main.module';
import { MerchantLayoutMainComponent } from './merchant-layout-main/merchant-layout-main.component';

@NgModule({
    imports: [
        BrowserModule,
        BrowserAnimationsModule,
        HttpClientModule,
        LayoutCenteredModule,
	    MerchantLayoutCenteredModule,
	    MerchantLayoutMainModule,
        LayoutMainModule,
        HeaderModule,
        NotificationModule,
        LocaleModule.forRoot({ defaultLocale: EN_LOCALE }),
        EnLocaleModule,
        DeLocaleModule,
    ],
    providers: [],
})
export class AppModule extends CustomElementModule {
    protected components = [
        {
            selector: 'web-mp-layout-centered',
            component: LayoutCenteredComponent,
        },
	    {
		    selector: 'web-mp-layout-main',
		    component: LayoutMainComponent,
	    },
	    {
		    selector: 'web-mp-merchant-layout-centered',
		    component: MerchantLayoutCenteredComponent,
	    },
	    {
		    selector: 'web-mp-merchant-layout-main',
		    component: MerchantLayoutMainComponent,
	    },
        {
            selector: 'web-mp-header',
            component: HeaderComponent,
        },
        {
            selector: 'web-spy-notification',
            component: NotificationComponent,
        },
        {
            selector: 'web-spy-locale-switcher',
            component: LocaleSwitcherComponent,
        },
    ];
}
