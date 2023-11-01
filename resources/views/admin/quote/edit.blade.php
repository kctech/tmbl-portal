@extends('layouts.app')

@section('title') {{ __('Edit Quote') }} @endsection

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

    @if(old('_token') === null)
        @php $options = json_decode($quote['options']); @endphp
    @else
        @php $options = old('options'); @endphp
    @endif

    <form id="quoteForm" method="POST" action="{{ route('quote.update',$quote->id) }}">
        @csrf
        @method('PATCH')
        <input name="user_id" type="hidden" value="{{ $quote->user_id }}" />
        <input name="client_id" type="hidden" value="{{ $quote->client_id }}" />

        @if(!Session::has('impersonate'))
            @include('admin.partials.adviser')
        @else
            @include('admin.partials.adviser_impersonating')
        @endif
        
        @include('admin.partials.client_edit',['client' => $quote->client])

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

                    @foreach(json_decode($quote['options']) as $option)
                        @include('admin.quote._partials.product',['option' => $option])
                    @endforeach

                </div>

            </div>
            <div class="card-footer">
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-success add-option">Add Option</button>
                </div>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4 d-flex">
                <button type="submit" class="btn btn-primary">
                    {{ __('Edit Quote') }}
                </button>
                <button type="button" class="btn btn-danger ml-auto" onclick="app.alerts.confirmDelete('delete','Quote ID {{$quote->id}}')">
                    {{ __('Delete Quote') }}
                </button>
            </div>
        </div>
    </form>
    <form id="delete" method="POST" action="{{ route('quote.destroy',$quote->id) }}">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('js')
    <script>

        var optCount = {{count($options)}};
        var currCount = {{count($options)}};

        @include('admin.quote._partials.quotejs')

    </script>
@endpush
