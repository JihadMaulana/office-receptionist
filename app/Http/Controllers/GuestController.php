<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * GuestController
 */
class GuestController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Invoke single action controller.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // TODO: Controller logic

        return response()->view('guest');
    }
}
