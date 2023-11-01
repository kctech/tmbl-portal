@extends('layouts.frontend')

@section('title') Thanks @endsection
@section('pagetitle') Thanks @endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    Thanks, your adviser will be in contact with you soon.
                    <br /><br />
                    
                    <button type="button" class="btn btn-primary dl-btn"  data-action="{{ signedRoute('terms-consent.download',['code'=>'terms']) }}">Download Business Terms</button>
                    <br /><br />
                    <button type="button" class="btn btn-primary dl-btn"  data-action="{{ signedRoute('terms-consent.download',['code'=>'promise']) }}">Download Our Promise</button>
                    <br /><br />
                    <button type="button" class="btn btn-primary dl-btn" data-action="{{ signedRoute('terms-consent.download',['code'=>'privacy']) }}">Download Privacy Policy</button>
                    <br /><br />
                </div>
            </div>
        </div>
    </div>
</div>

<form target="_blank" id="download" method="POST" action="/">
    @csrf
    <input name="record_id" type="hidden" value="{{$id}}" />
    <input name="uid" type="hidden" value="{{$uid}}" />
</form>

@endsection


@push('js')
<script>
    $(document).ready(function(){
        $(".dl-btn").click(function(){
            $("form#download").attr("action",$(this).data("action"));
            $("form#download").submit();
        });
    });
</script>
@endpush
