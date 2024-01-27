<div>

@section('breadcrumbs')
    {{ Breadcrumbs::render('leads') }}
    <a href="{{ route('leads.sources') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-inbox-in"></i> Lead Sources</a>
@endsection


<div class="card mb-4">
    <div class="card-body">
        <h3>Filters</h3>

        <div class="d-flex align-self-center">
            <div class="card">
                <div class="card-body bg-light d-flex align-items-center">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input wire:click="$set('lead_status','id_asc')" type="radio" id="lead_status_all" name="lead_status" class="custom-control-input" value="" {{checked('', $lead_status)}}>
                        <label class="custom-control-label" for="lead_status_all">All Leads</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input wire:click="$set('lead_status','{{\App\Models\Lead::NEW_LEAD}}')" type="radio" id="lead_status_no" name="lead_status" class="custom-control-input" value="{{\App\Models\Lead::NEW_LEAD}}" {{checked(\App\Models\Lead::NEW_LEAD, $lead_status)}}>
                        <label class="custom-control-label" for="lead_status_no">New Leads Only</label>
                    </div>
                </div>
            </div>
            <div class="card ml-3">
                <div class="card-body bg-light">
                    <input class="form-control" placeholder="Client Search" wire:model="search_filter" value="{{$search_filter}}" />
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
                            <td>{{ $item->first_name }}</td>
                            <td>{{ $item->last_name }}</td>
                            <td>{{ $item->email_address }}</td>
                            <td>{{ $item->contact_number }}</td>
                            <td>
                                {{\Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')}}
                                <span class="badge badge-primary">{{\Carbon\Carbon::parse($item->created_at)->diffForHumans()}}</span>
                            </td>
                            <td>{{ $item->status }}</td>
                            <td>actions</td>
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


</div>
