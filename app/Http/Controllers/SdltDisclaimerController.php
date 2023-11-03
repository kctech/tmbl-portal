<?php

namespace App\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Jobs\QueueEmail;

use App\Models\SdltDisclaimer;
use App\Models\Client;
use App\Models\User;

class SdltDisclaimerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $consent_status = $request->input('consent_status');
            $consent_additional = $request->input('consent_additional');
            $client_surname = $request->input('client_surname');
            $sort = $request->input('sort');

            $requests = SdltDisclaimer::filter($consent_status, $consent_additional, $client_surname, $sort);

            if ($consent_status == "") $consent_status = 'default';
            if ($consent_additional == "") $consent_additional = 'default';
            if ($sort == "") $sort = 'recent';
        } else {
            $consent_status = 'default';
            $consent_additional ='default';
            $client_surname = '';
            $sort = 'recent';

            $requests = SdltDisclaimer::where('user_id', Session::get('user_id', auth()->id()))->paginate(config('database.pagination_size'));
        }

        return view('admin.sdlt.index')->with(compact('requests'))->with('consent_status', $consent_status)->with('consent_additional', $consent_additional)->with('client_surname', $client_surname)->with('sort', $sort);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clients = Client::where('account_id', Session::get('account_id'))->get(); //->where('user_id', auth()->id())->get();  ||  ::all();
        $link = md5(microtime(true).random_bytes(6));
        return view('admin.sdlt.create',compact('clients'))->with('link',$link);
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
            'consent_type' => 'required',
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
            'last_name_2.required' => 'Need client Last Name for new client 2',
            'email_2.required' => 'Need client Email for new client 2'
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
            return redirect()->route('sdlt-consent.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        //Client 1
        $inserts[] = SdltDisclaimer::create($validator->validated());

        //New Client 2
        if (!empty($validator->validated()['email_2'])) {
            $inserts[] = SdltDisclaimer::create(array(
                'user_id' => $validator->validated()['user_id'],
                'link' => $validator->validated()['link'],
                'consent_type' => $validator->validated()['consent_type'],
                'first_name' => $validator->validated()['first_name_2'],
                'last_name' => $validator->validated()['last_name_2'],
                'email' => $validator->validated()['email_2'],
                'tel' => $validator->validated()['tel_2']
            ));
        }

        //Linked client(s)
        if (!empty($validator->validated()['linked'])) {
            foreach ($validator->validated()['linked'] as $linked_client) {
                $inserts[] = SdltDisclaimer::create(array(
                    'user_id' => $validator->validated()['user_id'],
                    'client_id' => $linked_client,
                    'consent_type' => $validator->validated()['consent_type']
                ));
            }
        }

        if (!empty($inserts)) {
            $counter = 1;
            foreach ($inserts as $insert) {
                if (!empty($insert)) {
                    $statusMsg[] = 'Task was successful for Client '. $counter . '!';

                    $record = SdltDisclaimer::findOrFail($insert['consent_id']);

                    //send emails
                    //Email Data
                    $fields = [
                        'client' => Client::findOrFail($record->client->id),
                        'adviser' => User::findOrFail($record->user->id),
                        'record' => $record
                    ];

                    //Client
                    $client['fields'] = $fields;
                    $client['view'] = 'templated.'.$record->user->account->viewset.'.sdlt.email.client_send';
                    $client['subject'] = "SDLT Disclaimer from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
                    $client['to'] = $record->client->email;
                    $client['from'] = $record->user->email;
                    $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
                    $client['replyTo'] = $record->user->email;
                    dispatch(new QueueEmail($client))->onQueue('clientemails');

                    //Adviser
                    $adviser['fields'] = $fields;
                    $adviser['view'] = 'templated.'.$record->user->account->viewset.'.sdlt.email.adviser_send';
                    $adviser['to'] = $record->user->email;
                    $adviser['subject'] = 'SDLT Disclaimer sent to: '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.')';
                    $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
                    $adviser['replyTo'] = $record->client->email;
                    dispatch(new QueueEmail($adviser))->onQueue('clientemails');
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

        return redirect()->route('sdlt-consent.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SdltDisclaimer  $sdltDisclaimer
     * @return \Illuminate\Http\Response
     */
    public function show(SdltDisclaimer $sdltDisclaimer)
    {
        $this->authorize('anything', $sdltDisclaimer);
        return view('templated.'.Session::get('viewset').'.sdlt.view', compact('SdltDisclaimer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SdltDisclaimer  $sdltDisclaimer
     * @return \Illuminate\Http\Response
     */
    public function edit(SdltDisclaimer $sdltDisclaimer)
    {
        $this->authorize('anything', $sdltDisclaimer);
        return view('admin.sdlt.edit')->with('consent', $sdltDisclaimer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SdltDisclaimer  $sdltDisclaimer
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $sdltDisclaimer = SdltDisclaimer::findOrFail($id);
        $this->authorize('anything', $sdltDisclaimer);

        $validatedData = request()->validate([
            'user_id' => 'required',
            'client_id' => 'required',
            'consent_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'tel' => '',
            'email' => 'required|email'
        ]);
        $validatedData['id'] = $id;
        $validatedData['consent'] = 'N';

        $update = SdltDisclaimer::edit($validatedData);

        if (!empty($update)) {

            Session::flash('alert-success', 'Task was successful!');

            $record = SdltDisclaimer::findOrFail($update['consent_id']);

            //send emails
            //Email Data
            $fields = [
                'client' => Client::findOrFail($record->client->id),
                'adviser' => User::findOrFail($record->user->id),
                'record' => $record
            ];

            //Client
            $client['fields'] = $fields;
            $client['view'] = 'templated.'.$record->user->account->viewset.'.sdlt.email.client_send';
            $client['subject'] = "Updated SDLT Disclaimer from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
            $client['to'] = $record->client->email;
            $client['from'] = $record->user->email;
            $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
            $client['replyTo'] = $record->user->email;
            dispatch(new QueueEmail($client))->onQueue('clientemails');

            //Adviser
            $adviser['fields'] = $fields;
            $adviser['view'] = 'templated.'.$record->user->account->viewset.'.sdlt.email.adviser_send';
            $adviser['to'] = $record->user->email;
            $adviser['subject'] = 'Updated SDLT Disclaimer Confirmation sent to: '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.')';
            $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
            $adviser['replyTo'] = $record->client->email;
            dispatch(new QueueEmail($adviser))->onQueue('clientemails');

        } else {
            Session::flash('alert-danger', 'Task was unsuccessful!');
        }

        //dd(request());
        return redirect()->route('sdlt-consent.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SdltDisclaimer  $sdltDisclaimer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sdltDisclaimer = SdltDisclaimer::findOrFail($id);
        $this->authorize('anything', $sdltDisclaimer);

        $action = SdltDisclaimer::remove($id);
        if ($action) {
            Session::flash('alert-success', 'Request ID '.$id.' deleted');
        } else {
            Session::flash('alert-danger', 'Request ID '.$id.' could not be deleted');
        }

        return redirect()->route('sdlt-consent.index');
    }


    /**
     * Client Responds the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SdltDisclaimer  $sdltDisclaimer
     * @return \Illuminate\Http\Response
     */
    public function respond($uid,$record_id)
    {
        if (request()->isMethod('post')) {

            $validatedData = request()->validate([
                'consent' => 'required',
                'consent_additional' => '',
                'client_id' => 'required',
                'user_id' => '',
                'mkt_phone_consent' => '',
                'mkt_email_consent' => '',
                'mkt_sms_consent' => '',
                'mkt_post_consent' => '',
            ]);
            $validatedData['id'] = $record_id;

            $respond = SdltDisclaimer::respond($validatedData);

            if (!empty($respond)) {

                Session::flash('alert-success', 'Task was successful!');

                $record = SdltDisclaimer::findOrFail($respond['consent_id']);

                //send emails
                //Email Data
                $fields = [
                    'client' => Client::findOrFail($record->client->id),
                    'adviser' => User::findOrFail($record->user->id),
                    'record' => $record
                ];

                //Adviser
                $adviser['fields'] = $fields;
                $adviser['view'] = 'templated.'.$record->user->account->viewset.'.sdlt.email.adviser_response';
                $adviser['to'] = $record->user->email;
                $adviser['subject'] = 'Response from '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.') to SDLT Disclaimer';
                $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
                $adviser['replyTo'] = $record->client->email;
                dispatch(new QueueEmail($adviser))->onQueue('adviseremails');

                return view('templated.'.$record->user->account->viewset.'.sdlt.thanks');

            } else {
                Session::flash('alert-danger', 'Task was unsuccessful!');
                return view('generic.sorry');
            }
        } else {
            //dd(request());
            $record = SdltDisclaimer::findOrFail($record_id);
            session([
                'logo_frontend' => $record->user->account->logo_frontend,
                'css' => $record->user->account->css,
                'viewset' => $record->user->account->viewset
            ]);
            if($record->client->uid == $uid) {
                return view('templated.'.$record->user->account->viewset.'.sdlt.respond', compact('record'));
            }else{
                abort(403);
            }
        }
    }

    /**
     * Adviser Resends email to client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SdltDisclaimer  $sdltDisclaimer
     * @return \Illuminate\Http\Response
     */
    public function resendClient($record_id)
    {
        if (request()->ajax()) {
            $record = SdltDisclaimer::findOrFail($record_id);
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
                $client['view'] = 'templated.'.$record->user->account->viewset.'.sdlt.email.client_send';
                $client['subject'] = "SDLT Disclaimer from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
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
     * Adviser Resends email to themselves.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SdltDisclaimer  $sdltDisclaimer
     * @return \Illuminate\Http\Response
     */
    public function resendAdviser($record_id)
    {
        if (request()->ajax()) {
            $record = SdltDisclaimer::findOrFail($record_id);
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
                $adviser['view'] = 'templated.'.$record->user->account->viewset.'.sdlt.email.adviser_response';
                $adviser['to'] = $record->user->email;
                $adviser['subject'] = 'Response from '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.') to SDLT Disclaimer';
                $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
                $adviser['replyTo'] = $record->client->email;
                dispatch(new QueueEmail($adviser))->onQueue('adviseremails');

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
}
