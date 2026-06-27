<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Recomendaciones por regla para cada pregunta
    |--------------------------------------------------------------------------
    |
    | Textos predefinidos para generar recomendaciones cuando una pregunta
    | es respondida negativamente. La prioridad se asigna según el peso
    | de la pregunta: >=12 alta, >=8 media, <8 baja.
    |
    */
    'by_question' => [
        2 => 'Redacte y publique su política de tratamiento de datos en el sitio web corporativo y en todos los puntos de atención al titular.',
        3 => 'Incluya en su política una sección explícita que detalle cada finalidad del tratamiento: comercial, operativa, estadística, entre otras.',
        4 => 'Incorpore en su política los derechos ARCO (Acceso, Rectificación, Cancelación, Oposición) que la Ley 1581 reconoce a los titulares.',
        5 => 'Defina canales formales (correo electrónico, formulario web) y plazos de respuesta para que los titulares puedan ejercer sus derechos.',
        6 => 'Implemente evaluaciones de impacto de privacidad (PIA) antes de lanzar nuevos proyectos que traten datos personales.',
        7 => 'Audite los datos que recolecta y elimine los que no sean estrictamente necesarios para la finalidad declarada.',
        8 => 'Reconfigure sus formularios y sistemas para que, por defecto, solo soliciten los campos obligatorios.',
        9 => 'Establezca una matriz de riesgos de privacidad con periodicidad de revisión anual como mínimo.',
        10 => 'Designe un Oficial de Protección de Datos (DPO) responsable del cumplimiento de la Ley 1581.',
    ],
];
