<div class="form-group row">
    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('User') }}</label>

    <div class="col-md-6">

        <select name="user_id" id="user_id"  class="form-control select2 {{ $errors->has('user_id') ? ' is-invalid' : '' }}" required autofocus tabindex="1">
            <option value="" {!! selected('', old('user_id', $user_id)) !!}>- Select -</option>

            @foreach($users as $user)

                <option value="{{ $user->id }}" {!! selected($user->id, old('user_id', $user_id)) !!}>{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }} - {{ $user->account->acronym }})</option>

            @endforeach

        </select>

        @if ($errors->has('user_id'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('user_id') }}</strong>
            </span>
        @endif
    </div>
</div>
