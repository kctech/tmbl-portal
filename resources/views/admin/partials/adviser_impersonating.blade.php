<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Adviser') }}</label>

    @php $impersonatedUser = getUser(Session::get('user_id')); @endphp
    <div class="col-md-6">
        {{ $impersonatedUser->first_name }} {{ $impersonatedUser->last_name }}
        <br />
        {{ $impersonatedUser->email }}
    </div>
</div>