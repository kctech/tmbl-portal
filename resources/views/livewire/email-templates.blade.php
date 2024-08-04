<div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div id="topbar_title"><h1 class="h2">Email Templates</h1></div>

    <div class="btn-toolbar mb-2 mb-md-0">
        {{ Breadcrumbs::render('leads') }}
        <button wire:click="create()" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-plus"></i> Add Email Template </button>
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
                    <input type="radio" id="chaser_status_{{\App\Models\EmailTemplate::ACTIVE}}" wire:model="status" class="custom-control-input" value="{{\App\Models\EmailTemplate::ACTIVE}}" />
                    <label class="custom-control-label" for="chaser_status_{{\App\Models\EmailTemplate::ACTIVE}}">Active</label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" id="chaser_status_{{\App\Models\EmailTemplate::INACTIVE}}" wire:model="status" class="custom-control-input" value="{{\App\Models\EmailTemplate::INACTIVE}}" />
                    <label class="custom-control-label" for="chaser_status_{{\App\Models\EmailTemplate::INACTIVE}}">Inactive</label>
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
                            <label class="custom-control-label" for="chaser_status_all">All Email Templates</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input wire:click="$set('chaser_status','{{\App\Models\EmailTemplate::ACTIVE}}')" type="radio" id="chaser_status_no" class="custom-control-input" value="{{\App\Models\EmailTemplate::ACTIVE}}" {{checked($chaser_status,\App\Models\EmailTemplate::ACTIVE)}}>
                            <label class="custom-control-label" for="chaser_status_no">Active Email Templates Only</label>
                        </div>
                    </div>
                </div>
                <div class="card ml-3">
                    <div class="card-body bg-light">
                        <input class="form-control" placeholder="Email Template Search" wire:model="search_filter" value="{{$search_filter}}" />
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
                                <option value="newest_first" {{selected('newest_first', $sort_order)}} {{selected('default', $sort_order)}}>Newest First</option>
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
                            <th>Name</th>
                            <th>Subject</th>
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
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->subject }}</td>
                                <td>
                                    {{\Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i')}}
                                    <span class="badge badge-primary">{{\Carbon\Carbon::parse($item->updated_at)->diffForHumans()}}</span>
                                </td>
                                <td>
                                    @if($item->status == \App\Models\EmailTemplate::ACTIVE)
                                        Active
                                    @elseif($item->status == \App\Models\EmailTemplate::INACTIVE)
                                        Inactive
                                    @else
                                        Unknown
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button class="btn @if($item->status == \App\Models\EmailTemplate::ACTIVE) btn-success @else btn-danger @endif px-2 btn-sm dropdown-toggle" type="button" id="dropdownMenuButtonCog{{$item->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-boundary="viewport">
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
                                            @if($item->status == \App\Models\EmailTemplate::ACTIVE)
                                                <span class="dropdown-item text-danger" onclick="confirm('Are you sure you want to remove the email template?') || event.stopImmediatePropagation()" wire:click="delete({{$item->id}})">
                                                    <i class="fa fa-fw fa-times"></i> Delete
                                                </span>
                                            @else
                                                <span class="dropdown-item text-success" onclick="confirm('Are you sure you want to restore the email template?') || event.stopImmediatePropagation()" wire:click="restore({{$item->id}})">
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
    <h1 class="h2">Email Templates</h1>
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