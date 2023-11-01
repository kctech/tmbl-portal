<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Adviser') }}</label>

    <div class="col-md-6">
        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
        <br />
        {{ Auth::user()->email }}
    </div>
</div>