<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Client') }}</label>

    <div class="col-md-6">
        {{ $client->first_name }} {{ $client->last_name }}
        <br />
        {{ $client->email }}
    </div>
</div>