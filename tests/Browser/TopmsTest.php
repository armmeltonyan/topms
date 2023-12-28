<?php

namespace Tests\Browser;

use App\Models\Iteration;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Config;

class TopmsTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $processId = Cache::get('dusk.data');
        $processId = 1;
        $configs = Iteration::where('id',$processId)->first();
        echo $processId;
        $roundsId = $configs->iteration.' круг';
        $this->browse(function (Browser $browser) use ($processId,$configs,$roundsId) {
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
                ->waitForText($configs->server_name)->clickLink($configs->server_name)
//            $browser = tap($browser)->script("document.evaluate('//td[@class=\"tms-services-select\"]/label[contains(., \"{$configs->server_name}\")]/p', document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.click();");
//                $browser->assertSee('Управление услугами')
//                ->click('.tms-services-select')
//                $browser
                    ->pause(1000)
                    ->waitForText('Управление услугами')
//                    ->click('td.tms-services-select input[name="management_service"][value="boost"]')
                    ->click('td.tms-services-select img[src="/media/services/boost.png"]')

                    ->pause(500)
                    ->waitFor('.uk-button',10)->click('.uk-button')
                    ->pause(500)
                    ->waitForText('Выберите количество кругов')
                    ->pause(1000)
                    ->waitFor('#service_period',10)
                    ->waitForText('1 круг - 15 руб')->clickLink('1 круг - 15 руб')
//            $browser = tap($browser)->script("document.evaluate('//p[@class=\"tms-services-select\"]/label[contains(., \"{$roundsId}\")]/b', document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.click();");
////                    ->click('input[name="service_period"][value="'.$configs->iteration.'"]')
////                    ->radio('service_period', $configs->iteration)->check()
////                    ->click('#'.$configs->iteration.'_period')
//                    $browser->pause(200)
//                    ->assertSee('Sign in')
                ->screenshot('topms'.auth()->id().'_'.now());
            Iteration::where('id',$processId)->update(['processed'=>1]);
        });
//        Iteration::where('id',$processId)->update(['processed'=>1]);
////        try {
//            $iteration = Iteration::find($processId);
//            $iteration->processed = true;
//            $iteration->save();
//        }catch (\Exception $exception){
//            Log::error($exception->getMessage());
//        }

    }

    /**
     * Click on an element by its text content.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $text
     * @return void
     */
    public function clickByText(Browser $browser, $text)
    {
        $browser->script("document.evaluate('//button[contains(text(), \"$text\")]', document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.click();");
    }

    /**
     * Click on an element by its text content within a <p> tag.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $text
     * @return void
     */
    public function clickByTextInParagraph(Browser $browser, $text)
    {
        $browser->script("document.evaluate('//b[contains(text(), \"$text\")]', document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.click();");
    }
}
