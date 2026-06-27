<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe de Diagnóstico - CheckData AI</title>
    <style>
        @page { margin: 20mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1E293B; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #2563EB; }
        .header h1 { font-size: 20px; color: #0F172A; margin: 0 0 5px 0; }
        .header p { color: #64748B; font-size: 10px; margin: 0; }
        .section { margin-bottom: 15px; }
        .section h2 { font-size: 13px; color: #2563EB; border-bottom: 1px solid #E2E8F0; padding-bottom: 5px; margin: 0 0 8px 0; }
        .info-grid { width: 100%; margin-bottom: 10px; }
        .info-grid td { padding: 3px 8px; font-size: 10px; }
        .info-grid td:first-child { color: #64748B; width: 130px; }
        .info-grid td:last-child { font-weight: bold; color: #0F172A; }
        .score-box { text-align: center; padding: 15px; margin: 10px 0; border-radius: 8px; }
        .score-critical { background: #FEE2E2; border: 1px solid #EF4444; }
        .score-low { background: #FEF3C7; border: 1px solid #F59E0B; }
        .score-moderate { background: #FEF9C3; border: 1px solid #EAB308; }
        .score-high { background: #DCFCE7; border: 1px solid #16A34A; }
        .score-number { font-size: 28px; font-weight: bold; }
        .score-label { font-size: 10px; color: #64748B; }
        .block-row { margin: 5px 0; }
        .block-name { font-size: 10px; color: #0F172A; }
        .block-bar { height: 8px; border-radius: 4px; margin: 2px 0; }
        .block-fill { height: 8px; border-radius: 4px; }
        .block-pct { font-size: 9px; color: #64748B; }
        ul { margin: 5px 0; padding-left: 18px; }
        li { margin-bottom: 4px; font-size: 10px; }
        .rec-high { border-left: 3px solid #EF4444; padding-left: 8px; margin-bottom: 6px; }
        .rec-medium { border-left: 3px solid #F59E0B; padding-left: 8px; margin-bottom: 6px; }
        .rec-low { border-left: 3px solid #16A34A; padding-left: 8px; margin-bottom: 6px; }
        .rec-label { font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .rec-high .rec-label { color: #EF4444; }
        .rec-medium .rec-label { color: #F59E0B; }
        .rec-low .rec-label { color: #16A34A; }
        .rec-text { font-size: 10px; color: #0F172A; }
        .rec-origin { font-size: 8px; color: #64748B; }
        .gap-item { margin-bottom: 5px; padding: 4px 6px; background: #FEF3C7; border-radius: 4px; font-size: 10px; }
        .gap-weight { font-weight: bold; color: #F59E0B; }
        .summary-text { font-size: 10px; color: #0F172A; }
        .summary-text p { margin: 6px 0; }
        .footer { text-align: center; color: #94A3B8; font-size: 8px; margin-top: 20px; padding-top: 10px; border-top: 1px solid #E2E8F0; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Informe de Diagnóstico</h1>
        <p>Autodiagnóstico de Cumplimiento - Ley 1581 de 2012</p>
    </div>

    <div class="section">
        <h2>Datos de la Empresa</h2>
        <table class="info-grid">
            <tr><td>Empresa</td><td>{{ $assessment->company->name }}</td></tr>
            <tr><td>NIT</td><td>{{ $assessment->company->nit }}</td></tr>
            <tr><td>Sector</td><td>{{ $assessment->company->sector ?? 'No especificado' }}</td></tr>
            <tr><td>Tamaño</td><td>{{ ['small' => 'Pequeña', 'medium' => 'Mediana', 'large' => 'Grande'][$assessment->company->size] ?? 'No especificado' }}</td></tr>
            <tr><td>Fecha del diagnóstico</td><td>{{ $assessment->created_at->format('d/m/Y H:i') }}</td></tr>
            <tr><td>Evaluador</td><td>{{ $assessment->user->name }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h2>Resultado Global</h2>
        @php
            $score = (int) $assessment->score;
            $scoreClass = $score >= 80 ? 'score-high' : ($score >= 60 ? 'score-moderate' : ($score >= 40 ? 'score-low' : 'score-critical'));
            $scoreColor = $score >= 80 ? '#16A34A' : ($score >= 60 ? '#EAB308' : ($score >= 40 ? '#F59E0B' : '#EF4444'));
        @endphp
        <div class="score-box {{ $scoreClass }}">
            <div class="score-number" style="color: {{ $scoreColor }};">{{ $score }}%</div>
            <div class="score-label">
                {{ $score >= 80 ? 'Cumplimiento Alto' : ($score >= 60 ? 'Cumplimiento Moderado' : ($score >= 40 ? 'Cumplimiento Bajo' : 'Cumplimiento Crítico')) }}
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Desglose por Bloque</h2>
        @foreach($categoryResults as $result)
            @php
                $pct = $result['earned_percentage'];
                $total = $result['max_percentage'];
                $barColor = $pct >= ($total * 0.7) ? '#16A34A' : ($pct >= ($total * 0.4) ? '#F59E0B' : '#EF4444');
                $barWidth = $total > 0 ? min(100, ($pct / $total) * 100) : 0;
            @endphp
            <div class="block-row">
                <div class="block-name">{{ $result['name'] }} <span class="block-pct">{{ $pct }}% / {{ $total }}%</span></div>
                <div class="block-bar" style="background: #E2E8F0;">
                    <div class="block-fill" style="width: {{ $barWidth }}%; background: {{ $barColor }};"></div>
                </div>
            </div>
        @endforeach
    </div>

    @if(count($gaps) > 0)
        <div class="section">
            <h2>Brechas Identificadas ({{ count($gaps) }} áreas de mejora)</h2>
            @foreach($gaps as $gap)
                <div class="gap-item">
                    <strong>{{ $gap['category'] }}:</strong> {{ $gap['question'] }}
                    @if($gap['help_text'])<br><span style="font-size:9px;color:#64748B;">{{ $gap['help_text'] }}</span>@endif
                </div>
            @endforeach
        </div>
    @endif

    @if($assessment->relationLoaded('recommendations') && $assessment->recommendations->isNotEmpty())
        <div class="section">
            <h2>Plan de Acción Recomendado</h2>
            @php $sorted = $assessment->recommendations->sortBy(fn($r) => match($r->priority) { 'high' => 0, 'medium' => 1, 'low' => 2 }); @endphp
            @foreach($sorted as $rec)
                <div class="rec-{{ $rec->priority }}">
                    <div class="rec-label">{{ $rec->priority === 'high' ? 'ALTA' : ($rec->priority === 'medium' ? 'MEDIA' : 'BAJA') }} prioridad</div>
                    <div class="rec-text">{{ $rec->text }}</div>
                    <div class="rec-origin">Origen: {{ $rec->origin === 'ai' ? 'Inteligencia Artificial' : 'Regla de negocio' }}</div>
                </div>
            @endforeach
        </div>
    @endif

    @if($assessment->ai_summary)
        <div class="section">
            <h2>Análisis con Inteligencia Artificial</h2>
            <div class="summary-text">
                @foreach(explode("\n\n", $assessment->ai_summary) as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <div class="footer">
        <p>Generado por CheckData AI - Hackathon Cavaltec / Talento Tech</p>
        <p>Este informe es un autodiagnóstico orientativo y no constituye asesoría legal.</p>
    </div>

</body>
</html>
