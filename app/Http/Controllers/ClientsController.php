<?php

namespace App\Http\Controllers;

use Validator;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ClientsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $consent_status = $request->consent_status ?? "";
        $consent_type = $request->consent_type ?? "";
        $client_search = $request->client_search ?? "";
        $sort = $request->sort ?? "";

        $clients = Client::filter($consent_status, $consent_type, $client_search, $sort);

        if ($consent_status == "") $consent_status = 'default';
        if ($consent_type == "") $consent_type = 'default';
        if ($client_search == "") $client_search = '';
        if ($sort == "") $sort = 'recent';

        return view('admin.clients.index')->with(compact('clients'))->with('consent_status', $consent_status)->with('consent_type', $consent_type)->with('client_search', $client_search)->with('sort', $sort);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $link = md5(microtime(true).random_bytes(6));
        return view('admin.clients.create')->with('link',$link);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //blank vars
        $statusMsg = array();
        $status = 0;

        //Fields
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'link' => '',
            'first_name' => 'requiredif:client_id,0',
            'last_name' => 'requiredif:client_id,0',
            'tel' => '',
            'email_2' => 'sometimes|nullable|email',
            'tel_2' => ''
        ],
        [
            'first_name.requiredif' => 'Need client First Name for new client',
            'last_name.requiredif' => 'Need client Last Name for new client',
            'email.required' => 'Need client Email for new client',
            'first_name_2.required' => 'Need client First Name for new client 2',
            'last_name_2.required' => 'Need client Last Name for new client 2'
        ]);

        $validator->sometimes('email', 'required|email', function ($input) {
            if(empty($input->client_id)) return true;
        });

        //required client 2 name if client 2 email
        $validator->sometimes('first_name_2', 'required', function ($input) {
            if(!empty($input->email_2)) return true;
        });
        $validator->sometimes('last_name_2', 'required', function ($input) {
            if(!empty($input->email_2)) return true;
        });

        $insert = Client::create($validator->validated());

        //Client 1
        if (!empty($insert)) {
            $statusMsg[] = 'Task was successful for Client 1!';
        } else {
            $status = 1;
                $statusMsg[] = 'Task was unsuccessful for Client 1!';
        }

        //Client 2
        if (!empty($validator->validated()['first_name_2'])) {
            $insert2 = Client::create(array(
                'user_id' => $validator->validated()['user_id'],
                'link' => $validator->validated()['link'],
                'first_name' => $validator->validated()['first_name_2'],
                'last_name' => $validator->validated()['last_name_2'],
                'email' => $validator->validated()['email_2'],
                'tel' => $validator->validated()['tel_2']
            ));
            if (!empty($insert2)) {
                $statusMsg[] = 'Task was successful for Client 2!';
            } else {
                $status = 1;
                $statusMsg[] = 'Task was unsuccessful for Client 2!';
            }
        }

        if ($status == 0) {
            $request->session()->flash('alert-success', implode('<br />', $statusMsg));
        } else {
            $request->session()->flash('alert-danger', implode('<br />', $statusMsg));
        }

        return redirect()->route('clients.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        //$user = User::where('id', Session::get('user_id', auth()->id()))->get();
        $this->authorize('show', $client);
        $linked = Client::linked($client->id, $client->link);
        return view('admin.clients.view')->with('client', $client)->with('linked', $linked);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        $this->authorize('edit', $client);
        $users = User::select('users.*')->join('roles', 'roles.id', 'users.role_id')->where('deleted_at',null)->where('users.id','>',0)->where('roles.level','>=', Auth::user()->role->level)->get();
        return view('admin.clients.edit') ->with('client', $client)->with('users', $users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $client = Client::findOrFail($id);
        $this->authorize('anything', $client);

        $validatedData = request()->validate([
            'user_id' => 'required',
            'client_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'tel' => '',
            'email' => 'required|email'
        ]);
        $validatedData['id'] = $id;
        $validatedData['account_id'] = User::where('id', $validatedData['user_id'])->first()->account_id;

        $update = Client::change($validatedData);

        if ($update !== false) {
            Session::flash('alert-success', 'Task was successful!');
        } else {
            Session::flash('alert-danger', 'Task was unsuccessful!');
        }

        //dd(request());
        return redirect()->route('clients.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        //if admin skip gate, else check ability
        if (auth()->user()->role->level > 1) {
            $this->authorize('anything', $client);
        }

        $action = Client::remove($id);
        if ($action) {
            Session::flash('alert-success', 'Client ID '.$id.' deleted');
        } else {
            Session::flash('alert-danger', 'Client ID '.$id.' could not be deleted');
        }

        return redirect()->route('clients.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public static function list()
    {
        $clients = Client::where('user_id', auth()->id())->get();
        if ($clients) {
            return response()->json([
                'status' => 0,
                'clients' => $clients
            ]);
        } else {
            return response()->json(['status' => 1]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function find()
    {
        $client = Client::where('user_id', Session::get('user_id', auth()->id()))->where('email', $request->input('search'))->first();
        if ($client) {
            return response()->json([
                'status' => 0,
                'client' => $client
            ]);
        } else {
            return response()->json(['status' => 1]);
        }
    }

    /**
     * Adviser Resends email to client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function resendClientDetails($record_id)
    {
        if (request()->ajax()) {
            $record = Client::findOrFail($record_id);
            $this->authorize('anything', $record);

            if ($record) {

                //send emails
                //Email Data
                $fields = [
                    'client' => Client::findOrFail($record->client->id),
                    'adviser' => User::findOrFail($record->user->id),
                    'record' => $record
                ];

                //Client
                $client['fields'] = $fields;
                $client['view'] = 'admin.clients.login_details';
                $client['subject'] = "User Login for ".Session::get('acronym')." Client Portal";
                $client['to'] = $record->client->email;
                $client['from'] = $record->user->email;
                $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
                $client['replyTo'] = $record->user->email;
                dispatch(new QueueEmail($client))->onQueue('clientemails');

                return response()->json([
                    'status' => 'success',
                    'title' => __('Email Sent'),
                    'message' => __('The email has been queued for resending to '.$record->client->email)
                ]);

            } else {
                return response()->json([
                    'status' => 'error',
                    'title' => __('Error'),
                    'message' => __('There was a problem fetching the record.')
                ]);
            }
        } else {
            abort(403);
        }

    }

    /**
     * Check for linked clients
     *
     * @return JSON
     */
    public function checkLinked(Request $request)
    {
        if ($request->has('client_id')) {
            $client = Client::find($request->input('client_id'));
            if ($client) {
                $links = Client::where('link', $client->link)->where('id', '!=', $request->input('client_id'))->get();
                return response()->json([
                    'status' => 0,
                    'msg' => "OK",
                    'links' => $links->count(),
                    'clients' => $links->toJson()
                ]);
            }
        }

        // else fail
        return response()->json([
            'status' => 1,
            'msg' => "Couldn't retrieve client information",
            'links' => 0,
            'clients' => []
        ]);
    }

    /**
     * Check for exisiting client
     *
     * @return JSON
     */
    public function checkDuplicate(Request $request)
    {
        $count = 0;

        if ($request->has('email') && !empty($request->input('email'))) {
            $count = Client::where('email', 'LIKE', $request->input('email'))->where('account_id', Session::get('account_id'))->get()->count();
        }

        return response()->json([
            'count' => $count
        ]);
    }

}
