<?php

namespace App\Http\Controllers;

use App\Jobs\duskJob;
use App\Models\Iteration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Application;

class DuskController extends Controller
{
    public function index()
    {
        duskJob::dispatch();

        dd('yoo');

        $app = app(Application::class);
        // Start a new Dusk browser instance
        $browser = new Browser($app);

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

        // Close the browser
        $browser->quit();

        return response()->json(['status' => 'Dusk functionality executed']);
    }

    public function buy(Request $request)
    {
        dd($request->all());
        Iteration::insert(['user_id'=>auth()->id(),'monitoring'=>$request->monitoringName,'iteration'=>$request->iteration]);
        duskJob::dispatch($request->monitoringName);
    }
}
