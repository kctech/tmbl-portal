<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Middleware\EnsureTokenIsValid;

use App\Models\Lead;

use Illuminate\Support\Str;

class LeadApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(EnsureTokenIsValid::class);
    }

    /**
     * Store new lead.
     *
     * @return JSON
     */
    public function store(Request $request)
    {
        dump($request->all());

        //prepare incoming request, check its not empty
        $full_request = $request->all();
        unset($full_request['api_token']);
        if(empty($full_request)){
            return response()->json("Error: Empty Request", 422);
        }

        //prepare new lead
        $new_lead = [];
        $new_lead['uuid'] = Str::uuid();
        $new_lead['status'] = LEAD::NEW_LEAD;
        $new_lead['source_id'] = session('source_id') ?? 0;
        $new_lead['account_id'] = session('account_id') ?? 0;

        // attempt to set default column data
        foreach($request->all() as $key => $value){
            switch(strtolower($key)){
                case 'email':
                case 'email_address':
                case 'emailaddress';
                    $new_lead['email_address'] = trim($value);
                    break;
                case 'contact_number':
                case 'contactnumber':
                case 'phone_number':
                case 'phonenumber':
                    $new_lead['contact_number'] = trim($value);
                    break;
                case 'first_name':
                case 'firstname';
                    $new_lead['first_name'] = trim($value);
                    break;
                case 'last_name':
                case 'lastname';
                    $new_lead['last_name'] = trim($value);
                    break;
                case 'full_name':
                case 'fullname';
                    $split_name = split_name($value);
                    $new_lead['first_name'] = $full_request['first_name'] = trim($split_name->first_name);
                    $new_lead['last_name'] = $full_request['last_name'] = trim($split_name->last_name);
                    break;
            }
            //if all default data populated move on
            if(isset($new_lead['email_address']) && isset($new_lead['contact_number']) && isset($new_lead['first_name']) && isset($new_lead['last_name'])){
                break;
            }
        }

        //remove misc data from payload
        unset($full_request['api_token']);
        unset($full_request['action']);
        unset($full_request['id']);
        unset($full_request['hidden']);
        unset($full_request['type']);
        unset($full_request['triggerIntegration']);
        unset($full_request['fieldLabels']);
        unset($full_request['formcraft3_wpnonce']);
        $new_lead['data'] = json_encode($full_request);

        //attempt save
        if(Lead::create($new_lead)){
            return response()->json("Success", 201);
        }else{
            return response()->json("Error", 422);
        }


    }
}
