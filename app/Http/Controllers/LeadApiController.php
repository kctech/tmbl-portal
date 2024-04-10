<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Middleware\EnsureTokenIsValid;

use App\Models\Lead;

use Illuminate\Support\Str;

class LeadApiController extends Controller
{

    protected static $strip_fields = ['api_token','action','id','hidden','type','triggerIntegration','fieldLabels','formcraft3_wpnonce'];

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
        //prepare incoming request, check its not empty
        $full_request = $request->all();
        //dump($full_request);

        if(empty($full_request)){
            return response()->json("Error: Empty Request", 422);
        }

        //prepare new lead
        $new_lead = [];
        $new_lead['uuid'] = Str::uuid();
        $new_lead['status'] = LEAD::PROSPECT;
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
                case 'phone':
                case 'contact_number':
                case 'contactnumber':
                case 'phone_number':
                case 'phonenumber':
                case 'mobile':
                case 'mobile_number':
                case 'mobilenumber':
                    $new_lead['contact_number'] = trim($value);
                    break;
                case 'fname':
                case 'first_name':
                case 'firstname';
                    $new_lead['first_name'] = trim($value);
                    break;
                case 'lname':
                case 'last_name':
                case 'lastname';
                    $new_lead['last_name'] = trim($value);
                    break;
                case 'full_name':
                case 'fullname';
                case 'name':
                    $split_name = split_name($value);
                    $new_lead['first_name'] = $full_request['first_name'] = trim($split_name->first_name);
                    $new_lead['last_name'] = $full_request['last_name'] = trim($split_name->last_name);
                    break;
            }
            //if all default data populated move on
            if(isset($new_lead['email_address']) && isset($new_lead['contact_number']) && isset($new_lead['first_name']) && isset($new_lead['last_name'])){
                break;
            }

            //remove empty variables from saved payload
            if(trim($value) == ""){
                unset($full_request[$key]);
            }

            //stringify arrays
            if(is_array($value)){
                $full_request[$key] = implode(", ", $value);
            }
        }

        //remove misc data from payload
        foreach(self::$strip_fields as $f){
            if(isset($full_request[$f])){
                unset($full_request[$f]);
            }
        }

        $new_lead['data'] = json_encode($full_request);

        //attempt save
        if(Lead::create($new_lead)){
            return response()->json("Success", 201);
        }else{
            return response()->json("Error", 422);
        }


    }
}
