<div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

    <div id="topbar_title"><h1 class="h2">Leads</h1></div>

    <div class="btn-toolbar mb-2 mb-md-0">
        {{ Breadcrumbs::render('leads') }}
    </div>
</div>

<div class="flash-message py-2">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
            <div class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        @endif
    @endforeach
</div> <!-- end .flash-message -->

<div wire:loading.remove>

    @foreach($stats as $section_title => $section)
        <div class="row mb-4">
            {{--<div class="col-12"><h2>{{$section_title}}</h2></div>--}}
            @foreach($section as $stat)
                @include('dashboard.'.($stat->tpl ?? 'total'), ['stat' => (object) $stat])
            @endforeach
        </div>
    @endforeach

</div>

<div class="card mb-4">
    <div class="card-body">
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
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="m-0">{{$lead->first_name}} {{$lead->last_name}}</h2>
                        </div>
                        <div class="col-6 d-flex align-items-center justify-content-end">
                            <button class="btn btn-sm btn-secondary btn-blockX" wire:click="close()">Close</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-1">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                            @foreach(json_decode($lead->data) as $d_key => $d_val)
                                @if(in_array($d_key,['full_name','first_name','last_name'])) @continue @endif
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
                            <div class="p-3">
                                @if($lead->status == \App\Models\Lead::PROSPECT)
                                    <p>If you would like to cleam this clead and reveal the contact details, click the button below. Please note once claimed you will not be able to release it back into the pool until after 7 days.</p>
                                    <button class="btn btn-sm btn-secondary btn-blockX" wire:click="allocate({{$lead->id}})">Claim</button>
                                @elseif($lead->status == \App\Models\Lead::CLAIMED || ($lead->status == \App\Models\Lead::PAUSE_CONTACTING && $lead->user_id == session('user_id')))
                                    <p>Transfer the lead into MAB</p>
                                    <button class="ml-2 btn btn-sm btn-primary btn-blockX" wire:click="transfer({{$lead->id}})">Transfer</button>
                                    @if(\Carbon\Carbon::parse($lead->allocated_at)->diff(\Carbon\Carbon::now())->days > 7)
                                        <button class="btn btn-sm btn-danger btn-blockX" wire:click="deallocate({{$lead->id}})">Release Claim</button>
                                    @else
                                        <br /><br /><p>You can release this lead back into the general pool after 7 days.</p>
                                    @endif
                                @elseif($lead->status == \App\Models\Lead::TRANSFERRED)
                                    {{ \App\Libraries\Interpret::LeadStatus($lead->status) }}
                                @else
                                    {{ \App\Libraries\Interpret::LeadStatus($lead->status) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                                @if(in_array($item->status,[\App\Models\Lead::PROSPECT,\App\Models\Lead::CONTACT_ATTEMPTED,App\Models\Lead::PAUSE_CONTACTING]) || $item->user_id != session('user_id'))
                                    <td>{{ __('***') }}</td>
                                    <td>{{ __('***') }}</td>
                                    <td>{{ __('***') }}</td>
                                    <td>{{ __('***') }}</td>
                                @else
                                    <td>{{ $item->first_name }}</td>
                                    <td>{{ $item->last_name }}</td>
                                    <td>{{ $item->email_address }}</td>
                                    <td>{{ $item->contact_number }}</td>
                                @endif
                                <td>
                                    {{\Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')}}
                                    <span class="badge badge-primary">{{\Carbon\Carbon::parse($item->created_at)->diffForHumans()}}</span>
                                </td>
                                <td>
                                    {{ \App\Libraries\Interpret::LeadStatus($item->status) }}
                                    @if(is_numeric($item->user_id))
                                        @if($item->status == \App\Models\Lead::CLAIMED || ($item->status == \App\Models\Lead::PAUSE_CONTACTING && $item->user_id == session('user_id')))
                                            <span class="badge badge-primary">{{\Carbon\Carbon::parse($item->allocated_at)->diffForHumans()}}</span>
                                        @endif
                                    @else
                                        @if(in_array($item->status,[\App\Models\Lead::PROSPECT,\App\Models\Lead::CONTACT_ATTEMPTED]) && !empty($item->last_contacted_at))
                                            <span class="badge badge-primary tip" title="contacted {{ $item->contact_count }} times, last contacted {{$item->last_contacted_at}}">{{\Carbon\Carbon::parse($item->last_contacted_at)->diffForHumans()}}</span>
                                        @endif
                                    @endif

                                    @if(!in_array($item->status,[\App\Models\Lead::TRANSFERRED]))
                                        <div class="d-block w-100">
                                            <i class="fa fa-envelope"></i>
                                            @foreach($contact_schedule as $chaser)
                                                @if(in_array($chaser->id, $item->events()->where('event_id',\App\Models\LeadEvent::AUTO_CONTACT_ATTEMPTED)->pluck('information')->toArray()))
                                                    <i class="fas fa-check-circle text-success tip" title="Chaser {{$chaser->name}} sent {{$item->events()->where('event_id',\App\Models\LeadEvent::AUTO_CONTACT_ATTEMPTED)->where('information',$chaser->id)->first()->created_at}}"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-muted tip" title="Chaser {{$chaser->name}} not sent yet"></i>
                                                @endif
                                            @endforeach
                                        </div>
                                        @if($item->events()->where('event_id',\App\Models\LeadEvent::MANUAL_CONTACT_ATTEMPTED)->count() != 0)
                                            <div class="d-block w-100">
                                                <i class="fa fa-phone"></i>
                                                @foreach($item->events()->where('event_id',\App\Models\LeadEvent::MANUAL_CONTACT_ATTEMPTED)->get() as $contact)
                                                    <i class="fas fa-phone-square text-success tip" title="Contacted at {{$contact->created_at}}"></i>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if(in_array($item->status,[\App\Models\Lead::PROSPECT,\App\Models\Lead::CONTACT_ATTEMPTED,App\Models\Lead::PAUSE_CONTACTING]) || $item->user_id != session('user_id'))
                                        <button class="btn btn-sm btn-secondary btn-blockX" wire:click="allocate({{$item->id}})">Claim</button>
                                    @elseif($item->status == \App\Models\Lead::CLAIMED || ($item->status == \App\Models\Lead::PAUSE_CONTACTING && $item->user_id == session('user_id')))
                                        <a class="btn btn-sm btn-primary" href="{{route('leads.contact', $item->id)}}">Contact</a>
                                        <button class="btn btn-sm btn-secondary" wire:click="info({{$item->id}})">Info</button>
                                        @if(\Carbon\Carbon::parse($item->allocated_at)->diff(\Carbon\Carbon::now())->days > 7)
                                            <button class="btn btn-sm btn-danger btn-blockX" wire:click="deallocate({{$item->id}})">Release Claim</button>
                                        @endif
                                    @elseif($item->status == \App\Models\Lead::TRANSFERRED)
                                        {{ \App\Libraries\Interpret::LeadStatus($item->status) }}
                                    @else
                                        <button class="btn btn-sm btn-secondary" wire:click="info({{$item->id}})">Info</button>
                                    @endif
                                </td>
                            </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>
            @if($list->count() > 0)
                <div class="mt-3 d-flex justify-content-center">
                    {{ $list->links() }}
                </div>
            @endif

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
