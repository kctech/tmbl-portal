<div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div id="topbar_title"><h1 class="h2">Lead Chasers</h1></div>

    <div class="btn-toolbar mb-2 mb-md-0">
        {{ Breadcrumbs::render('leads') }}
        <button wire:click="create()" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-plus"></i> Add Chaser </button>
    </div>
</div>

<div class="flash-message py-2">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
            <div class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        @endif
    @endforeach
</div> <!-- end .flash-message -->


@if($view == 'form')

    @if($errors->count()>0)
        <div class="alert alert-danger">
            <i class="fa fa-info-circle"></i>
            Some problems occurred saving your form. Please address the issues below:
            {{--dump($questions[0])--}}
            @foreach($errors->getMessages() as $q => $err)
                <br /><span id="question_text_{{$q}}">{{$q}}</span> - {{ preg_replace("/(The c \d+ \d+)/i","This", $err[0]) }}
                <script>
                    if(typeof document.getElementById('{$q}}') != 'undefined'){
                        document.getElementById('question_text_{{$q}}').innerHTML = (document.getElementById("{{$q}}").textContent ?? '{{$q}}');
                    }else{
                        document.getElementById('question_text_{{$q}}').innerHTML = 'required field \'{{$q}}\' is missing';
                    }
                </script>
                @if($loop->iteration >= 3 && $errors->count() > 3)
                    <br /> And others, see form below.
                    @break
                @endif
            @endforeach
        </div>
    @endif

    <div class="form-group row">
        <label for="method" class="col-md-4 col-form-label text-md-right">{{ __('Method') }}</label>
        <div class="col-md-6">
            <div class="form-control{{ $errors->has('method') ? ' is-invalid' : '' }}" style="height:auto;">
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="chaser_method_email" wire:model="method" class="custom-control-input" value="email" />
                    <label class="custom-control-label" for="chaser_method_email">Email</label>
                </div>
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="chaser_method_sms" wire:model="method" class="custom-control-input" value="sms" disabled />
                    <label class="custom-control-label" for="chaser_method_sms">SMS (coming soon)</label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" id="chaser_method_whatsapp" wire:model="method" class="custom-control-input" value="whatsapp" disabled />
                    <label class="custom-control-label" for="chaser_method_whatsapp">WhatsApp (coming soon)</label>
                </div>
            </div>
            @if ($errors->has('method'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('method') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
        <div class="col-md-6">
            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" wire:model.defer="name">
            @if ($errors->has('name'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="chase_order" class="col-md-4 col-form-label text-md-right">{{ __('Order') }}</label>
        <div class="col-md-6">
            <input type="number" class="form-control{{ $errors->has('chase_order') ? ' is-invalid' : '' }}" wire:model.defer="chase_order">
            @if ($errors->has('chase_order'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('chase_order') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="auto_progress" class="col-md-4 col-form-label text-md-right">{{ __('Automatic Progession?') }}</label>
        <div class="col-md-6">
            <div class="form-control{{ $errors->has('auto_progress') ? ' is-invalid' : '' }}" style="height:auto;">
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="chaser_auto_progress_{{\App\Models\LeadChaser::ACTIVE}}" wire:model="auto_progress" class="custom-control-input" value="{{\App\Models\LeadChaser::ACTIVE}}" />
                    <label class="custom-control-label" for="chaser_auto_progress_{{\App\Models\LeadChaser::ACTIVE}}">Automatic</label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" id="chaser_auto_progress_{{\App\Models\LeadChaser::INACTIVE}}" wire:model="auto_progress" class="custom-control-input" value="{{\App\Models\LeadChaser::INACTIVE}}" />
                    <label class="custom-control-label" for="chaser_auto_progress_{{\App\Models\LeadChaser::INACTIVE}}">Manual</label>
                </div>
            </div>
            @if ($errors->has('auto_progress'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('auto_progress') }}</strong>
                </span>
            @endif
            <small id="auto_progressHelpBlock" class="form-text text-muted">
                {{ __('Once communication is sent, automatically move to the next step in chase progress.') }}
            </small>
        </div>
    </div>
    <div class="form-group row">
        <label for="auto_contact" class="col-md-4 col-form-label text-md-right">{{ __('Auto Contact?') }}</label>
        <div class="col-md-6">
            <div class="form-control{{ $errors->has('auto_contact') ? ' is-invalid' : '' }}" style="height:auto;">
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="chaser_auto_contact_{{\App\Models\LeadChaser::ACTIVE}}" wire:model="auto_contact" class="custom-control-input" value="{{\App\Models\LeadChaser::ACTIVE}}" />
                    <label class="custom-control-label" for="chaser_auto_contact_{{\App\Models\LeadChaser::ACTIVE}}">Automatic</label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" id="chaser_auto_contact_{{\App\Models\LeadChaser::INACTIVE}}" wire:model="auto_contact" class="custom-control-input" value="{{\App\Models\LeadChaser::INACTIVE}}" />
                    <label class="custom-control-label" for="chaser_auto_contact_{{\App\Models\LeadChaser::INACTIVE}}">Manual</label>
                </div>
            </div>
            @if ($errors->has('auto_contact'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('auto_contact') }}</strong>
                </span>
            @endif
            <small id="auto_contactsHelpBlock" class="form-text text-muted">
                {{ __('Should the associated communication (email/sms/whatsapp) be sent automatically based on timings below, or manually as they are moved.') }}
            </small>
        </div>
    </div>
    <div class="form-group row">
        <label for="time_amount" class="col-md-4 col-form-label text-md-right">{{ __('Chase Frequency') }}</label>
        <div class="col-md-3">
            <label for="time_amount" class="">{{ __('Time Amount') }}</label>
            <select wire:model.defer="time_amount" id="time_amount" class="form-control{{ $errors->has('time_amount') ? ' is-invalid' : '' }}">
                @for($amount=0; $amount<=60; $amount++)
                    <option value="{{$amount}}" {{selected($amount, $time_amount)}}>{{$amount}}</option>
                @endfor
            </select>
            @if ($errors->has('time_amount'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('time_amount') }}</strong>
                </span>
            @endif
        </div>
        <div class="col-md-3">
            <label for="time_unit" class="">{{ __('Time Unit') }}</label>
            <select wire:model.defer="time_unit" id="time_unit" class="form-control{{ $errors->has('time_amount') ? ' is-invalid' : '' }}">
                <option value="minutes" {{selected('minutes', $time_unit)}} {{selected('minutes', $time_unit)}}>Minutes</option>
                <option value="hours" {{selected('hours', $time_unit)}}>Hours</option>
                <option value="days" {{selected('days', $time_unit)}}>Days</option>
                <option value="weeks" {{selected('weeks', $time_unit)}}>Weeks</option>
            </select>
            @if ($errors->has('time_unit'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('time_unit') }}</strong>
                </span>
            @endif
        </div>
        @if ($errors->has('chase_duration'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('chase_duration') }}</strong>
            </span>
        @endif
    </div>
    <div class="form-group row">
        <label for="subject" class="col-md-4 col-form-label text-md-right">{{ __('Subject') }}</label>
        <div class="col-md-6">
            <input type="text" class="form-control{{ $errors->has('subject') ? ' is-invalid' : '' }}" wire:model.defer="subject">
            @if ($errors->has('subject'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('subject') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="body" class="col-md-4 col-form-label text-md-right">{{ __('Body Content') }}</label>
        <div class="col-md-6">
            <textarea class="form-control{{ $errors->has('body') ? ' is-invalid' : '' }}" wire:model.defer="body" rows="10"></textarea>
            @if ($errors->has('body'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('body') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
        <div class="col-md-6">
            <div class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}" style="height:auto;">
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="chaser_status_{{\App\Models\LeadChaser::ACTIVE}}" wire:model="status" class="custom-control-input" value="{{\App\Models\LeadChaser::ACTIVE}}" />
                    <label class="custom-control-label" for="chaser_status_{{\App\Models\LeadChaser::ACTIVE}}">Active</label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" id="chaser_status_{{\App\Models\LeadChaser::INACTIVE}}" wire:model="status" class="custom-control-input" value="{{\App\Models\LeadChaser::INACTIVE}}" />
                    <label class="custom-control-label" for="chaser_status_{{\App\Models\LeadChaser::INACTIVE}}">Inactive</label>
                </div>
            </div>
            @if ($errors->has('status'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('status') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button wire:click="save()" type="button" class="btn btn-primary">
                {{ __('Save') }}
            </button>
            <button type="button" class="btn btn-danger ml-auto" wire:click="$set('view','list')">
                {{ __('Cancel') }}
            </button>
        </div>
    </div>

@elseif($view == 'list')

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-self-center">
                <div class="card">
                    <div class="card-body bg-light d-flex align-items-center">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input wire:click="$set('chaser_status','')" type="radio" id="chaser_status_all" class="custom-control-input" value="" {{checked($chaser_status,'')}}>
                            <label class="custom-control-label" for="chaser_status_all">All Chasers</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input wire:click="$set('chaser_status','{{\App\Models\LeadChaser::ACTIVE}}')" type="radio" id="chaser_status_no" class="custom-control-input" value="{{\App\Models\LeadChaser::ACTIVE}}" {{checked($chaser_status,\App\Models\LeadChaser::ACTIVE)}}>
                            <label class="custom-control-label" for="chaser_status_no">Active Chasers Only</label>
                        </div>
                    </div>
                </div>
                <div class="card ml-3">
                    <div class="card-body bg-light">
                        <input class="form-control" placeholder="Chaser Search" wire:model="search_filter" value="{{$search_filter}}" />
                    </div>
                </div>
                <div class="card ml-3">
                    <div class="card-body bg-light">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="far fa-sort-alpha-down"></i></div>
                            </div>
                            <select wire:model="sort_order" id="sort_order"  class="form-control">
                                <option value="recent" {{selected('recent', $sort_order)}}>Recently Updated</option>
                                <option value="newest_first" {{selected('newest_first', $sort_order)}} {{selected('default', $sort_order)}}>Newset First</option>
                                <option value="oldest_first" {{selected('oldest_first', $sort_order)}}>Oldest First</option>
                                <option value="surname_az" {{selected('surname_az', $sort_order)}}>Surname A-Z</option>
                                <option value="surname_za" {{selected('surname_za', $sort_order)}}>Surname Z-A</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="ml-auto d-flex align-items-center justify-content-center">
                    <button wire:click="resetFilters" class="btn btn-outline-dark btn-lg">
                        <i class="fas fa-times fa-sm"></i> Clear
                    </button>
                </div>
            </div>

        </div>

    </div>

    <div wire:loading.remove>

        <div class="card mb-4">

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-sm">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>Strategy</th>
                            <th>Order</th>
                            <th>Name</th>
                            <th>Method</th>
                            <th>Chase Duration</th>
                            <th>Last Updated</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @if($list->count()==0)
                            <tr class="">
                                <td colspan=11>{{ __('Zero results.') }}</td>
                            </tr>
                        @endif
                        @foreach($list as $item)

                            <tr class="">
                                <td>{{ $item->strategy->name }}</td>
                                <td>{{ $item->chase_order }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    @if($item->auto_contact == 0)
                                        {{ __('Automatic') }}
                                    @else
                                        {{ __('Manual') }}
                                    @endif
                                    {{ $item->method }}
                                    @if($item->auto_progress == 0)
                                        {{ __('then progress') }}
                                    @endif
                                </td>
                                <td>{{ $item->chase_duration }}</td>
                                <td>
                                    {{\Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i')}}
                                    <span class="badge badge-primary">{{\Carbon\Carbon::parse($item->updated_at)->diffForHumans()}}</span>
                                </td>
                                <td>
                                    @if($item->status == \App\Models\LeadChaser::ACTIVE)
                                        Active
                                    @elseif($item->status == \App\Models\LeadChaser::INACTIVE)
                                        Inactive
                                    @else
                                        Unknown
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button class="btn @if($item->status == \App\Models\LeadChaser::ACTIVE) btn-success @else btn-danger @endif px-2 btn-sm dropdown-toggle" type="button" id="dropdownMenuButtonCog{{$item->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-boundary="viewport">
                                            <i class="fas fa-cog text-light"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButtonCog{{$item->id}}" id="dropdownMenuContents{{$item->id}}">
                                            <span class="dropdown-item" wire:click="edit({{$item->id}})">
                                                <i class="fa fa-fw fa-edit"></i> Edit
                                            </span>
                                            <span class="dropdown-item" wire:click="copy({{$item->id}})">
                                                <i class="fa fa-fw fa-copy"></i> Copy
                                            </span>
                                            <div class="dropdown-divider"></div>
                                            @if($item->status == \App\Models\LeadChaser::ACTIVE)
                                                <span class="dropdown-item text-danger" onclick="confirm('Are you sure you want to remove the API Key??') || event.stopImmediatePropagation()" wire:click="delete({{$item->id}})">
                                                    <i class="fa fa-fw fa-times"></i> Delete
                                                </span>
                                            @else
                                                <span class="dropdown-item text-success" onclick="confirm('Are you sure you want to restore the API Key?') || event.stopImmediatePropagation()" wire:click="restore({{$item->id}})">
                                                    <i class="fa fa-fw fa-check"></i> Restore
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $list->links() }}
            </div>
        </div>

    </div>

    <div class="w-100" wire:loading>
        <div class="card mt-4 border-0 shadow">
            <div class="card-body p-1">
                <div class="p-3 d-flex align-items-center justify-content-center text-center">
                    {{ __('Loading...') }}
                </div>
            </div>
        </div>
    </div>
@endif

<div id="topbar_title_content" class="d-none">
    <h1 class="h2">Lead Chasers</h1>
</div>

</div>

@push('js')
    <script>

        Livewire.on('updated', data => {
            if(typeof data.message != 'undefined'){
                app.alerts.toast(data.message);
            }
        });

        Livewire.on('error', data => {
            console.log("message");
            if(typeof data.message != 'undefined'){
                app.alerts.toast(data.message,'error');
            }else{
                app.alerts.toast('An error occurred','error');
            }
        });

        document.addEventListener("DOMContentLoaded", () => {
            Livewire.hook('message.processed', (message, component) => {
                $("#topbar_title").html($("#topbar_title_content").html());
            });

            $("#topbar_title").html($("#topbar_title_content").html());
        });

    </script>
@endpush
