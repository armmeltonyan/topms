<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://top-ms.ru/account/auth/')
                ->assertSee('Логин')
                ->type('login', 'Akimoff1') //
                ->type('password', 'rh45hHRHE354erf')
                ->press('Авторизация')
                ->pause(2000)
                ->assertPathIs('/cabinet/')
                ->assertSee('Кабинет')
                ->clickLink('Услуги')
                ->pause(2000)
                ->assertPathIs('/cabinet/services/')
                ->assertSee('Внимание! Перед покупкой услуг ознакомьтесь с правилами сервиса: https://top-ms.ru/pages/rules.htm')
//                ->click('.tms-services-select')
//                ->pause(1000)
//                    ->assertSee('Sign in')
                ->screenshot('aut');
        });
    }
}
