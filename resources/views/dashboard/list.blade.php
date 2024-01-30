@php $var = bin2hex(random_bytes(5)); @endphp
<div class="{{$stat->size}}">
<div class="h-100 w-100 card shadow {{--position-absolute overflow-hidden--}}">
    <div class="d-md d-lg d-xl">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="text-sm font-weight-bold text-primary text-uppercase"><i class="fas fa-fw {{$stat->icon}} mr-1"></i> {{$stat->title}} {{widget_title($stat->date,['FUTURE'])}}</div>
            <span class="badge badge-primary badge-pill text-auto">{{number_format($stat->count)}}</span>
        </div>
    </div>
    <div class="card-body p-2 d-flex align-items-center justify-content-center" style="height: 300px;">
        {{--Visible only on sm--}}
        <div class="d-sm d-none">
            <div class="text-center">
                @if($stat->route ?? false)<a href="{{route($stat->route, ((array) ($stat->qs ?? [])))}}">@endif
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{$stat->title}}<br />{{widget_title($stat->date,['FUTURE'])}}</div>
                    <div class="h1 mb-0 font-weight-bold text-gray-800">{{number_format($stat->count)}}</div>
                @if($stat->route ?? false)</a>@endif
            </div>
        </div>
        @if($stat->count == 0)
            <div class="text-center d-md d-lg d-xl">
                    <div class="h3 mb-0 text-gray-400"><i class="fal {{$stat->icon}}"></i><br />{{__('No data')}}</div>
                </div>
            </div>
        @else
            <div class="{{--d-md--}} w-100 h-100 position-relative">
                {{--Visible only on md--}}
                <div class="w-100 h-100 position-relative">
                    <div class="list_{{$var}} position-absolute overflow-auto" style="top:0; bottom:0; left:0; right:0;">
                        <div class="list-group list-group-flush">
                            @foreach($stat->data as $key => $row)
                                <div class="list-group-item p-1">
                                    <div class="row">
                                        <div class="col-6 text-truncate">
                                            {{$key}}
                                        </div>
                                        <div class="col-6 text-right">
                                            {{$row->current}}
                                            @if(!empty($row->previous))
                                                <div class="text-small mb-0 text-muted">
                                                    @php
                                                        $row->difference = number_format((($row->current / $row->previous) * 100) - 100, 0);
                                                    @endphp
                                                    @if($row->current > $row->previous)
                                                        <span class="text-success tip" data-toggle="tooltip" data-placement="right" title="up on previous period: {{$row->previous}}"><i class="fa fa-fw fa-arrow-up"></i>{{$row->difference}}%</span>
                                                    @else
                                                        <span class="text-danger tip" data-toggle="tooltip" data-placement="right" title="down on previous period: {{$row->previous}}"><i class="fa fa-fw fa-arrow-down"></i>{!! $row->difference !!}%</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="overflow-msg overflow-msg-more d-none position-relative text-center">
                            @if($stat->count > count($stat->data)) <a href="#">{{__('Click here to see the full list')}} <i class="ml-1 fa fa-external-link"></i></a> @else &nbsp; @endif
                        </div>
                    </div>
                    <div class="overflow-msg overflow-msg-scroll d-none position-absolute text-muted">
                        <small>scroll to see more</small>
                    </div>
                </div>
            </div>
            {{--Visible only on lg
            <div class="d-lg w-100 h-100 position-relative">

                <div class="w-100 h-100 position-relative">
                    <div class="list_{{$var}} position-absolute overflow-auto" style="top:0; bottom:0; left:0; right:0;">
                        <div class="list-group list-group-flush">
                            @foreach($stat->data as $row)
                                <div class="list-group-item p-1">
                                    <div class="row">
                                        <div class="col-5">
                                            <a href="javascript:void(0);" onclick="ats.candidateDetails({{@$row->app_id}})" class="text-body tip" title="" data-toggle="tooltip" data-original-title="View {{@$row->first_name}}'s record card">
                                                <i class="fas fa-id-card"></i> <strong>{{@$row->first_name}} {{@$row->last_name}}</strong>
                                            </a>
                                            <br />
                                            <a href="javascript:void(0);" onclick="ats.vacancyDetails({{@$row->vacancy_id}})" class="text-body tip" title="" data-toggle="tooltip" data-original-title="View {{@$row->vacancy_title}} ({{@$row->vacancy_id}}) details">
                                                <i class="fas fa-info-circle"></i> {{@$row->vacancy_title}}
                                            </a>
                                        </div>
                                        <div class="col">
                                            {{$row->int_date}} {{__('at')}} {{$row->int_time}}<br />
                                            <small class="text-muted">{{__('In')}} {{carbonObjectFromDateTime($row->int_date,$row->int_time)->diffForHumans()}}</small>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-sm" type="button" id="dropdownMenuButton{{$row->app_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{$row->app_id}}">
                                                    <a class="dropdown-item" href="{{route('candidate.cv', $row->app_id)}}" target="_blank"><i class="fa fa-fw fa-file-download"></i> CV</a>
                                                    <div>
                                                        <form id="CV_{{@$row->app_id}}" action="{{temp_signed_route('ical.interview.download', 60)}}" method="POST" target="_blank">
                                                            @csrf
                                                            <input type="hidden" name="appId" value="{{$row->app_id}}" />
                                                            <input type="hidden" name="interviewNumber" value="{{$row->int_number}}" />
                                                            <button type="submit" class="dropdown-item"><i class="fa fa-fw fa-calendar-day"></i> Download iCal File</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="overflow-msg overflow-msg-more d-none position-relative text-center">
                            @if($stat->count > count($stat->data)) <a href="#">{{__('Click here to see the full list')}} <i class="ml-1 fa fa-external-link"></i></a> @else &nbsp; @endif
                        </div>
                    </div>
                    <div class="overflow-msg overflow-msg-scroll d-none position-absolute text-muted">
                        <small>scroll to see more</small>
                    </div>
                </div>
            </div>
            --}}
        @endif
    </div>
</div>

<script>
    $('.list_{{$var}}').each(function(index, value) {
        if(this.offsetHeight < this.scrollHeight){
            $(this).parent().find('.overflow-msg').each(function(index, value) {
                $(this).removeClass('d-none');
            });
        }
    });
</script>
</div>
