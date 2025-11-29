<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    /**
     * Display the Terms and Conditions page.
     *
     * @return \Illuminate\View\View
     */
    public function terms()
    {
        return view('legal.terms');
    }

    /**
     * Display the Privacy Policy page.
     *
     * @return \Illuminate\View\View
     */
    public function privacy()
    {
        return view('legal.privacy');
    }
}

