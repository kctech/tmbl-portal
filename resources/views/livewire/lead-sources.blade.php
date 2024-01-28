<div>

@section('breadcrumbs')
    {{ Breadcrumbs::render('leads') }}
    <button wire:click="add_source()" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-plus"></i> Add Source </button>
@endsection

<div class="card mb-4">
    <div class="card-body">
        <h3>Filters</h3>

        <div class="d-flex align-self-center">
            <div class="card">
                <div class="card-body bg-light d-flex align-items-center">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input wire:click="$set('source_status','')" type="radio" id="source_status_all" name="source_status" class="custom-control-input" value="" {{checked('', $source_status)}}>
                        <label class="custom-control-label" for="source_status_all">All Sources</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input wire:click="$set('source_status','{{\App\Models\ApiKey::ACTIVE}}')" type="radio" id="source_status_no" name="source_status" class="custom-control-input" value="{{\App\Models\ApiKey::ACTIVE}}" {{checked(\App\Models\ApiKey::ACTIVE, $source_status)}}>
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
                            <td>{{ $item->api_token }}</td>
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
