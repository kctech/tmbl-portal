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
     * Show the leads manager.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function manager()
    {
        return view('admin.leads.manager');
    }

    /**
     * Show the leads manager.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function contact($id)
    {
        return view('admin.leads.contact')->with('id', $id);
    }

    /**
     * Show the leads table.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function table()
    {
        return view('admin.leads.table');
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

    /**
     * Show the lead sources editor.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function chasers()
    {
        return view('admin.leads.chasers');
    }

    /**
     * Show the adviser availability calendar.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adviserAvailability()
    {
        return view('admin.leads.adviser-availability');
    }
}
