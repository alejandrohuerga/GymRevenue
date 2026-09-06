<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home');
    }

    public function calculator(): View
    {
        return view('pages.calculator');
    }

    public function thanks(): View
    {
        return view('pages.thanks');
    }

    public function howItWorks(): View
    {
        return view('pages.how-it-works');
    }

    public function pricing(): View
    {
        return view('pages.pricing');
    }

    public function legal(): View
    {
        return view('pages.legal');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function cookies(): View
    {
        return view('pages.cookies');
    }
}
