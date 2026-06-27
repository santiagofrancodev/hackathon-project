<x-mail::message>
# Informe de Diagnóstico - CheckData AI

Hola **{{ $assessment->user->name }}**,

Adjunto encontrarás el informe de diagnóstico de cumplimiento de la **Ley 1581 de 2012** para la empresa **{{ $assessment->company->name }}**.

## Resumen del resultado

- **Empresa:** {{ $assessment->company->name }}
- **Fecha del diagnóstico:** {{ $assessment->created_at->format('d/m/Y') }}
- **Puntaje de cumplimiento:** {{ (int) $assessment->score }}%
- **Nivel:** 
  @php $s = (int) $assessment->score; @endphp
  {{ $s >= 80 ? 'Cumplimiento Alto' : ($s >= 60 ? 'Cumplimiento Moderado' : ($s >= 40 ? 'Cumplimiento Bajo' : 'Cumplimiento Crítico')) }}

El archivo PDF con el detalle completo del diagnóstico está adjunto a este correo.

<x-mail::button :url="route('diagnostic.results', $assessment)">
Ver resultado en línea
</x-mail::button>

---

Si tienes dudas sobre este informe, consulta con un asesor especializado en protección de datos.

<x-mail::subcopy>
Este mensaje fue generado automáticamente por **CheckData AI**.
Hackathon Cavaltec / Talento Tech — Protección de Datos Personales.
</x-mail::subcopy>
</x-mail::message>
