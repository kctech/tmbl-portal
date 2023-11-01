<?php

namespace App\Http\Controllers;

use Validator;
Use Storage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Jobs\QueueEmail;

use App\TermsConsent;
use App\Client;
use App\User;

class TermsConsentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $services = array(
            'MR' => 'Mortgage + Referred Protection',
            'MP' => 'Mortgage + Protection',
            'P' => 'Protection Only',
        );

        if ($request->isMethod('post')) {
            $consent_status = $request->input('consent_status');
            $timing = $request->input('timing');
            $service = $request->input('service');
            $client_surname = $request->input('client_surname');
            $sort = $request->input('sort');

            $requests = TermsConsent::filter($consent_status, $timing, $service, $client_surname, $sort);

            if ($consent_status == "") $consent_status = 'default';
            if ($timing == "") $timing = 'default';
            if ($service == "") $service = 'default';
            if ($sort == "") $sort = 'recent';
        } else {
            $consent_status = 'default';
            $timing = 'default';
            $service = $request->get('service', 'default');
            $client_surname = '';
            $sort = 'recent';

            $requests = TermsConsent::where('user_id', Session::get('user_id', auth()->id()));
            if ($service != "default") {
                $requests = $requests->where('service', $service);
            }
            $requests = $requests->paginate(config('database.pagination_size'));
            
        }

        return view('admin.terms.index')->with(compact('requests'))->with('services', $services)->with('consent_status', $consent_status)->with('timing', $timing)->with('service', $service)->with('client_surname', $client_surname)->with('sort', $sort);
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
        return view('admin.terms.create',compact('clients'))->with('link',$link);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createProtection()
    {
        $clients = Client::where('account_id', Session::get('account_id'))->get(); //->where('user_id', auth()->id())->get();  ||  ::all();
        $link = md5(microtime(true).random_bytes(6));
        return view('admin.terms.create_protection',compact('clients'))->with('link',$link);
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
            'type' => 'required',
            'amount' => 'required',
            'timing' => 'required',
            'type_2' => '',
            'amount_2' => 'sometimes|numeric',
            'timing_2' => '',
            'service' => 'required',
            'description' => 'required',
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
            return redirect()->route('terms-consent.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        //Client 1
        $inserts[] = TermsConsent::create($validator->validated());

        //New Client 2
        if (!empty($validator->validated()['email_2'])) {
            $inserts[] = TermsConsent::create(array(
                'user_id' => $validator->validated()['user_id'],
                'link' => $validator->validated()['link'],
                'type' => $validator->validated()['type'],
                'amount' => $validator->validated()['amount'],
                'timing' => $validator->validated()['timing'],
                'type_2' => $validator->validated()['type_2'],
                'amount_2' => $validator->validated()['amount_2'],
                'timing_2' => $validator->validated()['timing_2'],
                'service' => $validator->validated()['service'],
                'description' => $validator->validated()['description'],
                'first_name' => $validator->validated()['first_name_2'],
                'last_name' => $validator->validated()['last_name_2'],
                'email' => $validator->validated()['email_2'],
                'tel' => $validator->validated()['tel_2']
            ));
        }

        //Linked client(s)
        if (!empty($validator->validated()['linked'])) {
            foreach ($validator->validated()['linked'] as $linked_client) {
                $inserts[] = TermsConsent::create(array(
                    'user_id' => $validator->validated()['user_id'],
                    'client_id' => $linked_client,
                    'type' => $validator->validated()['type'],
                    'amount' => $validator->validated()['amount'],
                    'timing' => $validator->validated()['timing'],
                    'type_2' => $validator->validated()['type_2'],
                    'amount_2' => $validator->validated()['amount_2'],
                    'timing_2' => $validator->validated()['timing_2'],
                    'service' => $validator->validated()['service'],
                    'description' => $validator->validated()['description']
                ));
            }
        }

        if (!empty($inserts)) {
            $counter = 1;
            foreach ($inserts as $insert) {
                if (!empty($insert)) {
                    $statusMsg[] = 'Task was successful for Client '. $counter . '!';

                    $record = TermsConsent::findOrFail($insert['consent_id']);

                    //send emails
                    //Email Data
                    $fields = [
                        'client' => Client::findOrFail($record->client->id),
                        'adviser' => User::findOrFail($record->user->id),
                        'record' => $record
                    ];

                    //Client
                    $client['fields'] = $fields;
                    $client['view'] = 'templated.'.$record->user->account->viewset.'.terms.email.client_send';
                    $client['subject'] = "Business Terms Consent Request from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
                    $client['to'] = $record->client->email;
                    $client['from'] = $record->user->email;
                    $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
                    $client['replyTo'] = $record->user->email;
                    $client['attachments'] = array(
                        array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.business', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_terms.pdf'),
                        array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.promise', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_promise.pdf'),
                        array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.privacy', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_privacy.pdf')
                    );
                    dispatch(new QueueEmail($client))->onQueue('clientemails');

                    //Adviser
                    $adviser['fields'] = $fields;
                    $adviser['view'] = 'templated.'.$record->user->account->viewset.'.terms.email.adviser_send';
                    $adviser['to'] = $record->user->email;
                    $adviser['subject'] = $record->consent_type.' Business Terms Consent Request sent to: '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.')';
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

        return redirect()->route('terms-consent.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TermsConsent  $termsConsent
     * @return \Illuminate\Http\Response
     */
    public function show(TermsConsent $termsConsent)
    {
        $this->authorize('anything', $termsConsent);
        return view('templated.'.Session::get('viewset').'.terms.view', compact('TermsConsent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TermsConsent  $termsConsent
     * @return \Illuminate\Http\Response
     */
    public function edit(TermsConsent $termsConsent)
    {
        $this->authorize('anything', $termsConsent);
        return view('admin.terms.edit')->with('consent', $termsConsent);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TermsConsent  $termsConsent
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $termsConsent = TermsConsent::findOrFail($id);
        $this->authorize('anything', $termsConsent);

        $validatedData = request()->validate([
            'user_id' => 'required',
            'client_id' => 'required',
            'type' => 'required',
            'amount' => 'required',
            'timing' => 'required',
            'type_2' => '',
            'amount_2' => '',
            'timing_2' => '',
            'description' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'tel' => '',
            'email' => 'required|email'
        ]);
        $validatedData['id'] = $id;
        $validatedData['privacy_consent'] = 'N';
        $validatedData['terms_consent'] = 'N';
        $validatedData['signature'] = null;

        $update = TermsConsent::edit($validatedData);

        if (!empty($update)) {

            Session::flash('alert-success', 'Task was successful!');

            $record = TermsConsent::findOrFail($update['consent_id']);

            //clear old files
            Storage::disk('documents')->delete($record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_terms.pdf');
            Storage::disk('documents')->delete($record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_promise.pdf');
            Storage::disk('documents')->delete($record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_privacy.pdf');

            //send emails
            //Email Data
            $fields = [
                'client' => Client::findOrFail($record->client->id),
                'adviser' => User::findOrFail($record->user->id),
                'record' => $record
            ];

            //Client
            $client['fields'] = $fields;
            $client['view'] = 'templated.'.$record->user->account->viewset.'.terms.email.client_send';
            $client['subject'] = "Updated Business Terms Consent Request from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
            $client['to'] = $record->client->email;
            $client['from'] = $record->user->email;
            $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
            $client['replyTo'] = $record->user->email;
            $client['attachments'] = array(
                array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.business', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_terms.pdf'),
                array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.promise', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_promise.pdf'),
                array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.privacy', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_privacy.pdf')
            );
            dispatch(new QueueEmail($client))->onQueue('clientemails');

            //Adviser
            $adviser['fields'] = $fields;
            $adviser['view'] = 'templated.'.$record->user->account->viewset.'.terms.email.adviser_send';
            $adviser['to'] = $record->user->email;
            $adviser['subject'] = 'Updated '.$record->consent_type.' Business Terms Consent Request sent to: '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.')';
            $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
            $adviser['replyTo'] = $record->client->email;
            dispatch(new QueueEmail($adviser))->onQueue('clientemails');

        } else {
            Session::flash('alert-danger', 'Task was unsuccessful!');
        }

        //dd(request());
        return redirect()->route('terms-consent.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TermsConsent  $termsConsent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $termsConsent = TermsConsent::findOrFail($id);
        $this->authorize('anything', $termsConsent);

        $action = TermsConsent::remove($id);
        if ($action) {
            Session::flash('alert-success', 'Request ID '.$id.' deleted');
        } else {
            Session::flash('alert-danger', 'Request ID '.$id.' could not be deleted');
        }

        return redirect()->route('terms-consent.index');
    }

    /**
     * Client Responds the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TermsConsent  $termsConsent
     * @return \Illuminate\Http\Response
     */
    public function respond($uid,$record_id)
    {
        if (request()->isMethod('post')) {

            $validatedData = request()->validate([
                'privacy_consent' => 'required',
                'terms_consent' => 'required',
                'signature' => 'required',
                'client_id' => 'required',
                'user_id' => '',
                'comm_email_consent' => '',
                'comm_phone_consent' => '',
                'comm_sms_consent' => '',
                'comm_face_consent' => '',
                'comm_thirdparty_consent' => '',
                'comm_other_consent' => '',
                'mkt_post_consent' => '',
                'mkt_automatedcall_consent' => '',
                'mkt_web_consent' => '',
                'mkt_email_consent' => '',
                'mkt_phone_consent' => '',
                'mkt_sms_consent' => '',
                'mkt_face_consent' => '',
                'mkt_thirdparty_consent' => '',
                'mkt_other_consent' => '',
                'client_id_2' => '',
                'comm_email_consent_2' => '',
                'comm_phone_consent_2' => '',
                'comm_sms_consent_2' => '',
                'comm_face_consent_2' => '',
                'comm_thirdparty_consent_2' => '',
                'comm_other_consent_2' => '',
                'mkt_post_consent_2' => '',
                'mkt_automatedcall_consent_2' => '',
                'mkt_web_consent_2' => '',
                'mkt_email_consent_2' => '',
                'mkt_phone_consent_2' => '',
                'mkt_sms_consent_2' => '',
                'mkt_face_consent_2' => '',
                'mkt_thirdparty_consent_2' => '',
                'mkt_other_consent_2' => ''
            ]);
            $validatedData['id'] = $record_id;

            $respond = TermsConsent::respond($validatedData);

            if (!empty($respond)) {

                Session::flash('alert-success', 'Task was successful!');

                $record = TermsConsent::findOrFail($respond['consent_id']);

                //send emails
                //Email Data
                $fields = [
                    'client' => Client::findOrFail($record->client->id),
                    'adviser' => User::findOrFail($record->user->id),
                    'record' => $record,
                    'linked' => Client::linked($record->client->id, $record->client->link)
                ];

                //Client
                $client['fields'] = $fields;
                $client['view'] = 'templated.'.$record->user->account->viewset.'.terms.email.client_response';
                $client['subject'] = "Thank you for completing your Business Terms Consent Request from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
                $client['to'] = $record->client->email;
                $client['from'] = $record->user->email;
                $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
                $client['replyTo'] = $record->user->email;
                $client['attachments'] = array(
                    array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.business', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_terms.pdf'),
                    array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.promise_completed', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_promise_completed.pdf'),
                    array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.privacy', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_privacy.pdf')
                );
                dispatch(new QueueEmail($client))->onQueue('clientemails');

                //Adviser
                $adviser['fields'] = $fields;
                $adviser['view'] = 'templated.'.$record->user->account->viewset.'.terms.email.adviser_response';
                $adviser['to'] = $record->user->email;
                $adviser['subject'] = 'Response from '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.') to '.$record->consent_type.' Business Terms Consent Request';
                $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
                $adviser['replyTo'] = $record->client->email;
                $adviser['attachments'] = array(
                    array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.promise_completed', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_promise_completed.pdf')
                );
                dispatch(new QueueEmail($adviser))->onQueue('adviseremails');

                return view('templated.'.$record->user->account->viewset.'.terms.thanks')->with('id', $record->id)->with('uid', $record->client->uid);

            } else {
                Session::flash('alert-danger', 'Task was unsuccessful!');
                return view('generic.sorry');
            }
        } else {
            //dd(request());
            $record = TermsConsent::findOrFail($record_id);
            $linked = Client::linked($record->client->id, $record->client->link);
            session([
                'logo_frontend' => $record->user->account->logo_frontend,
                'css' => $record->user->account->css,
                'viewset' => $record->user->account->viewset
            ]);
            if($record->client->uid == $uid) {
                return view('templated.'.$record->user->account->viewset.'.terms.respond', compact('record', 'linked'));
            }else{
                abort(403);
            }
        }
    }

    /**
     * Adviser Resends email to client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TermsConsent  $termsConsent
     * @return \Illuminate\Http\Response
     */
    public function resendClient($record_id)
    {
        if (request()->ajax()) {
            $record = TermsConsent::findOrFail($record_id);
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
                $client['view'] = 'templated.'.$record->user->account->viewset.'.terms.email.client_send';
                $client['subject'] = "Business Terms Consent Request from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
                $client['to'] = $record->client->email;
                $client['from'] = $record->user->email;
                $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
                $client['replyTo'] = $record->user->email;
                $client['attachments'] = array(
                    array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.business', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_terms.pdf'),
                    array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.promise', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_promise.pdf'),
                    array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.privacy', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_privacy.pdf')
                );
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
     * @param  \App\TermsConsent  $termsConsent
     * @return \Illuminate\Http\Response
     */
    public function resendAdviser($record_id)
    {
        if (request()->ajax()) {
            $record = TermsConsent::findOrFail($record_id);
            $this->authorize('anything', $record);

            if ($record) {

                //send emails
                //Email Data
                $fields = [
                    'client' => Client::findOrFail($record->client->id),
                    'adviser' => User::findOrFail($record->user->id),
                    'record' => $record,
                    'linked' => Client::linked($record->client->id, $record->client->link)
                ];

                //Client
                $adviser['fields'] = $fields;
                $adviser['view'] = 'templated.'.$record->user->account->viewset.'.terms.email.adviser_response';
                $adviser['to'] = $record->user->email;
                $adviser['subject'] = 'Response from '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.') to '.$record->consent_type.' Business Terms Consent Request';
                $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
                $adviser['replyTo'] = $record->client->email;
                $adviser['attachments'] = array(
                    array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.terms.pdf.promise_completed', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_promise_completed.pdf')
                );
                dispatch(new QueueEmail($adviser))->onQueue('adviseremails');

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

    /**
     * Download PDF
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadPDF($code,Request $request) {

        $record = TermsConsent::findOrFail($request->post('record_id'));

        switch ($code) {
            case 'terms':
                return self::pdf($request->post('uid'), $request->post('record_id'), 'download', 'templated.'.$record->user->account->viewset.'.terms.pdf.business', $request->post('uid').'/'.$request->post('uid').'_'.$request->post('record_id').'_terms.pdf');
                break;

            case 'promise':
                return self::pdf($request->post('uid'), $request->post('record_id'), 'download', 'templated.'.$record->user->account->viewset.'.terms.pdf.promise_completed', $request->post('uid').'/'.$request->post('uid').'_'.$request->post('record_id').'_promise_completed.pdf');
                break;

            case 'promise_default':
                return self::pdf($request->post('uid'), $request->post('record_id'), 'download', 'templated.'.$record->user->account->viewset.'.terms.pdf.promise', $request->post('uid').'/'.$request->post('uid').'_'.$request->post('record_id').'_promise.pdf');
                break;

            case 'privacy':
                return self::pdf($request->post('uid'), $request->post('record_id'), 'download', 'templated.'.$record->user->account->viewset.'.terms.pdf.privacy', $request->post('uid').'/'.$request->post('uid').'_'.$request->post('record_id').'_privacy.pdf');
                break;
            
            default:
                return 'forbidden';
                break;
        }
        
    }

    /**
     * Make PDF File
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TermsConsent  $termsConsent
     * @return \Illuminate\Http\Response
     */
    private static function pdf($uid, $record_id, $mode, $view, $filename)
    {

        $record = TermsConsent::findOrFail($record_id);

        if ($record && $record->client->uid == $uid) {

            //View Data
            $fields = [
                'client' => Client::findOrFail($record->client->id),
                'adviser' => User::findOrFail($record->user->id),
                'record' => $record,
                'linked' => Client::linked($record->client->id, $record->client->link)
            ];

            //check if files exists
            if (
                Storage::disk('documents')->exists($filename)
                && (intval(Storage::disk('documents')->lastModified($filename)) >= intval(strtotime($record->updated_at)))
            ) {
                $file = 'saved';
            } else {
                Storage::disk('documents')->move($filename, $filename.'_'.time().'.old');
                $file = PdfController::generatePDF($fields, $view, $filename, 'save');
            }

            //return
            if ($mode == 'download' && $file == 'saved') {
                return Storage::disk('documents')->download($filename);
            } else {
                return $file;
            }

        } else {

            return false;

        }
    }

}
