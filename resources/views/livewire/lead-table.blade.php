<div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

    <div id="topbar_title"><h1 class="h2">Leads</h1></div>

    <div class="btn-toolbar mb-2 mb-md-0">
        {{ Breadcrumbs::render('leads') }}
        <a href="{{ route('leads.adviser-availability') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-users"></i> Adviser Availability</a>
        <a href="{{ route('leads.sources') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-inbox-in"></i> Lead Sources</a>
    </div>
</div>

<div class="flash-message py-2">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
            <div class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        @endif
    @endforeach
</div> <!-- end .flash-message -->

<div class="card mb-4">
    <div class="card-body">
        <h3>Filters</h3>

        <div class="d-flex align-self-center">
            <div class="card">
                <div class="card-body bg-light d-flex align-items-center">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input wire:click="$set('lead_status','')" type="radio" id="lead_status_all" name="lead_status" class="custom-control-input" value="" {{checked('', $lead_status)}}>
                        <label class="custom-control-label" for="lead_status_all">All Leads</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input wire:click="$set('lead_status','{{\App\Models\Lead::PROSPECT}}')" type="radio" id="lead_status_no" name="lead_status" class="custom-control-input" value="{{\App\Models\Lead::PROSPECT}}" {{checked(\App\Models\Lead::PROSPECT, $lead_status)}}>
                        <label class="custom-control-label" for="lead_status_no">New Leads Only</label>
                    </div>
                </div>
            </div>
            <div class="card ml-3">
                <div class="card-body bg-light">
                    <input class="form-control" placeholder="Lead Search" wire:model="search_filter" value="{{$search_filter}}" />
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

        @if(!is_null($lead_id))
            <div class="card">
                <div class="card-header d-flex flex-row justify-content-center align-items-center">
                    <h2 class="mb-0 mr-auto">{{$lead->first_name}} {{$lead->last_name}}</h2>
                    @if($lead->status == \App\Models\Lead::PROSPECT)<button class="btn btn-primary" wire:click="assign({{$lead_id}})">Send to MAB Distribution Group</button>@endif
                    <button class="ml-3 btn btn-secondary" wire:click="close()">Close</button>
                </div>
                <div class="card-body p-1">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                            @foreach(json_decode($lead->data) as $d_key => $d_val)
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>{{$d_key}}</strong>
                                        </div>
                                        <div class="col-md-8">
                                            {{$d_val}}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">

                            <div class="w-100 h-100 position-relative">
                                <div class="list_{{$lead_id}} position-absolute overflow-auto" style="top:0; bottom:0; left:0; right:0;">
                                    <div class="list-group list-group-flush">
                                        @if($lead->status == \App\Models\Lead::PROSPECT)
                                            @foreach($advisers as $adviser)
                                                <div class="list-group-item p-1">
                                                    <div class="row">
                                                        <div class="col-1 text-right">
                                                            <span class="tip" title="{{$adviser->presence->activity ?? 'unknown'}}">
                                                                @switch(($adviser->presence->availability ?? 'unknown'))
                                                                    @case('Available')
                                                                        <i class="fas fa-check-circle text-success"></i>
                                                                        @break
                                                                    @case('Busy')
                                                                    @case('Presenting')
                                                                    @case('DoNotDisturb')
                                                                        <i class="fas fa-circle text-danger"></i>
                                                                        @break
                                                                    @case('Away')
                                                                        <i class="fas fa-circle text-warning"></i>
                                                                        @break
                                                                    @case('Offline')
                                                                        <i class="fas fa-times-circle text-muted"></i>
                                                                        @break
                                                                    @default
                                                                        <i class="fas fa-question-circle text-muted"></i>
                                                                @endswitch
                                                            </span>
                                                        </div>
                                                        <div class="col-5 text-truncate">
                                                            {{$adviser->first_name}} {{$adviser->last_name}}
                                                        </div>
                                                        <div class="col-2 text-right">
                                                            {{$adviser->leads_this_month->count()}} this mo.
                                                        </div>
                                                        <div class="col-4 text-right">
                                                            @if($lead->status == \App\Models\Lead::PROSPECT)
                                                                <button class="btn btn-sm btn-secondary btn-blockX" wire:click="allocate({{$lead_id}},'{{$adviser->id}}')">Allocate</button>
                                                                <button class="ml-2 btn btn-sm btn-primary btn-blockX" wire:click="transfer({{$lead_id}},'{{$adviser->email}}')">Transfer</button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @elseif($lead->status == \App\Models\Lead::CLAIMED)
                                            Claimed by {{$lead->owner->full_name()}}
                                            <button class="ml-2 btn btn-sm btn-primary btn-blockX" wire:click="transfer({{$lead_id}},'{{$lead->owner->email}}')">Transfer to MAB</button>
                                            <br />
                                            <button class="btn btn-sm btn-danger btn-blockX" wire:click="deallocate({{$lead_id}})">Remove {{$lead->owner->full_name()}} from lead</button>
                                        @elseif($lead->status == \App\Models\Lead::TRANSFERRED)
                                            {{ \App\Libraries\Interpret::LeadStatus($lead->status) }} to {{$lead->owner->full_name()}}
                                        @else
                                            {{ \App\Libraries\Interpret::LeadStatus($lead->status) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="overflow-msg overflow-msg-scroll d-none position-absolute text-muted text-center" style="bottom:0; left:0; right:0; background-image: linear-gradient(180deg, rgba(255,255,255,0), rgba(255,255,255,1));">
                                    <small>scroll to see more</small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <script>
                $('.list_{{$lead_id}}').each(function(index, value) {
                    if(this.offsetHeight < this.scrollHeight){
                        $(this).parent().find('.overflow-msg').each(function(index, value) {
                            $(this).removeClass('d-none');
                        });
                    }
                });
            </script>

        @else

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-sm mb-0">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email Address</th>
                            <th>Contact Number</th>
                            <th>Recieved</th>
                            <th>Source</th>
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
                                <td>{{ $item->first_name }}</td>
                                <td>{{ $item->last_name }}</td>
                                <td>{{ $item->email_address }}</td>
                                <td>{{ $item->contact_number }}</td>
                                <td>
                                    {{\Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')}}
                                    <span class="badge badge-primary">{{\Carbon\Carbon::parse($item->created_at)->diffForHumans()}}</span>
                                </td>
                                <td>{{ $item->source->source ?? 'Unknown' }}</td>
                                <td>
                                    {{ \App\Libraries\Interpret::LeadStatus($item->status) }}
                                    @if(is_numeric($item->user_id))
                                        <br /><span class="badge badge-primary">{{$item->owner->full_name() ?? 'Unknown User'}}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($item->status == \App\Models\Lead::PROSPECT)
                                        <button class="btn btn-primary" wire:click="info({{$item->id}})">Actions</button>
                                    @else
                                        <button class="btn btn-secondary" wire:click="info({{$item->id}})">Info</button>
                                    @endif
                                </td>
                            </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $list->links() }}
            </div>

        @endif

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

<div id="topbar_title_content" class="d-none">
    <h1 class="h2">Leads</h1>
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
