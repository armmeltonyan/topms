<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;

class ServerContorller extends Controller
{
    public function store(Request $request)
    {
        Server::create(['title'=>$request->title,'user_id'=>auth()->id()]);

        return back();
    }
}
