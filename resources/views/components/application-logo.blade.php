<svg viewBox="0 0 280 60" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => 'text-white']) }}>
    <defs>
        <linearGradient id="sg" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#3B82F6"/>
            <stop offset="100%" stop-color="#2563EB"/>
        </linearGradient>
    </defs>
    <g transform="translate(6, 6)">
        <path d="M24 0L44 10V30C44 44 34 52 24 56C14 52 4 44 4 30V10Z"
              fill="url(#sg)"/>
        <path d="M15 27L21 36L33 19"
              stroke="white" stroke-width="3.5" fill="none"
              stroke-linecap="round" stroke-linejoin="round"/>
    </g>

    <text x="56" y="34" font-family="Inter, system-ui, sans-serif" font-weight="800" font-size="18" letter-spacing="-0.3">
        <tspan fill="currentColor">Check</tspan>
        <tspan fill="#3B82F6">Data</tspan>
        <tspan fill="currentColor"> AI</tspan>
    </text>

    <text x="56" y="50" font-family="Inter, system-ui, sans-serif" font-weight="300" font-size="8" fill="#94A3B8" letter-spacing="0.3">
        <tspan>by </tspan>
        <tspan font-weight="600" letter-spacing="1.5">CAVALTEC</tspan>
    </text>
</svg>
