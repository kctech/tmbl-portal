<div class="{{$stat->size}} @if(!is_null($stat->link ?? null)) cursor-pointer @endif" @if(!is_null($stat->link ?? null)) onclick="window.location.href='{{$stat->link}}';" @endif>
    <div class="card {{--border-left-{{$stat->colour}}--}} shadow h-100 w-100">
        <div class="card-body">
            <div class="row h-100 no-gutters align-items-center">
                <div class="col-12">
                    <div class="text-xs font-weight-bold text-{{$stat->colour ?? 'primary'}} text-uppercase mb-1">{{$stat->title}} {{widget_title(@$stat->date)}}</div>
                </div>
                <div class="col mr-2">
                    <div>
                        <div class="h1 mb-0 font-weight-bold text-gray-800">{{$stat->data->current}}</div>
                        @if(!empty($stat->data->previous))
                            <div class="text-small mb-0 text-muted">
                                @php
                                    $stat->difference = number_format((($stat->data->current / $stat->data->previous) * 100) - 100, 0);
                                @endphp
                                @if($stat->data->current > $stat->data->previous)
                                    <span class="text-success tip" data-toggle="tooltip" data-placement="right" title="up on previous period: {{$stat->data->previous}}"><i class="fa fa-fw fa-arrow-up"></i>{{$stat->difference}}%</span>
                                @else
                                    <span class="text-danger tip" data-toggle="tooltip" data-placement="right" title="down on previous period: {{$stat->data->previous}}"><i class="fa fa-fw fa-arrow-down"></i>{!! $stat->difference !!}%</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-auto">
                    <i class="{{$stat->icon}} fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>
