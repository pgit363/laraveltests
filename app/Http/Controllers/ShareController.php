<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function shareButtons()
    {
        $socialShares = \Share::page(
            'https://www.pranavkamble.in',
            'hello this is dummy text',
        )
        ->facebook()
        ->twitter()
        ->linkedin()
        ->telegram()
        ->whatsapp()        
        ->reddit();
        
        return view('share', compact('socialShares'));
    }
}
