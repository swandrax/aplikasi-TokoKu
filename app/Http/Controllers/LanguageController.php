<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLang(string $lang)
    {
        $locales = (array) config('app.locales', ['id' => 'Indonesian', 'en' => 'English']);
        if (array_key_exists($lang, $locales)) {
            Session::put('applocale', $lang);
        }
        return redirect()->back();
    }
}
