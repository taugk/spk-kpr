<div class="card-box height-100-p widget-style3">
    <div class="d-flex flex-wrap">
        <div class="widget-data">
            <div class="weight-700 font-24 text-dark">{{ $value ?? 0 }}</div>
            <div class="font-14 text-secondary weight-500">{{ $label ?? 'Statistik' }}</div>
        </div>
        <div class="widget-icon">
            <div class="icon" data-color="{{ $color ?? '#1b00ff' }}">
                <i class="{{ $icon ?? 'icon-copy dw dw-analytics-21' }}"></i>
            </div>
        </div>
    </div>
</div>
