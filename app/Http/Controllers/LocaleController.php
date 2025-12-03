<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch application locale
     */
    public function switch(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:en,hy,ge,ru',
        ]);

        $locale = $request->input('locale');
        
        App::setLocale($locale);
        Session::put('locale', $locale);

        return back();
    }
}

