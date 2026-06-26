# Reto Cavaltec

## Aplicación Web para Autodiagnóstico de Cumplimiento de Protección de Datos (Fase de Diseño)

---

# Descripción del reto

La protección de datos personales se ha convertido en una necesidad crítica para las organizaciones, especialmente con marcos regulatorios como la **Ley 1581 de 2012 (Colombia)**, que establece principios, derechos y obligaciones para el tratamiento adecuado de datos personales.

Uno de los mayores retos empresariales consiste en implementar la protección de datos durante todas las fases del desarrollo, lo que implica:

- Pensar en la privacidad desde el inicio de productos y procesos.
- Minimizar riesgos antes de operar.
- Garantizar transparencia y control para los titulares de los datos.

Sin embargo, muchas organizaciones carecen de herramientas prácticas que les permitan evaluar su nivel de cumplimiento de manera sencilla, comprensible y accionable.

Este reto busca desarrollar una solución tecnológica que combine:

- Regulación (Ley 1581) de protección de datos.
- Desarrollo web seguro.
- Experiencia de usuario.
- Inteligencia Artificial aplicada.

---

# Objetivo del reto

Desarrollar una **aplicación web segura, intuitiva y multiempresa** que permita a las organizaciones realizar un **autodiagnóstico del nivel de cumplimiento de la Ley 1581**, enfocado en la fase de diseño, generando:

- Nivel de cumplimiento (%).
- Identificación de brechas.
- Recomendaciones prácticas basadas en IA.
- Estrategias de mejora.

---

# Contexto

Los equipos deberán construir una plataforma web que:

- Permita el registro e inicio de sesión mediante OAuth (Google, Microsoft, entre otros).
- Capture información básica de la empresa.
- Ejecute un cuestionario estructurado (fase de diseño).
- Calcule un resultado porcentual de cumplimiento.
- Presente un diagnóstico visual claro.
- Proporcione estrategias para cerrar brechas.
- Utilice Inteligencia Artificial para:
  - Explicar las preguntas.
  - Ayudar al usuario a responder correctamente.
  - Generar recomendaciones automáticas.

La solución debe ser:

- Fácil de usar.
- Amigable.
- Segura.
- Escalable (multiempresa).
- Basada en buenas prácticas (OWASP y Privacy by Design).

---

# Niveles de participación

## Nivel 1 (Básico)

Funcionalidades esperadas:

- Formulario de diagnóstico.
- Resultado en porcentaje.
- Interfaz simple.

---

## Nivel 2 (Intermedio)

Funcionalidades esperadas:

- Login con OAuth.
- Dashboard con resultados.
- Lógica condicional de preguntas.
- Recomendaciones básicas.

---

## Nivel 3 (Avanzado)

Funcionalidades esperadas:

- Integración con IA (asistencia y recomendaciones).
- Multiempresa.
- Roles:
  - Administrador.
  - Evaluador.
  - Auditor.
- Reportes descargables.
- Históricos de evaluaciones.
- Seguridad avanzada.

---

# Arquitectura funcional

## Módulo de autenticación y acceso

| Categoría | Funcionalidad | Descripción |
|-----------|--------------|-------------|
| Autenticación | Login con OAuth | Acceso mediante Google, Microsoft (Hotmail/Outlook) u otros |
| Autorización | Gestión de roles | Administrador, empresa evaluada y auditor |
| Seguridad | Sesiones seguras | Tokens, cifrado y manejo seguro de sesiones |

---

## Módulo de empresa

| Categoría | Funcionalidad | Descripción |
|-----------|--------------|-------------|
| Registro | Datos básicos | Nombre, NIT, sector, tamaño |
| Multiempresa | Gestión por cliente | Cada empresa puede tener múltiples evaluaciones |

---

## Módulo de Inteligencia Artificial

| Categoría | Funcionalidad | Descripción |
|-----------|--------------|-------------|
| Asistencia | Explicación de preguntas | Traduce términos legales a lenguaje sencillo |
| Apoyo | Orientación para responder | Sugiere cómo interpretar la pregunta |
| Recomendación | Plan de acción | Genera acciones para cerrar brechas |
| Análisis | Interpretación de resultados | Explica el nivel de cumplimiento |

---

# Módulo de diagnóstico (Fase de Diseño)

## Bloque 1: Política de datos personales (Máximo 40%)

### Pregunta 1

**¿Cuenta con una política de tratamiento de datos personales?**

Peso:
- Hereda el peso de las preguntas 2–5.

---

### Pregunta 2

**¿La política está documentada y publicada en un medio de fácil acceso?**

Peso: **10%**

---

### Pregunta 3

**¿Define las finalidades del tratamiento de datos?**

Peso: **10%**

---

### Pregunta 4

**¿Incluye los derechos de los titulares?**

Peso: **10%**

---

### Pregunta 5

**¿Menciona cómo ejercer los derechos de los titulares?**

Peso: **10%**

---

## Bloque 2: Privacidad desde el diseño (Máximo 36%)

### Pregunta 6

**¿Incorpora evaluaciones de impacto (Privacy Impact Assessments)?**

Peso: **12%**

---

### Pregunta 7

**¿Aplica técnicas de minimización de datos?**

Peso: **12%**

---

### Pregunta 8

**¿Configura sus sistemas para recopilar el mínimo de datos por defecto?**

Peso: **12%**

---

## Bloque 3: Gobernanza (Máximo 24%)

### Pregunta 9

**¿Cuenta con un sistema de administración de riesgos?**

Peso: **16%**

---

### Pregunta 10

**¿Cuenta con un oficial de protección de datos personales?**

Peso: **8%**

---

### Pregunta 11

**¿Está designado formalmente?**

Observación:

- Complementaria.
- No suma al puntaje total.

---

## Puntaje máximo

**100%**

---

# Módulo de resultados

| Categoría | Funcionalidad | Descripción |
|-----------|--------------|-------------|
| Indicador | Nivel de cumplimiento (%) | Basado en respuestas positivas |
| Visualización | Medidor tipo Gauge | Indicador visual de 0–100% |
| Brechas | Identificación automática | Preguntas en las que falló |
| Recomendaciones | Plan de mejora | Acciones priorizadas |
| Reportes | Exportables | PDF o Dashboard |

---

# Seguridad de la aplicación

| Categoría | Funcionalidad | Descripción |
|-----------|--------------|-------------|
| Protección | Validación de entradas | Prevención de ataques |
| Seguridad | Mitigación de riesgos comunes | OWASP Top 10 |
| Privacidad | Protección de datos | Buen manejo de la información empresarial |

---

# Criterios de evaluación

| Categoría | Peso | Aspectos evaluados |
|-----------|------|-------------------|
| Alineación con la Ley 1581 | 20% | Interpretación correcta de la normativa, coherencia del diagnóstico |
| Desarrollo técnico | 20% | Calidad del código, arquitectura, buenas prácticas |
| Seguridad | 15% | Implementación de controles, autenticación y autorización |
| Uso de IA | 15% | Utilidad real y precisión de recomendaciones |
| Experiencia de usuario | 10% | Facilidad de uso y diseño intuitivo |
| Calidad del diagnóstico | 10% | Claridad de resultados y capacidad de generar acciones |
| Innovación | 10% | Diferenciadores y funcionalidades adicionales |

---

# Resultado esperado del reto

La aplicación debe permitir que cualquier empresa pueda:

- Comprender su nivel de cumplimiento en la fase de diseño de protección de datos.
- Identificar rápidamente sus debilidades.
- Recibir orientación clara para mejorar.
- Adoptar principios de **Privacy by Design**.

---

# Resumen funcional

## Funcionalidades obligatorias

- Login OAuth.
- Registro de empresas.
- Multiempresa.
- Diagnóstico basado en cuestionario.
- Cálculo automático del porcentaje de cumplimiento.
- Dashboard con resultados.
- Identificación de brechas.
- Recomendaciones.
- Reportes.
- Seguridad OWASP.
- IA para asistencia y recomendaciones.

---

# Tecnologías sugeridas (inferidas)

Aunque el documento no impone tecnologías específicas, la solución está orientada a una arquitectura moderna basada en:

- Frontend Web.
- Backend REST.
- Base de datos.
- OAuth 2.0.
- Integración con un LLM mediante API.
- Arquitectura multiempresa.
- Buenas prácticas de seguridad (OWASP).