<div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div id="topbar_title"><h1 class="h2">Lead Sources</h1></div>

    <div class="btn-toolbar mb-2 mb-md-0">
        {{ Breadcrumbs::render('leads') }}
        <button wire:click="create()" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-plus"></i> Add Source </button>
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

    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Source Name') }}</label>
        <div class="col-md-6">
            <input type="text" class="form-control{{ $errors->has('source') ? ' is-invalid' : '' }}" wire:model.defer="source">
            @if ($errors->has('source'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('source') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('API Token') }}</label>
        <div class="col-md-6">
            <input type="text" class="form-control{{ $errors->has('api_token') ? ' is-invalid' : '' }}" wire:model.defer="api_token">
            @if ($errors->has('api_token'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('api_token') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
        <div class="col-md-6">
            <div class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}" style="height:auto;">
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="stage_event_{{\App\Models\ApiKey::ACTIVE}}" wire:model="status" class="custom-control-input" value="{{\App\Models\ApiKey::ACTIVE}}" />
                    <label class="custom-control-label" for="stage_event_{{\App\Models\ApiKey::ACTIVE}}">Active</label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" id="stage_event_{{\App\Models\ApiKey::INACTIVE}}" wire:model="status" class="custom-control-input" value="{{\App\Models\ApiKey::INACTIVE}}" />
                    <label class="custom-control-label" for="stage_event_{{\App\Models\ApiKey::INACTIVE}}">Inactive</label>
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
                            <input wire:click="$set('source_status','')" type="radio" id="source_status_all" class="custom-control-input" value="" {{checked($source_status,'')}}>
                            <label class="custom-control-label" for="source_status_all">All Sources</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input wire:click="$set('source_status','{{\App\Models\ApiKey::ACTIVE}}')" type="radio" id="source_status_no" class="custom-control-input" value="{{\App\Models\ApiKey::ACTIVE}}" {{checked($source_status,\App\Models\ApiKey::ACTIVE)}}>
                            <label class="custom-control-label" for="source_status_no">Active Sources Only</label>
                        </div>
                    </div>
                </div>
                <div class="card ml-3">
                    <div class="card-body bg-light">
                        <input class="form-control" placeholder="Source Search" wire:model="search_filter" value="{{$search_filter}}" />
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

    <div class="card mb-4 bg-light p-3">
        <div class="row">
            <div class="col-6">
                <strong>Guide:</strong><br />
                <hr />
                Send a POST request to endpoint: <strong class="cursor-pointer tip" title="Copy URL to clipboard" onclick="app.copyToClipboard('{{env('APP_URL')}}/api/leads/new')">{{env('APP_URL')}}/api/leads/new <i class="fal fa-copy"></i></strong>
                <br />
                Authentication is one of:
                <ul>
                    <li>Form input: 'api_token' with value of {token}</li>
                    <li>Querystring variable: ?api_token={token}</li>
                    <li>Header: 'x-api-token' with value of {token}</li>
                </ul>
            </div>
            <div class="col-6">
                <strong>Limits:</strong><br />
                <hr />
                100 requests within 1 minute.
            </div>
        </div>
    </div>

    <div wire:loading.remove>

        <div class="card mb-4">

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-sm">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>ID</th>
                            <th>Source</th>
                            <th>API Key</th>
                            <th>Last Use</th>
                            <th>Created</th>
                            <th>Leads</th>
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
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->source }}</td>
                                <td>
                                    <div onclick="app.copyToClipboard('{{env('APP_URL')}}/api/leads/new?api_token={{ $item->api_token }}')" class="w-100 d-flex align-items-center justify-content-between cursor-pointer tip" title="Copy full token URL to clipboard">
                                        <span>{{ $item->api_token }}</span>
                                        <i class="ml-auto fa fa-copy"></i>
                                    </div>
                                </td>
                                <td>
                                    @if(!is_null($item->last_login_at))
                                        {{\Carbon\Carbon::parse($item->last_login_at)->format('d/m/Y H:i')}}
                                        <span class="badge badge-primary">{{\Carbon\Carbon::parse($item->last_login_at)->diffForHumans()}}</span>
                                    @else
                                        Never
                                    @endif
                                </td>
                                <td>
                                    {{\Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')}}
                                    <span class="badge badge-primary">{{\Carbon\Carbon::parse($item->created_at)->diffForHumans()}}</span>
                                </td>
                                <td>{{ $item->leads->count() }}</td>
                                <td>
                                    @if($item->status == \App\Models\ApiKey::ACTIVE)
                                        Active
                                    @elseif($item->status == \App\Models\ApiKey::INACTIVE)
                                        Inactive
                                    @else
                                        Unknown
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button class="btn @if($item->status == \App\Models\ApiKey::ACTIVE) btn-success @else btn-danger @endif px-2 btn-sm dropdown-toggle" type="button" id="dropdownMenuButtonCog{{$item->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-boundary="viewport">
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
                                            @if($item->status == \App\Models\ApiKey::ACTIVE)
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
    <h1 class="h2">Lead Sources</h1>
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
