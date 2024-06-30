<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Controller;
use App\Models\Production\Restaurant;
use App\Models\Retail\RetailSession;
use Illuminate\Http\Request;

class CreateRetailSession extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'initial_cash_float' => 'numeric|min:0|required',
            'restaurant_id' => 'required|int|exists:restaurants,id',
        ]);

        $cash = $request->input('initial_cash_float');

        $session = RetailSession::create([
            'restaurant_id' => $request->input('restaurant_id'),
            'initial_cash_float' => $cash,
            'ending_cash_float' => $cash,
            'cashier_id' => auth_id(),
        ]);

        session(['retail_session_id' => $session->id]);
        cache_retail_session($session);

        return redirect(route('pos'));
    }

    public function create()
    {
        return view('pages.create-retail-session')->with([
            'restaurants' => Restaurant::all(),
        ]);
    }
}
