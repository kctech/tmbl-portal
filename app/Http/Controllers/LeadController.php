<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \Maatwebsite\Excel\Excel;
use App\Exports\LeadsExport;

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
    public function export_leads()
    {
        //(new LeadsExport)->queue('leads.xlsx');
        return (new LeadsExport)->download('all_leads_'.date("Y-m-d_H-i-s").'.xlsx', Excel::XLSX);
    }

    /**
     * Show the leads manager.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function flow()
    {
        return view('admin.leads.flow');
    }

    /**
     * Show the leads manager contact.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function managerContact($id,$redirect='leads.manager')
    {
        return view('admin.leads.manager-contact')->with('id', $id)->with('redirect', $redirect);
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
     * Show the lead sources editor.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function email_templates()
    {
        return view('admin.leads.email-templates');
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
     * Show the leads manager contact.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id,$redirect='leads.table')
    {
        return view('admin.leads.edit')->with('id', $id)->with('redirect', $redirect);
    }
}
