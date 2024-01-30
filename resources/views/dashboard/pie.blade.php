@php $var = bin2hex(random_bytes(5)); @endphp
<div class="h-100 w-100 card shadow position-absolute overflow-hidden">
    @include('dashboard.widgets.widget_cog',['widget_id' => $url, 'widget_date' => $date])
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="text-sm font-weight-bold text-primary text-uppercase"><i class="fas fa-fw {{$vars->icon}} mr-1"></i> {{$title}} {{widget_title($date)}}</div>
    </div>
    <div class="card-body position-relative">
        <div class="p-2 d-flex align-items-center justify-content-center flex-column" style="position: absolute; top:0; left:0; right:0; bottom: 0;">
        <div class="pb-1"><small>{{$description}}</small></div>
            <div class="h-100 w-100 position-relative">
                @if($count == 0)
                    <div class="text-center h3 mb-0 text-gray-400"><i class="fal fa-users"></i><br />{{__('No Statistics')}}</div>
                @else
                    <canvas id="stageTypeEnteredChartPie_{{$var}}"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

@if($count != 0)
    <script>
        var stageTypeEnteredChartPie_{{$var}} = new Chart(document.getElementById("stageTypeEnteredChartPie_{{$var}}"), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{!!$graph_data!!}],
                    backgroundColor: [{!!$graph_colours!!}],
                    borderColor: ['rgba(255, 255, 255, 1)'],
                    borderWidth: 1
                }],
                labels: [{!!$graph_labels!!}]
            },
            options: {
                aspectRatio: 1.25,
                maintainAspectRatio: false,
                legend : {
                    position: 'bottom',
                    display: true,
                    labels: {
                        fontColor: '#000'
                    }
                }
            }
        });
    </script>
@endif

<script>
    if(tooltips_enabled){
        $('[data-toggle="tooltip"], .tip').tooltip();
        $('[data-toggle="tooltip"], .tip').click(function () {
            $('[data-toggle="tooltip"], .tip').tooltip("hide");
        });
    }
</script>
