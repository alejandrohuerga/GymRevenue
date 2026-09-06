# GymRevenue — Decisiones de arquitectura

Registro de decisiones que afectan a la evolución futura del SaaS (§40.16 del AGENTS.md).

## ADR-01 · Base del producto (validación, no SaaS)

La primera versión es una landing orientada a conversión:

`Landing → Calculadora → Resultado → Formulario → Lead (gym_leads) → /gracias`

No se construyen funcionalidades del SaaS completo todavía (sección 38).

## ADR-02 · Lógica de la calculadora aislada en JavaScript

La fórmula de estimación vive en `resources/js/calculator.js`
(`estimateOpportunity`) y la UI únicamente la invoca. El servidor
(`app/Services/OpportunityEstimator.php`) recalcula siempre el valor al
guardar el lead; nunca se confía en datos del frontend.

Fórmula actual (conservadora):

```text
(socios inactivos + bajas mensuales) × cuota media × 50%
```

Es una hipótesis de marketing y debe ajustarse con datos reales.

## ADR-03 · Tabla única `gym_leads`

Solo existe la tabla de leads con los campos definidos en el AGENTS.md.
No hay tablas de members/subscriptions/opportunities hasta que se valide.

## ADR-04 · Base de datos local

En desarrollo se usa SQLite (`DB_CONNECTION=sqlite`). El stack de
producción previsto es MySQL: mantener configurado en `.env.example`
sin romper el entorno local.

## ADR-05 · Analítica preparada sin herramientas invasivas

Eventos listados en las secciones 32/42, emitidos a `window.dataLayer`
si existe (nunca se instala tracking sin consentimiento/configuración
legal):

```text
page_view, calculator_started, calculator_completed,
lead_form_started, lead_submitted, cta_clicked
```

## Visión futura (§43)

El producto debe evolucionar hacia un sistema acumulativo de
datos + decisiones + resultados:

```text
DATOS → ANALIZAR → DETECTAR → PRIORIZAR → ACCIÓN → MEDIR → INGRESOS RECUPERADOS
```

La ventaja competitiva futura es el conocimiento acumulado
(señales de abandono, qué acciones funcionan, qué ingreso se recupera),
no la simple adopción de IA.

Regla final (§44): la prioridad absoluta en esta fase es
`CONVERSIÓN > FUNCIONALIDADES > COMPLEJIDAD`.