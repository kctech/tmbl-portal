@php $var = bin2hex(random_bytes(5)); @endphp
<div class="h-100 w-100 card shadow position-absolute overflow-hidden">
    @include('dashboard.widgets.widget_cog',['widget_id' => $url, 'widget_date' => $date])
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="text-sm font-weight-bold text-primary text-uppercase"><i class="fas fa-fw {{$vars->icon}} mr-1"></i> {{$title}}</div>
    </div>
    <div class="card-body position-relative">
        <div class="p-2 d-flex align-items-center justify-content-center flex-column" style="position: absolute; top:0; left:0; right:0; bottom: 0;">
            @if(isset($decription))<div class="pb-1"><small>{{$description}}</small></div>@endif
            @if($graph_data_current == "")
                    <div class="text-center h3 mb-0 text-gray-400"><i class="fal fa-briefcase"></i><br />{{__('No Vacancies')}}</div>
            @else
                <div class="h-100 w-100 position-relative">
                    <canvas id="monthlyChartBar_{{$var}}"></canvas>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    var monthlyChartBar_{{$var}} = new Chart(document.getElementById("monthlyChartBar_{{$var}}"), {
        type: 'doughnut',
        data: {
            datasets: [{
                label:'Current Period',
                data: [{!!$graph_data_current!!}],
                backgroundColor: [{!!$graph_colours_current!!}],
                borderColor: ['rgba(255, 255, 255, 1)'],
                borderWidth: 1
            },
            @if($graph_data_previous != "")
            {
                label:'Previous Period',
                data: [{!!$graph_data_previous!!}],
                backgroundColor: [{!!$graph_colours_previous!!}],
                borderColor: ['rgba(0, 0, 0, 1)'],
                borderWidth: 1
            }
            @endif
            ],
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

<script>
    if(tooltips_enabled){
        $('[data-toggle="tooltip"], .tip').tooltip();
        $('[data-toggle="tooltip"], .tip').click(function () {
            $('[data-toggle="tooltip"], .tip').tooltip("hide");
        });
    }
</script>
