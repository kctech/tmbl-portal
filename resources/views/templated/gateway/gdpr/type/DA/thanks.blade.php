@extends('layouts.frontend')

@section('title') Client Portal @endsection
@section('pagetitle') Thanks @endsection

@section('content')
<div class="container">
    <div class="row d-flex justify-content-center align-self-stretch">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <p>Thank you for completing your preferences.</p>
                    <p>The video gives you an insight into our company and a chance to learn more about who you are speaking with.</p>
                    <p>Your adviser will be back in touch shortly.</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://player.vimeo.com/video/286315473?&title=0&byline=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>
@endsection
