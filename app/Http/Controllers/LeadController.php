<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the leads dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.leads.dashboard');
    }

    /**
     * Show the lead sources editor.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function sources()
    {
        return view('admin.leads.sources');
    }
}
