@extends('layouts.app')

@section('title') {{ __('Add New Quote') }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('quote') }}
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <h3>There's a probelm with your submission:</h3>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('quote.store') }}" id="quoteForm">
        @csrf
        <input id="options_count" name="options_count" type="hidden" value="1" />
        <input name="user_id" type="hidden" value="{{ Session::get('user_id', Auth::user()->id) }}" />
        <input name="link" type="hidden" value="{{$link}}" />

        @if(!Session::has('impersonate'))
            @include('admin.partials.adviser')
        @else
            @include('admin.partials.adviser_impersonating')
        @endif
        
        @include('admin.partials.client_select', ['clients' => $clients])

        @php
            $quote = new stdClass();
            $quote->purchase_val = 500000;
            $quote->loan_amnt = 150000;
            $quote->loan_interest = 0;
            $quote->term_yrs = "";
            $quote->term_mnth = "";
            $quote->fee = 0;
            $quote->fee_2 = 0;
            $quote->message = "";
            $quote->email_intro = "";
        @endphp
        @include('admin.quote._partials.info',['quote' => $quote])

        <div class="card mb-3">
            <div class="card-header">
                {{ __('Options') }}
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-success add-option">Add Option</button>
                </div>
            </div>
            <div class="card-body">

                <div class="row" id="product_container">

                    @if(old('_token') === null)
                        @php
                            $option = new stdClass();
                            $option->provider = "";
                            $option->product = "";
                            $option->end_date = "";
                            $option->monthly_payment = "";
                            $option->initial_rate = "";
                            $option->lender_prod_fee = "";
                            $option->lender_val_fee = "";
                            $option->lender_exit_fee = "";
                            $option->other_fees = "";
                            $option->incl_std_legal_fees = "";
                            $option->other_lender_incentives = "";
                            $option->tmbl_fee = "";
                            $option->details = "";
                            $option->initial_period = "";
                            $option->svr_period = "";
                            $option->svr = "";
                            $option->svr_monthly = "";
                            $option->total = "";
                            $option->aprc = "";

                            $loop = new stdClass();
                            $loop->index = 0;
                            $loop->iteration = 1;
                        @endphp
                        @include('admin.quote._partials.product',['option' => $option, 'loop' => $loop])
                    @else
                        @php $options = old('options'); @endphp
                        @foreach($options as $optionArr)
                            @php $option = (object) $optionArr; @endphp
                            @include('admin.quote._partials.product',['option' => $option])
                        @endforeach
                    @endif

                </div>

            </div>
            <div class="card-footer">
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-success add-option">Add Option</button>
                </div>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    {{ __('Send Quote') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>

        var optCount = 1;
        var currCount = 1;
        
        @include('admin.quote._partials.quotejs')

    </script>
@endpush
