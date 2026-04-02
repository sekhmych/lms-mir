<?php

namespace App\Http\Controllers;

use Dcblogdev\MsGraph\Models\MsGraphToken;
use Dcblogdev\MsGraph\MsGraph;
use Illuminate\Http\RedirectResponse;

class MicrosoftAccountController extends Controller
{
    public function connect(MsGraph $msgraph): mixed
    {
        return $msgraph->connect();
    }

    public function disconnect(): RedirectResponse
    {
        MsGraphToken::where('user_id', auth()->id())->delete();

        return redirect()->route('profile.edit')->with('status', 'microsoft-disconnected');
    }
}
