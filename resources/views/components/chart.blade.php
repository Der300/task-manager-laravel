<div class="card card-outline card-{{ $chart['cardColor'] ?? 'primary' }}">
    <div class="card-header">
        <h3 class="card-title">
            <i class="{{ $chart['icon'] ?? '' }} text-{{ $chart['cardColor'] ?? 'primary' }} mr-2"></i>
            {{ $chart['title'] ?? 'Chart' }}
        </h3>
    </div>
    @php
        $reponsive = $chart['responsive'] ?? true;
    @endphp
    <div class="card-body" style="{{ $reponsive === false ? 'height: 216px; overflow-y: auto;' : '' }}">
        <canvas id="{{ $chart['id'] }}" style="height: {{ $chart['height'] ?? 300 }}px; width: 100%;"></canvas>
    </div>
    @if ($reponsive === false)
        <div class="card-footer"></div>
    @endif
</div>

@push('js')
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx_{{ $chart['id'] }} = document.getElementById('{{ $chart['id'] }}').getContext('2d');
            const labelInfo = {!! json_encode($chart['labelInfo'] ?? [], JSON_UNESCAPED_UNICODE) !!};
            new Chart(ctx_{{ $chart['id'] }}, {
                type: '{{ $chart['type'] }}',
                data: {
                    labels: {!! json_encode($chart['labels']) !!},
                    datasets: [
                        @foreach ($chart['datasets'] as $dataset)
                            {
                                label: {!! json_encode($dataset['label']) !!},
                                data: {!! json_encode($dataset['data']) !!},
                                borderColor: {!! json_encode($dataset['borderColor'] ?? 'rgba(255,255,255,0.8)') !!},
                                backgroundColor: {!! json_encode($dataset['backgroundColor'] ?? 'rgba(0,0,0,0.1)') !!},
                                borderWidth: 1,
                                minBarLength: 1,
                                type: {!! json_encode($dataset['type'] ?? $chart['type']) !!},
                                fill: {!! json_encode($dataset['fill'] ?? false) !!},
                                yAxisID: {!! json_encode($dataset['yAxisID'] ?? 'y-axis-1') !!},
                                stack: {!! json_encode($dataset['stack'] ?? null) !!},
                            },
                        @endforeach
                    ]
                },
                options: {
                    responsive: {!! json_encode($chart['responsive'] ?? true) !!},
                    maintainAspectRatio: false,
                    legend: {
                        display: {!! json_encode($chart['legendDisplay'] ?? true) !!},
                        position: {!! json_encode($chart['legendPosition'] ?? 'top') !!}
                    },
                    scales: {!! json_encode(
                        $chart['scales'] ?? ['yAxes' => [['id' => 'y-axis-1']]],
                        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, //JSON_UNESCAPED_SLASHES=> khong encode dau /   JSON_UNESCAPED_UNICODE=> khong encode dau tieng viet
                    ) !!},
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItems) {
                                const idx = tooltipItems[0].index;
                                return labelInfo[idx] ?? tooltipItems[0].label;
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
