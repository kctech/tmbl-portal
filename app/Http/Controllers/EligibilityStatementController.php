<?php

namespace App\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Jobs\QueueTemplatedEmail;

use App\Models\EligibilityStatement;
use App\Models\Client;
use App\Models\User;

class EligibilityStatementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $statement_status = $request->input('statement_status');
            $statement_type = $request->input('statement_type');
            $client_surname = $request->input('client_surname');
            $sort = $request->input('sort');

            $requests = EligibilityStatement::filter($statement_status, $statement_type, $client_surname, $sort);

            if ($statement_status == "") $statement_status = 'default';
            if ($statement_type == "") $statement_type = 'default';
            if ($sort == "") $sort = 'recent';
        } else {
            $statement_status = 'default';
            $statement_type ='default';
            $client_surname = '';
            $sort = 'recent';

            $requests = EligibilityStatement::where('user_id', Session::get('user_id', auth()->id()))->paginate(config('database.pagination_size'));
        }

        return view('admin.eligibilitystatement.index')->with(compact('requests'))->with('statement_status', $statement_status)->with('statement_type', $statement_type)->with('client_surname', $client_surname)->with('sort', $sort);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clients = Client::where('account_id', Session::get('account_id'))->get(); //->where('user_id', auth()->id())->get();  ||  ::all();
        return view('admin.eligibilitystatement.create',compact('clients'));
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
        $inserts = $statusMsg = array();
        $status = 0;

        //Fields
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'client_id' => 'required',
            'link' => '',
            'statement_type' => 'required',
            'first_name' => 'requiredif:client_id,0',
            'last_name' => 'requiredif:client_id,0',
            'tel' => '',
            'email_2' => 'sometimes|nullable|email',
            'tel_2' => '',
            'linked' => ''
        ],
        [
            'first_name.requiredif' => 'Need client First Name for new client',
            'last_name.requiredif' => 'Need client Last Name for new client',
            'email.required' => 'Need client Email for new client',
            'first_name_2.required' => 'Need client First Name for new client 2',
            'last_name_2.required' => 'Need client Last Name for new client 2'
        ]);

        //required email if new client
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

        if ($validator->fails()) {
            return redirect()->route('eligibilitystatement-consent.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        //Client 1
        $inserts[] = EligibilityStatement::create($validator->validated());

        //New Client 2
        if (!empty($validator->validated()['email_2'])) {
            $inserts[] = EligibilityStatement::create(array(
                'user_id' => $validator->validated()['user_id'],
                'link' => $validator->validated()['link'],
                'statement_type' => $validator->validated()['statement_type'],
                'first_name' => $validator->validated()['first_name_2'],
                'last_name' => $validator->validated()['last_name_2'],
                'email' => $validator->validated()['email_2'],
                'tel' => $validator->validated()['tel_2']
            ));
        }

        //Linked client(s)
        if (!empty($validator->validated()['linked'])) {
            foreach ($validator->validated()['linked'] as $linked_client) {
                $inserts[] = EligibilityStatement::create(array(
                    'user_id' => $validator->validated()['user_id'],
                    'client_id' => $linked_client,
                    'statement_type' => $validator->validated()['statement_type']
                ));
            }
        }

        if (!empty($inserts)) {
            $counter = 1;
            foreach ($inserts as $insert) {
                if (!empty($insert)) {
                    $statusMsg[] = 'Task was successful for Client '. $counter . '!';

                    $record = EligibilityStatement::findOrFail($insert['statement_id']);

                    //send emails
                    //Email Data
                    $fields = [
                        'client' => Client::findOrFail($record->client->id),
                        'adviser' => User::findOrFail($record->user->id),
                        'record' => $record
                    ];

                    //Client
                    $client['fields'] = $fields;
                    $client['view'] = 'templated.'.$record->user->account->viewset.'.eligibilitystatement.type.'.$record->statement_type.'.client_send';
                    $client['subject'] = "Consent Request from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
                    $client['to'] = $record->client->email;
                    $client['from'] = $record->user->email;
                    $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
                    $client['replyTo'] = $record->user->email;
                    dispatch(new QueueTemplatedEmail($client))->onQueue('clientemails');

                    //Adviser
                    $adviser['fields'] = $fields;
                    $adviser['view'] = 'templated.'.$record->user->account->viewset.'.eligibilitystatement.email.adviser_send';
                    $adviser['to'] = $record->user->email;
                    $adviser['subject'] = $record->statement_type.' Consent Request sent to: '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.')';
                    $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
                    $adviser['replyTo'] = $record->client->email;
                    dispatch(new QueueTemplatedEmail($adviser))->onQueue('clientemails');
                } else {
                    $status = 1;
                    $statusMsg[] = 'Task was unsuccessful for Client '. $counter . '!';
                }

                $counter++;
            }
        } else {
            $status = 1;
            $statusMsg[] = 'Task was unsuccessful!';
        }

        if ($status == 0) {
            $request->session()->flash('alert-success', implode('<br />', $statusMsg));
        } else {
            $request->session()->flash('alert-danger', implode('<br />', $statusMsg));
        }

        return redirect()->route('eligibilitystatement-consent.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EligibilityStatement  $eligibilityStatement
     * @return \Illuminate\Http\Response
     */
    public function show(EligibilityStatement $eligibilityStatement)
    {
        $this->authorize('anything', $eligibilityStatement);
        return view('templated.'.Session::get('viewset').'.eligibilitystatement.view')->with('consent', $eligibilityStatement);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EligibilityStatement  $eligibilityStatement
     * @return \Illuminate\Http\Response
     */
    public function edit(EligibilityStatement $eligibilityStatement)
    {
        $this->authorize('anything', $eligibilityStatement);
        return view('admin.eligibilitystatement.edit')->with('consent', $eligibilityStatement);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EligibilityStatement  $eligibilityStatement
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $eligibilityStatement = EligibilityStatement::findOrFail($id);
        $this->authorize('anything', $eligibilityStatement);

        $validatedData = request()->validate([
            'user_id' => 'required',
            'client_id' => 'required',
            'statement_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'tel' => '',
            'email' => 'required|email'
        ]);
        $validatedData['id'] = $id;
        $validatedData['consent'] = 'N';

        $update = EligibilityStatement::edit($validatedData);

        if (!empty($update)) {

            Session::flash('alert-success', 'Task was successful!');

            $record = EligibilityStatement::findOrFail($update['statement_id']);

            //send emails
            //Email Data
            $fields = [
                'client' => Client::findOrFail($record->client->id),
                'adviser' => User::findOrFail($record->user->id),
                'record' => $record
            ];

            //Client
            $client['fields'] = $fields;
            $client['view'] = 'templated.'.$record->user->account->viewset.'.eligibilitystatement.type.'.$record->statement_type.'.client_send';
            $client['subject'] = "Updated Consent Request from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
            $client['to'] = $record->client->email;
            $client['from'] = $record->user->email;
            $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
            $client['replyTo'] = $record->user->email;
            dispatch(new QueueTemplatedEmail($client))->onQueue('clientemails');

            //Adviser
            $adviser['fields'] = $fields;
            $adviser['view'] = 'templated.'.$record->user->account->viewset.'.eligibilitystatement.email.adviser_send';
            $adviser['to'] = $record->user->email;
            $adviser['subject'] = 'Updated '.$record->statement_type.' Consent Request sent to: '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.')';
            $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
            $adviser['replyTo'] = $record->client->email;
            dispatch(new QueueTemplatedEmail($adviser))->onQueue('clientemails');

        } else {
            Session::flash('alert-danger', 'Task was unsuccessful!');
        }

        //dd(request());
        return redirect()->route('eligibilitystatement-consent.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EligibilityStatement  $eligibilityStatement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $eligibilityStatement = EligibilityStatement::findOrFail($id);
        $this->authorize('anything', $eligibilityStatement);

        $action = EligibilityStatement::remove($id);
        if ($action) {
            Session::flash('alert-success', 'Request ID '.$id.' deleted');
        } else {
            Session::flash('alert-danger', 'Request ID '.$id.' could not be deleted');
        }

        return redirect()->route('eligibilitystatement-consent.index');
    }


    /**
     * Client Responds the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EligibilityStatement  $eligibilityStatement
     * @return \Illuminate\Http\Response
     */
    public function respond($uid,$record_id)
    {
        if (request()->isMethod('post')) {

            $validatedData = request()->validate([
                'consent' => 'required',
                'statement_additional' => '',
                'client_id' => 'required',
                'user_id' => '',
                'mkt_phone_consent' => '',
                'mkt_email_consent' => '',
                'mkt_sms_consent' => '',
                'mkt_post_consent' => '',
            ]);
            $validatedData['id'] = $record_id;

            $respond = EligibilityStatement::respond($validatedData);

            if (!empty($respond)) {

                Session::flash('alert-success', 'Task was successful!');

                $record = EligibilityStatement::findOrFail($respond['statement_id']);

                //send emails
                //Email Data
                $fields = [
                    'client' => Client::findOrFail($record->client->id),
                    'adviser' => User::findOrFail($record->user->id),
                    'record' => $record
                ];

                //Adviser
                $adviser['fields'] = $fields;
                $adviser['view'] = 'templated.'.$record->user->account->viewset.'.eligibilitystatement.type.'.$record->statement_type.'.adviser_response';
                $adviser['to'] = $record->user->email;
                $adviser['subject'] = 'Response from '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.') to '.$record->statement_type.' Consent Request';
                $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
                $adviser['replyTo'] = $record->client->email;
                dispatch(new QueueTemplatedEmail($adviser))->onQueue('adviseremails');

                return view('templated.'.$record->user->account->viewset.'.eligibilitystatement.type.'.$record->statement_type.'.thanks');

            } else {
                Session::flash('alert-danger', 'Task was unsuccessful!');
                return view('generic.sorry');
            }
        } else {
            //dd(request());
            $record = EligibilityStatement::findOrFail($record_id);
            session([
                'logo_frontend' => $record->user->account->logo_frontend,
                'css' => $record->user->account->css,
                'viewset' => $record->user->account->viewset
            ]);
            if($record->client->uid == $uid) {
                return view('templated.'.$record->user->account->viewset.'.eligibilitystatement.type.'.$record->statement_type.'.respond', compact('record'));
            }else{
                abort(403);
            }
        }
    }

    /**
     * Adviser Resends email to client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EligibilityStatement  $eligibilityStatement
     * @return \Illuminate\Http\Response
     */
    public function resendClient($record_id)
    {
        if (request()->ajax()) {
            $record = EligibilityStatement::findOrFail($record_id);
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
                $client['view'] = 'templated.'.$record->user->account->viewset.'.eligibilitystatement.type.'.$record->statement_type.'.client_send';
                $client['subject'] = "Consent Request from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
                $client['to'] = $record->client->email;
                $client['from'] = $record->user->email;
                $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
                $client['replyTo'] = $record->user->email;
                dispatch(new QueueTemplatedEmail($client))->onQueue('clientemails');

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
     * Adviser Resends email to themselves.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EligibilityStatement  $eligibilityStatement
     * @return \Illuminate\Http\Response
     */
    public function resendAdviser($record_id)
    {
        if (request()->ajax()) {
            $record = EligibilityStatement::findOrFail($record_id);
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
                $adviser['fields'] = $fields;
                $adviser['view'] = 'templated.'.$record->user->account->viewset.'.eligibilitystatement.type.'.$record->statement_type.'.adviser_response';
                $adviser['to'] = $record->user->email;
                $adviser['subject'] = 'Response from '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.') to '.$record->statement_type.' Consent Request';
                $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
                $adviser['replyTo'] = $record->client->email;
                dispatch(new QueueTemplatedEmail($adviser))->onQueue('adviseremails');

                return response()->json([
                    'status' => 'success',
                    'title' => __('Email Sent'),
                    'message' => __('The email has been queued for resending to '.$record->user->email)
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
}
