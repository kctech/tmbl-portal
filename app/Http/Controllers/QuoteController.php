<?php

namespace App\Http\Controllers;

use Validator;
use Storage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Jobs\QueueEmail;

use App\Models\Quote;
use App\Models\Client;
use App\Models\User;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $accepted = $request->input('accepted');
            $client_surname = $request->input('client_surname');
            $sort = $request->input('sort');

            $requests = Quote::filter($accepted, $client_surname, $sort);

            if ($accepted == "") $accepted = 'default';
            if ($sort == "") $sort = 'recent';
        } else {
            $accepted = 'default';
            $client_surname = '';
            $sort = 'recent';

            $requests = Quote::where('user_id', Session::get('user_id',auth()->id()))->paginate(config('database.pagination_size'));
        }

        return view('admin.quote.index')->with(compact('requests'))->with('accepted', $accepted)->with('client_surname', $client_surname)->with('sort', $sort);
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
        return view('admin.quote.create',compact('clients'))->with('link',$link);
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

        //dd($request->all());

        //Fields
        $validator = Validator::make($request->all(), Quote::VALIDATION_RULES, Quote::VALIDATION_LABELS);

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

        //return if fails
        if ($validator->fails()) {
            return redirect()->route('quote.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        //reindex and encode options
        foreach ($validator->validated()['options'] as $k => $option) {
            $optionsArr[]=$option;
        }

        //put valused data into it's own variable so we can overwite options
        $validated = $validator->validated();
        $validated['options'] = json_encode($optionsArr);

        //Client 1
        $inserts[] = Quote::create($validated);

        //New Client 2
        if (!empty($validator->validated()['email_2'])) {
            $inserts[] = Quote::create(array(
                'user_id' => $validator->validated()['user_id'],
                'link' => $validator->validated()['link'],
                'options' => $validated['options'],
                'purchase_val' => $validator->validated()['purchase_val'],
                'loan_amnt' => $validator->validated()['loan_amnt'],
                'loan_interest' => $validator->validated()['loan_interest'],
                'term_yrs' => $validator->validated()['term_yrs'],
                'term_mnth' => $validator->validated()['term_mnth'],
                'fee_type' => $validator->validated()['fee_type'],
                'fee' => $validator->validated()['fee'],
                'fee_timing' => $validator->validated()['fee_timing'],
                'fee_2_type' => $validator->validated()['fee_2_type'],
                'fee_2' => $validator->validated()['fee_2'],
                'fee_2_timing' => $validator->validated()['fee_2_timing'],
                'message' => $validator->validated()['message'],
                'email_intro' => $validator->validated()['email_intro'],
                'first_name' => $validator->validated()['first_name_2'],
                'last_name' => $validator->validated()['last_name_2'],
                'email' => $validator->validated()['email_2'],
                'tel' => $validator->validated()['tel_2']
            ));
        }

        //Linked client(s)
        if (!empty($validator->validated()['linked'])) {
            foreach ($validator->validated()['linked'] as $linked_client) {
                $inserts[] = Quote::create(array(
                    'user_id' => $validator->validated()['user_id'],
                    'client_id' => $linked_client,
                    'options' => $validated['options'],
                    'purchase_val' => $validator->validated()['purchase_val'],
                    'loan_amnt' => $validator->validated()['loan_amnt'],
                    'loan_interest' => $validator->validated()['loan_interest'],
                    'term_yrs' => $validator->validated()['term_yrs'],
                    'term_mnth' => $validator->validated()['term_mnth'],
                    'fee_type' => $validator->validated()['fee_type'],
                    'fee' => $validator->validated()['fee'],
                    'fee_timing' => $validator->validated()['fee_timing'],
                    'fee_2_type' => $validator->validated()['fee_2_type'],
                    'fee_2' => $validator->validated()['fee_2'],
                    'fee_2_timing' => $validator->validated()['fee_2_timing'],
                    'message' => $validator->validated()['message'],
                    'email_intro' => $validator->validated()['email_intro'],
                ));
            }
        }

        if (!empty($inserts)) {
            $counter = 1;
            foreach ($inserts as $insert) {
                if (!empty($insert)) {
                    $statusMsg[] = 'Task was successful for Client '. $counter . '!';

                    $record = Quote::findOrFail($insert['quote_id']);

                    //send emails
                    //Email Data
                    $fields = [
                        'client' => Client::findOrFail($record->client->id),
                        'adviser' => User::findOrFail($record->user->id),
                        'record' => $record
                    ];

                    //Client
                    $client['fields'] = $fields;
                    $client['view'] = 'templated.'.$record->user->account->viewset.'.quote.email.client_send';
                    $client['subject'] = "Quote from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
                    $client['to'] = $record->client->email;
                    $client['from'] = $record->user->email;
                    $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
                    $client['replyTo'] = $record->user->email;
                    $client['attachments'] = array(
                        array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.quote.pdf.quote', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_quote.pdf')
                    );
                    dispatch(new QueueEmail($client))->onQueue('clientemails');

                    //Adviser
                    $adviser['fields'] = $fields;
                    $adviser['view'] = 'templated.'.$record->user->account->viewset.'.quote.email.adviser_send';
                    $adviser['to'] = $record->user->email;
                    $adviser['subject'] = 'Quote sent to: '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.')';
                    $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
                    $adviser['replyTo'] = $record->client->email;
                    $adviser['attachments'] = array(
                        array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.quote.pdf.quote_adviser', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_quote_adviser.pdf')
                    );
                    dispatch(new QueueEmail($adviser))->onQueue('adviseremails');

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

        return redirect()->route('quote.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function show(Quote $quote)
    {
        $this->authorize('anything', $quote);
        return view('templated.'.Session::get('viewset').'.quote.view', compact('quote'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function edit(Quote $quote)
    {
        $this->authorize('anything', $quote);
        return view('admin.quote.edit')->with('quote', $quote);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        $quote = Quote::findOrFail($id);
        $this->authorize('anything', $quote);
        $clients = Client::where('account_id', Session::get('account_id'))->get(); //->where('user_id', auth()->id())->get();  ||  ::all();
        $link = md5(microtime(true).random_bytes(6));
        return view('admin.quote.copy', compact('clients'))->with('link', $link)->with('quote', $quote);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $quote = Quote::findOrFail($id);
        $this->authorize('anything', $quote);

        $validatedData = request()->validate([
            'user_id' => 'required',
            'client_id' => 'required',
            'options_count' => '',
            'options.*.provider' => 'required',
            'options.*.product' => 'required',
            'options.*.end_date' => 'required',
            'options.*.monthly_payment' => 'required',
            'options.*.initial_rate' => 'required',
            'options.*.lender_prod_fee' => 'required',
            'options.*.lender_val_fee' => 'required',
            'options.*.lender_exit_fee' => 'required',
            'options.*.other_fees' => 'required',
            'options.*.incl_std_legal_fees' => 'required',
            'options.*.other_lender_incentives' => 'required',
            'options.*.tmbl_fee' => 'required',
            'options.*.details' => 'required',
            'options.*.initial_period' => 'required',
            'options.*.svr_period' => 'required',
            'options.*.svr' => 'required',
            'options.*.svr_monthly' => 'required',
            'options.*.total' => 'required',
            'options.*.aprc' => 'required',
            'purchase_val' => 'required',
            'loan_amnt' => 'required',
            'loan_interest' => 'required',
            'term_yrs' => 'required',
            'term_mnth' => 'required',
            'fee_type' => 'required',
            'fee' => 'required|numeric',
            'fee_timing' => 'required',
            'fee_2_type' => '',
            'fee_2' => 'sometimes|numeric',
            'fee_2_timing' => '',
            'message' => '',
            'email_intro' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'tel' => '',
            'email' => 'required|email'
        ],
        Quote::VALIDATION_LABELS);

        $validatedData['id'] = $id;
        $validatedData['accepted'] = 0;
        $validatedData['signature'] = null;

        //reindex and encode options
        foreach ($validatedData['options'] as $k => $option) {
            $optionsArr[]=$option;
        }

        //overwite options
        $validatedData['options'] = json_encode($optionsArr);

        $update = Quote::edit($validatedData);

        if (!empty($update)) {

            Session::flash('alert-success', 'Task was successful!');

            $record = Quote::findOrFail($update['quote_id']);

            //clear old files
            Storage::disk('documents')->delete($record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_quote.pdf');
            Storage::disk('documents')->delete($record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_quote_adviser.pdf');

            //send emails
            //Email Data
            $fields = [
                'client' => Client::findOrFail($record->client->id),
                'adviser' => User::findOrFail($record->user->id),
                'record' => $record
            ];

            //Client
            $client['fields'] = $fields;
            $client['view'] = 'templated.'.$record->user->account->viewset.'.quote.email.client_send';
            $client['subject'] = "Updated Quote from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
            $client['to'] = $record->client->email;
            $client['from'] = $record->user->email;
            $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
            $client['replyTo'] = $record->user->email;
            $client['attachments'] = array(
                array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.quote.pdf.quote', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_quote.pdf'),
            );
            dispatch(new QueueEmail($client))->onQueue('clientemails');

            //Adviser
            $adviser['fields'] = $fields;
            $adviser['view'] = 'templated.'.$record->user->account->viewset.'.quote.email.adviser_send';
            $adviser['to'] = $record->user->email;
            $adviser['subject'] = 'Updated Quote sent to: '.$record->client->first_name.' '.$record->client->last_name. ' ('.$record->client->email.')';
            $adviser['fromName'] = Session::get('acronym') .' Adviser Portal';
            $adviser['replyTo'] = $record->client->email;
            $adviser['attachments'] = array(
                array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.quote.pdf.quote_adviser', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_quote_adviser.pdf')
            );
            dispatch(new QueueEmail($adviser))->onQueue('adviseremails');

        } else {
            Session::flash('alert-danger', 'Task was unsuccessful!');
        }

        //dd(request());
        return redirect()->route('quote.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $quote = Quote::findOrFail($id);
        $this->authorize('anything', $quote);

        $action = Quote::remove($id);
        if ($action) {
            Session::flash('alert-success', 'Quote ID'.$id.' deleted');
        } else {
            Session::flash('alert-danger', 'Quote ID'.$id.' could not be deleted');
        }

        return redirect()->route('quote.index');
    }

    /**
     * Client Responds the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function respond($uid,$record_id)
    {
        if (request()->isMethod('post')) {
            Session::flash('alert-danger', 'Sorry, not set up for POST.');
            return view('generic.sorry');
        } else {
            //dd(request());
            $record = Quote::findOrFail($record_id);
            $linked = Client::linked($record->client->id, $record->client->link);
            if($record->client->uid == $uid) {
                return view('templated.'.$record->user->account->viewset.'.quote.respond', compact('record', 'linked'));
            }else{
                abort(403);
            }
        }
    }

    /**
     * Adviser Resends email to client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function resendClient($record_id)
    {
        if (request()->ajax()) {
            $record = Quote::findOrFail($record_id);
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
                $client['view'] = 'templated.'.$record->user->account->viewset.'.quote.email.client_send';
                $client['subject'] = "Quote from ".Session::get('acronym').": ".$record->user->first_name.' '.$record->user->last_name;
                $client['to'] = $record->client->email;
                $client['from'] = $record->user->email;
                $client['fromName'] = $record->user->first_name.' '.$record->user->last_name;
                $client['replyTo'] = $record->user->email;
                $client['attachments'] = array(
                    array('disk' => 'documents', 'view' => 'templated.'.$record->user->account->viewset.'.quote.pdf.quote', 'file' => $record->client->uid.'/'.$record->client->uid.'_'.$record->id.'_quote.pdf')
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
     * Download PDF
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadPDF($code,Request $request) {

        switch ($code) {
            case 'quote':
                $record = Quote::findOrFail($request->post('record_id'));
                return self::pdf($request->post('uid'), $request->post('record_id'), 'download', 'templated.'.$record->user->account->viewset.'.quote.pdf.quote', $request->post('uid').'/'.$request->post('uid').'_'.$request->post('record_id').'_quote.pdf');
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
     * @param  \App\Quote  $quote
     * @return \Illuminate\Http\Response
     */
    private static function pdf($uid, $record_id, $mode, $view, $filename)
    {

        $record = Quote::findOrFail($record_id);

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
