# Reglas de Estilo para OpenCode

## Stack Técnico
- Laravel 11 / Blade
- Tailwind CSS
- MySQL
- Alpine.js / Vanilla JS

## Guía de Diseño (Anti-IA Generic)
- Estilo visual: Editorial / Minimalismo asimétrico.
- Usar clases de Tailwind con bordes rectos o mínimos (`rounded-none` o `rounded-sm`).
- No usar colores pastel ni gradientes de fondo.
- Paleta: Base `zinc-950`, bordes `zinc-800`, texto `zinc-100`/`zinc-400`, acento `emerald-400`.
- Todo formulario debe incluir la directiva `@csrf` de Laravel.

# GymRevenue — AGENTS.md

## 1. Contexto del proyecto

GymRevenue es un micro-SaaS B2B dirigido inicialmente a gimnasios independientes.

### Objetivo del producto

GymRevenue ayuda a un gimnasio a detectar oportunidades de ingresos que están pasando desapercibidas, por ejemplo:

- Socios inactivos.
- Socios con señales de riesgo de baja.
- Leads sin seguimiento.
- Pruebas/no-shows pendientes.
- Otras oportunidades de recuperación.

La propuesta no es sustituir el software de gestión del gimnasio. GymRevenue debe analizar los datos que el gimnasio ya tiene y convertirlos en acciones prioritarias.

### Posicionamiento principal

> Tu gimnasio está perdiendo ingresos. GymRevenue te dice dónde.

Alternativa:

> Descubre qué socios y oportunidades deberías recuperar antes de que sea demasiado tarde.

### Principio de producto

No construir un "software de gestión de gimnasios" genérico.

El producto debe centrarse en:

```text
DATOS
  ↓
ANÁLISIS
  ↓
OPORTUNIDADES
  ↓
PRIORIZACIÓN
  ↓
ACCIÓN
  ↓
RESULTADO / INGRESOS RECUPERADOS
```

---

# 2. Objetivo actual

Estamos en la primera fase del proyecto.

NO estamos construyendo todavía el SaaS completo.

La primera versión debe validar si existe interés comercial.

## MVP actual

```text
Landing
  ↓
Calculadora
  ↓
Resultado estimado
  ↓
Formulario
  ↓
Lead guardado
  ↓
Página de gracias
```

El objetivo inicial es conseguir gimnasios interesados y validar el problema antes de invertir tiempo en desarrollar el producto completo.

---

# 3. Stack tecnológico

Utilizar:

- Laravel.
- PHP.
- Blade.
- Tailwind CSS.
- JavaScript vanilla para la calculadora.
- MySQL.
- Vite.
- Git/GitHub.

No introducir React, Vue, Inertia, Livewire u otros frameworks salvo que exista una necesidad clara.

Mantener la primera versión sencilla, rápida y mantenible.

---

# 4. Principios de desarrollo

## 4.1 No sobreingeniería

No crear arquitecturas complejas para funcionalidades que todavía no necesitamos.

Priorizar:

1. Conversión.
2. Velocidad.
3. UX.
4. SEO.
5. Mantenibilidad.
6. Escalabilidad razonable.

## 4.2 Mobile first

La landing debe funcionar perfectamente en:

- Móvil.
- Tablet.
- Desktop.

La mayoría de componentes deben diseñarse primero pensando en pantallas pequeñas.

## 4.3 Componentización

NO crear una única vista Blade gigantesca.

Utilizar componentes Blade reutilizables.

Ejemplo:

```text
resources/views/
├── layouts/
│   └── app.blade.php
├── pages/
│   ├── home.blade.php
│   ├── calculator.blade.php
│   ├── thanks.blade.php
│   ├── pricing.blade.php
│   ├── how-it-works.blade.php
│   ├── privacy.blade.php
│   ├── cookies.blade.php
│   └── legal.blade.php
└── components/
    ├── navbar.blade.php
    ├── hero.blade.php
    ├── problem.blade.php
    ├── solution.blade.php
    ├── how-it-works.blade.php
    ├── calculator.blade.php
    ├── calculator-result.blade.php
    ├── dashboard-preview.blade.php
    ├── benefits.blade.php
    ├── target-customer.blade.php
    ├── faq.blade.php
    ├── final-cta.blade.php
    └── footer.blade.php
```

---

# 5. Arquitectura inicial de rutas

Preparar inicialmente:

```php
Route::get('/', ...)->name('home');

Route::get('/calculadora', ...)->name('calculator');

Route::post('/calculadora', ...)->name('calculator.calculate');

Route::post('/lead', ...)->name('lead.store');

Route::get('/gracias', ...)->name('thanks');

Route::get('/como-funciona', ...)->name('how-it-works');

Route::get('/precios', ...)->name('pricing');

Route::get('/aviso-legal', ...)->name('legal');

Route::get('/privacidad', ...)->name('privacy');

Route::get('/cookies', ...)->name('cookies');
```

La calculadora puede hacer el cálculo en JavaScript para obtener resultado instantáneo.

El servidor debe validar siempre los datos enviados.

---

# 6. Estructura de la HOME

La Home es la página principal de conversión.

Orden obligatorio recomendado:

```text
1. Navbar
2. Hero
3. Social proof / confianza (cuando exista)
4. Problema
5. Solución
6. Cómo funciona
7. Calculadora
8. Resultado / CTA
9. Dashboard preview
10. Beneficios
11. Diferenciación
12. Para quién es
13. FAQ
14. CTA final
15. Footer
```

---

# 7. NAVBAR

Objetivo: navegación sencilla y conversión.

Contenido:

```text
GYMREVENUE

Cómo funciona
Calculadora
Precios

[ Analizar mi gimnasio ]
```

En móvil:

- Logo.
- Menú hamburguesa.
- CTA visible cuando sea posible.

No añadir enlaces innecesarios.

El CTA principal siempre debe dirigir a la calculadora o al flujo de análisis.

---

# 8. HERO

## Objetivo

Explicar en pocos segundos:

- Qué es GymRevenue.
- Para quién es.
- Qué problema soluciona.
- Qué acción debe realizar el visitante.

## Copy inicial

Título:

> Tu gimnasio está perdiendo ingresos. GymRevenue te dice dónde.

Subtítulo:

> Descubre qué socios están en riesgo, qué clientes están inactivos y qué oportunidades de recuperación estás dejando pasar.

CTA principal:

> Analizar mi gimnasio gratis

Microcopy:

> Sin cambiar tu software · Análisis inicial gratuito · Resultados en minutos

## Diseño

Desktop:

```text
┌──────────────────────────────┬─────────────────────────────┐
│ TÍTULO                       │                             │
│ SUBTÍTULO                    │     DASHBOARD MOCKUP        │
│                              │                             │
│ [ CTA ]                      │                             │
│                              │                             │
│ microcopy                    │                             │
└──────────────────────────────┴─────────────────────────────┘
```

Móvil:

```text
Título
Subtítulo
CTA
Microcopy
Dashboard
```

El dashboard debe parecer un producto SaaS real, pero no debe presentarse como datos reales.

---

# 9. SECCIÓN PROBLEMA

Título:

> El problema no siempre es conseguir más socios.

Texto:

> Muchos gimnasios ya tienen clientes, leads y datos suficientes para aumentar sus ingresos. El problema es que muchas oportunidades están dispersas y pasan desapercibidas.

Crear cuatro tarjetas:

### Socios en riesgo

Socios cuya actividad está disminuyendo y podrían terminar dándose de baja.

### Socios inactivos

Personas que siguen pagando pero llevan demasiado tiempo sin acudir.

### Leads sin seguimiento

Personas que mostraron interés pero nunca terminaron convirtiéndose.

### Oportunidades perdidas

Situaciones que podrían generar ingresos pero nadie está siguiendo.

Cada tarjeta debe tener:

- Icono.
- Título.
- Descripción breve.
- Estado visual.

No utilizar gráficos complejos aquí.

---

# 10. SECCIÓN SOLUCIÓN

Título:

> No necesitas otro software para gestionar tu gimnasio.

Subtítulo:

> GymRevenue analiza los datos que ya tienes para encontrar oportunidades de ingresos.

Visual:

```text
TU SOFTWARE ACTUAL
       ↓
      DATOS
       ↓
  GYMREVENUE
       ↓
OPORTUNIDADES
       ↓
    ACCIÓN
```

Frase destacada:

> Tu software almacena los datos. GymRevenue te ayuda a actuar sobre ellos.

Debe quedar muy claro que GymRevenue complementa el software actual.

---

# 11. CÓMO FUNCIONA

Mostrar tres pasos.

## 01 — Analiza

> Introduces los datos de tu gimnasio o conectas tu software.

## 02 — Detecta

> GymRevenue identifica socios y oportunidades que requieren atención.

## 03 — Actúa

> Obtienes una lista priorizada de dónde centrar tus esfuerzos.

Visual:

```text
DATOS → ANÁLISIS → OPORTUNIDADES → ACCIÓN
```

Mantenerlo simple.

---

# 12. CALCULADORA

Esta es una de las partes más importantes de la Home.

## Título

> ¿Cuánto dinero podría estar dejando escapar tu gimnasio?

## Subtítulo

> Introduce unos pocos datos y obtén una estimación de tu oportunidad de recuperación.

## Campos iniciales

```text
Número de socios
[ 350 ]

Cuota mensual media
[ 40 € ]

Socios actualmente inactivos
[ 30 ]

Bajas mensuales aproximadas
[ 15 ]
```

CTA:

> Calcular mi oportunidad

## Requisitos UX

- Validación inmediata.
- Valores numéricos.
- Formato monetario.
- No permitir valores negativos.
- Mensajes de error claros.
- Resultado sin recargar la página.
- Diseño muy visible.
- Animación ligera al mostrar resultado.
- Compatible con teclado y accesibilidad.

---

# 13. LÓGICA DE LA CALCULADORA

IMPORTANTE:

La cifra mostrada es una estimación comercial, NO una afirmación de dinero realmente perdido.

No utilizar frases como:

> Estás perdiendo exactamente 1.200 €.

Utilizar:

> Oportunidad estimada: 1.200 €/mes.

La fórmula inicial debe ser deliberadamente conservadora y fácil de explicar.

La lógica debe quedar aislada en JavaScript para poder modificarla posteriormente sin rehacer la UI.

Ejemplo conceptual:

```text
INPUTS
├── members
├── average_fee
├── inactive_members
└── monthly_cancellations

        ↓

ESTIMACIÓN

        ↓

estimated_opportunity
```

No inventar una fórmula sofisticada sin validarla.

La fórmula inicial será una hipótesis de marketing y deberá ajustarse después de obtener datos reales.

---

# 14. RESULTADO DE LA CALCULADORA

Ejemplo visual:

```text
┌─────────────────────────────────────┐
│                                     │
│       OPORTUNIDAD ESTIMADA          │
│                                     │
│            1.200 €/mes              │
│                                     │
│ Según los datos introducidos,       │
│ existe una oportunidad potencial    │
│ de recuperación.                    │
│                                     │
│ ¿Quieres descubrir exactamente      │
│ dónde está?                         │
│                                     │
│ [ ANALIZAR MI GIMNASIO GRATIS ]     │
│                                     │
└─────────────────────────────────────┘
```

Debe existir una explicación pequeña:

> Esta cifra es una estimación orientativa basada en los datos introducidos y no garantiza ingresos recuperables.

---

# 15. FORMULARIO DE CAPTACIÓN

Después del resultado:

Título:

> Descubre exactamente dónde está la oportunidad.

Campos iniciales:

```text
Nombre
Email
Nombre del gimnasio
Software que utilizas (opcional)
```

El formulario debe incluir:

- CSRF.
- Validación backend.
- Validación frontend.
- Mensajes de error.
- Honeypot o protección anti-spam.
- Consentimiento correspondiente para el tratamiento de datos.
- Enlaces a privacidad.

No solicitar información innecesaria.

---

# 16. MODELO INICIAL DE BASE DE DATOS

Crear una tabla para leads.

Nombre:

```text
gym_leads
```

Campos:

```text
id
gym_name
contact_name
email
software
members
average_fee
inactive_members
monthly_cancellations
estimated_opportunity
consent_at
created_at
updated_at
```

No crear todavía tablas de members, subscriptions, opportunities, etc.

---

# 17. DASHBOARD PREVIEW

Título:

> De cientos de socios a las oportunidades que importan.

Mostrar mockup:

```text
GYMREVENUE
────────────────────────────────

Resumen

Socios analizados             428

🔴 Alto riesgo                 14
🟠 Inactivos                   31
🟡 Seguimiento pendiente      18

Oportunidad estimada       2.840 €

────────────────────────────────

ACCIONES PRIORITARIAS

Carlos      Riesgo alto       45 €/mes
Miguel      Inactivo           39 €/mes
Laura       Riesgo alto        50 €/mes
Pedro       Sin seguimiento    45 €/mes
```

Este contenido es demostrativo.

No afirmar que son datos reales.

Frase:

> No necesitas analizar cientos de filas. GymRevenue te dice dónde mirar.

---

# 18. BENEFICIOS

Título:

> Convierte tus datos en acciones.

Beneficios:

### Detecta socios en riesgo

Identifica señales que pueden preceder a una baja.

### Recupera clientes inactivos

Encuentra socios que siguen pagando pero han reducido o detenido su actividad.

### Prioriza oportunidades

No todas las oportunidades tienen el mismo valor. GymRevenue ayuda a priorizarlas.

### Reduce el trabajo manual

Evita revisar continuamente hojas de cálculo para descubrir qué clientes necesitan atención.

### Aprovecha tu software actual

No necesitas sustituir inmediatamente tus herramientas actuales.

### Mide resultados

La visión futura del producto debe permitir medir qué acciones generan recuperación real.

---

# 19. DIFERENCIACIÓN

Título:

> GymRevenue no sustituye tu software. Lo complementa.

Comparación conceptual:

```text
SOFTWARE DE GESTIÓN
- Guarda clientes
- Gestiona cuotas
- Gestiona reservas
- Gestiona operaciones

GYMREVENUE
- Analiza datos
- Detecta oportunidades
- Prioriza acciones
- Ayuda a recuperar ingresos
- Mide resultados
```

No atacar ni desacreditar a otros proveedores.

---

# 20. PARA QUIÉN ES

Título:

> Creado para gimnasios que quieren aprovechar mejor sus datos.

Perfil:

```text
✓ Gimnasios independientes
✓ 100–1.000 socios
✓ 1–3 centros
✓ Cuotas recurrentes
✓ Software de gestión o Excel
✓ Quieren reducir bajas
✓ Quieren recuperar clientes
```

Mensaje:

> Si tienes socios, datos y oportunidades que no estás siguiendo, GymRevenue está pensado para ti.

---

# 21. CTA FINAL

Título:

> ¿Cuánto dinero podría estar dejando escapar tu gimnasio?

Subtítulo:

> Descúbrelo gratis con nuestra calculadora.

CTA:

> Analizar mi gimnasio

No añadir múltiples CTA diferentes.

---

# 22. FAQ

Preguntas iniciales:

### ¿Tengo que cambiar mi software?

> No. GymRevenue está pensado para complementar las herramientas que ya utilizas.

### ¿Cuánto cuesta?

> Durante la fase inicial de validación, el análisis será gratuito.

### ¿Qué datos necesito?

> Inicialmente podrás proporcionar algunos datos básicos y, posteriormente, importar información de tu gimnasio para obtener un análisis más detallado.

### ¿Necesito conocimientos técnicos?

> No. La herramienta debe estar diseñada para que cualquier propietario o responsable de gimnasio pueda utilizarla.

### ¿GymRevenue gestiona mi gimnasio?

> No. GymRevenue se centra en detectar oportunidades de ingresos y ayudarte a priorizarlas.

### ¿Mis datos están seguros?

> Explicar únicamente las medidas de seguridad que realmente estén implementadas. Nunca inventar certificaciones o garantías.

---

# 23. FOOTER

Contenido:

```text
GYMREVENUE

Revenue intelligence for gyms.

Producto
- Cómo funciona
- Calculadora
- Precios

Recursos
- Blog

Empresa
- Contacto

Legal
- Aviso legal
- Privacidad
- Cookies

© 2026 GymRevenue
```

---

# 24. PÁGINA /calculadora

Crear una página específica:

```text
/calculadora
```

Objetivo:

SEO + conversión.

Título:

> Calculadora de ingresos perdidos para gimnasios

Subtítulo:

> Estima cuánto podrías recuperar identificando socios inactivos y otras oportunidades.

Debe contener:

1. Introducción corta.
2. Calculadora.
3. Resultado.
4. CTA.
5. Explicación de la metodología.
6. FAQ.
7. Enlaces legales.

La calculadora debe poder utilizarse directamente sin crear cuenta.

---

# 25. PÁGINA /como-funciona

Estructura:

```text
Hero
↓
El problema
↓
Cómo funciona
↓
Ejemplo de análisis
↓
Dashboard
↓
Beneficios
↓
CTA
```

Explicar:

```text
1. Proporcionas datos
2. Analizamos
3. Detectamos oportunidades
4. Priorizamos
5. Actúas
6. Medimos
```

---

# 26. PÁGINA /precios

Durante validación mantenerla sencilla.

No presentar precios definitivos como si estuvieran cerrados.

Hipótesis inicial:

```text
STARTER
49 €/mes

GROWTH
99 €/mes

PRO
199 €/mes
```

Estos precios deben considerarse provisionales hasta validar disposición a pagar.

---

# 27. PÁGINA /gracias

Después de enviar el formulario:

```text
✓ Hemos recibido tus datos.

Estamos preparando el siguiente paso de tu análisis.

[ Ver cómo funciona GymRevenue ]

[ Volver a la calculadora ]
```

No dejar al usuario en una página vacía.

---

# 28. SEO

Implementar desde el principio:

- `<title>` único por página.
- Meta description.
- H1 único.
- Jerarquía H2/H3 correcta.
- URLs limpias.
- Open Graph.
- Canonical.
- Sitemap posteriormente.
- Robots.txt.
- Alt text en imágenes.
- Buen rendimiento.
- HTML semántico.

Keywords iniciales:

```text
calculadora ingresos gimnasio
cómo reducir bajas gimnasio
cómo recuperar socios inactivos
retención de clientes gimnasio
cómo recuperar clientes gimnasio
churn gimnasio
socios inactivos gimnasio
seguimiento leads gimnasio
```

No hacer keyword stuffing.

---

# 29. Diseño visual

GymRevenue debe parecer un SaaS B2B moderno, profesional y orientado a datos.

Características:

- Diseño limpio.
- Mucho espacio en blanco.
- Tipografía moderna.
- Jerarquía visual fuerte.
- Tarjetas discretas.
- Bordes suaves.
- Dashboard como elemento visual principal.
- Microanimaciones sutiles.
- Excelente responsive.

Evitar:

- Diseño de gimnasio genérico.
- Fotos de personas levantando pesas como recurso principal.
- Exceso de gradientes.
- Animaciones excesivas.
- Stock photos innecesarias.
- Elementos visuales que parezcan una plantilla genérica.

La marca debe transmitir:

```text
DATOS
PRECISIÓN
DINERO
CONTROL
CRECIMIENTO
```

---

# 30. Accesibilidad

Cumplir buenas prácticas básicas:

- Labels reales para inputs.
- Contraste suficiente.
- Navegación por teclado.
- Estados focus visibles.
- Botones claramente identificables.
- Errores asociados a campos.
- No depender únicamente del color.
- `aria-*` solo cuando sea necesario.

---

# 31. Rendimiento

Prioridades:

1. HTML semántico.
2. CSS optimizado.
3. JS mínimo.
4. Imágenes optimizadas.
5. Lazy loading cuando corresponda.
6. Evitar librerías innecesarias.
7. Evitar fuentes externas excesivas.

La landing debe cargar rápidamente.

---

# 32. Analítica

Preparar el proyecto para medir:

```text
page_view
calculator_started
calculator_completed
lead_form_started
lead_submitted
cta_clicked
```

Objetivo:

Poder saber:

```text
Visitantes
    ↓
Usuarios calculadora
    ↓
Resultados
    ↓
Leads
    ↓
Usuarios interesados
```

No implementar herramientas de tracking invasivas sin el consentimiento y configuración legal correspondientes.

---

# 33. Seguridad

Aplicar siempre:

- CSRF.
- Validación backend.
- Sanitización/escape Blade.
- Rate limiting en endpoints sensibles.
- Protección anti-spam.
- No confiar en datos del frontend.
- No guardar datos que no sean necesarios.
- Variables sensibles en `.env`.
- No subir `.env` a Git.
- No mostrar errores internos en producción.

---

# 34. Copywriting

Regla principal:

NO vender funcionalidades.

Vender resultados.

Malo:

> "GymRevenue utiliza algoritmos avanzados para analizar tus datos."

Mejor:

> "Descubre qué socios deberías recuperar primero."

Malo:

> "Dashboard con análisis avanzado."

Mejor:

> "De cientos de socios a las oportunidades que importan."

La comunicación debe ser clara para un propietario de gimnasio, no para un desarrollador.

---

# 35. No inventar pruebas sociales

No crear:

- Falsos clientes.
- Falsos testimonios.
- Falsos logos.
- Falsos números de usuarios.
- Falsos porcentajes.
- Falsos resultados.

Hasta tener clientes reales, utilizar:

> "Estamos preparando GymRevenue para los primeros gimnasios."

Cuando existan datos reales, sustituirlos por evidencia real.

---

# 36. Flujo completo de usuario

Debe funcionar así:

```text
VISITANTE
    ↓
HOME
    ↓
ENTIENDE EL PROBLEMA
    ↓
USA CALCULADORA
    ↓
INTRODUCE DATOS
    ↓
CALCULA RESULTADO
    ↓
VE OPORTUNIDAD ESTIMADA
    ↓
PULSA CTA
    ↓
DEJA DATOS
    ↓
POST /lead
    ↓
MYSQL
    ↓
/gracias
```

---

# 37. Roadmap posterior

Una vez validada la landing:

## Fase 2

Registro de gimnasios.

## Fase 3

Importación CSV.

## Fase 4

Análisis de socios.

## Fase 5

Detección de oportunidades.

## Fase 6

Dashboard.

## Fase 7

Sistema de acciones.

## Fase 8

Automatizaciones.

## Fase 9

Integraciones con software de gimnasios.

## Fase 10

Pagos y suscripciones.

## Fase 11

SEO y adquisición a escala.

---

# 38. Funcionalidades que NO construir ahora

No construir todavía:

- App móvil.
- Chatbot.
- IA compleja.
- WhatsApp automático.
- Sistema completo de reservas.
- Control de acceso.
- Facturación.
- Gestión de entrenamientos.
- Nutrición.
- CRM completo.
- Integraciones complejas.
- Multiidioma.
- Multi-moneda.
- Sistema de afiliados.

Primero validar.

---

# 39. Orden de implementación

El agente debe implementar en este orden:

```text
STEP 1
Crear proyecto Laravel

STEP 2
Configurar Tailwind/Vite

STEP 3
Crear layout

STEP 4
Crear sistema visual base

STEP 5
Navbar

STEP 6
Hero

STEP 7
Problema

STEP 8
Solución

STEP 9
Cómo funciona

STEP 10
Calculadora UI

STEP 11
Lógica JavaScript calculadora

STEP 12
Resultado

STEP 13
Formulario

STEP 14
Migración gym_leads

STEP 15
Modelo GymLead

STEP 16
FormRequest / validación

STEP 17
Controller

STEP 18
Persistencia MySQL

STEP 19
Página gracias

STEP 20
Dashboard preview

STEP 21
Beneficios

STEP 22
Diferenciación

STEP 23
Target customer

STEP 24
FAQ

STEP 25
CTA final

STEP 26
Footer

STEP 27
Página calculadora

STEP 28
Página cómo funciona

STEP 29
Página precios

STEP 30
Páginas legales

STEP 31
Responsive

STEP 32
SEO

STEP 33
Accesibilidad

STEP 34
Rendimiento

STEP 35
Analytics

STEP 36
Testing

STEP 37
Revisión final
```

---

# 40. Reglas para OpenCode

Cuando OpenCode implemente una tarea:

1. Inspeccionar primero el proyecto existente.
2. No sobrescribir archivos sin necesidad.
3. Reutilizar componentes existentes.
4. Mantener responsabilidades separadas.
5. No introducir dependencias sin justificar.
6. Ejecutar tests después de cambios relevantes.
7. Comprobar `npm run build` cuando se modifique frontend.
8. Comprobar Laravel/PHP cuando se modifique backend.
9. Revisar responsive.
10. No inventar datos comerciales.
11. No inventar testimonios.
12. No inventar integraciones.
13. No implementar funcionalidades futuras sin pedirlo.
14. Mantener el código preparado para evolucionar hacia SaaS.
15. Priorizar una UX de conversión sobre adornos visuales.
16. Si una decisión afecta a la arquitectura futura, documentarla.
17. Si existe una ambigüedad importante, detenerse y explicar la decisión antes de hacer una implementación destructiva.

---

# 41. Definition of Done — Landing

La primera versión se considera terminada cuando:

- [ ] Home completa.
- [ ] Responsive móvil/tablet/desktop.
- [ ] Calculadora funcional.
- [ ] Resultado visible sin recarga.
- [ ] Formulario funcional.
- [ ] Datos guardados en MySQL.
- [ ] Validación backend.
- [ ] Protección CSRF.
- [ ] Página de gracias.
- [ ] Página `/calculadora`.
- [ ] Página `/como-funciona`.
- [ ] Página `/precios`.
- [ ] Footer.
- [ ] FAQ.
- [ ] SEO básico.
- [ ] Accesibilidad básica.
- [ ] Rendimiento revisado.
- [ ] Sin datos ficticios presentados como reales.
- [ ] Sin errores de consola.
- [ ] Sin errores Laravel.
- [ ] Build frontend correcto.
- [ ] Código organizado en componentes.
- [ ] Git commit limpio.

---

# 42. Objetivo empresarial de esta versión

El objetivo de esta landing NO es conseguir miles de visitas.

El objetivo es validar:

> ¿Un propietario de gimnasio tiene suficiente interés en conocer sus oportunidades de recuperación como para introducir los datos de su gimnasio y dejar sus datos de contacto?

Métrica principal:

```text
VISITANTE
   ↓
CALCULADORA
   ↓
RESULTADO
   ↓
LEAD
```

Métricas secundarias:

- CTR del CTA.
- Inicio de calculadora.
- Finalización de calculadora.
- Conversión a lead.
- Coste por lead cuando haya publicidad.
- Número de gimnasios que aceptan probar el análisis.
- Número de gimnasios que posteriormente pagan.

---

# 43. Visión a largo plazo

GymRevenue debe evolucionar hacia:

```text
                 GYMREVENUE
                      │
          ┌───────────┴───────────┐
          ↓                       ↓
       DATOS                   ACCIONES
          ↓                       ↓
     ANALIZAR                 CONTACTAR
          ↓                       ↓
     DETECTAR                 RECUPERAR
          ↓                       ↓
    PRIORIZAR                 MEDIR
          └───────────┬───────────┘
                      ↓
             INGRESOS RECUPERADOS
```

La ventaja competitiva futura no debe ser simplemente "usar IA".

Debe ser el conocimiento acumulado sobre:

- Qué señales predicen abandono.
- Qué oportunidades merecen atención.
- Qué acciones funcionan.
- Qué mensajes funcionan.
- Qué segmentos responden.
- Cuánto ingreso se recupera.
- Qué patrones aparecen en distintos gimnasios.

Ese sistema de datos + decisiones + resultados es el verdadero producto.

---

# 44. Regla final

Construir primero lo que permita responder:

> "¿Los gimnasios quieren esto?"

No construir primero lo que simplemente sea divertido de programar.

La prioridad absoluta durante esta fase es:

```text
CONVERSIÓN
    >
FUNCIONALIDADES
    >
COMPLEJIDAD
```
