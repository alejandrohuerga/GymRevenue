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

---

# 45. ESTADO ACTUAL DEL PROYECTO — DESPUÉS DE LA LANDING

La landing principal ya está construida.

Por tanto, NO volver a trabajar en la estructura visual general de la landing salvo que una prueba o problema concreto de UX/conversión lo justifique.

La siguiente prioridad de desarrollo es convertir la calculadora en una funcionalidad real y dejar preparado el flujo de captación.

## Objetivo inmediato

Construir y probar este flujo completo:

```text
LANDING
   ↓
CALCULADORA
   ↓
RESULTADO INMEDIATO
   ↓
CTA "ANALIZAR MI GIMNASIO GRATIS"
   ↓
FORMULARIO DE CONTACTO
   ↓
LEAD GUARDADO
   ↓
PÁGINA DE GRACIAS
```

IMPORTANTE:

- No pedir registro antes de utilizar la calculadora.
- No pedir email antes de mostrar el resultado.
- El usuario debe poder introducir los datos y ver el resultado inmediatamente.
- El email y los datos de contacto se solicitan DESPUÉS del resultado, cuando el usuario ya ha recibido valor.
- No crear todavía sistema de usuarios, login, dashboard privado ni suscripciones.

La razón es maximizar la conversión y validar primero si el problema despierta interés comercial.

---

# 46. CALCULADORA V1 — ESPECIFICACIÓN FUNCIONAL

La calculadora es ahora la funcionalidad prioritaria del proyecto.

Debe existir tanto en la Home como en `/calculadora` reutilizando el mismo componente y la misma lógica.

## Campos V1

Utilizar únicamente estos cuatro campos:

```text
members
average_fee
inactive_members
monthly_cancellations
```

Correspondencia visual:

```text
Número de socios
Cuota mensual media (€)
Socios actualmente inactivos
Bajas mensuales aproximadas
```

### Reglas de validación

- Todos los campos son obligatorios.
- Todos los campos son numéricos.
- No aceptar números negativos.
- `members` debe ser un entero positivo.
- `inactive_members` debe ser un entero positivo o cero.
- `monthly_cancellations` debe ser un entero positivo o cero.
- `average_fee` debe ser mayor que cero.
- `inactive_members` no puede superar `members`.
- Los errores deben mostrarse junto al campo correspondiente.
- La validación debe existir en frontend y backend.
- Nunca confiar en el cálculo realizado únicamente por JavaScript.

## Valores iniciales sugeridos

```text
Número de socios: 350
Cuota mensual media: 40 €
Socios inactivos: 30
Bajas mensuales: 15
```

Estos valores son únicamente valores de ejemplo para facilitar la interacción y no deben presentarse como datos reales de gimnasios.

---

# 47. FÓRMULA DE LA CALCULADORA V1

La calculadora debe mostrar una **oportunidad estimada de recuperación**, no afirmar que el gimnasio está perdiendo exactamente esa cantidad.

La fórmula inicial será deliberadamente sencilla y conservadora.

## Tasas configurables

Definir las tasas como constantes/configuración fácilmente modificable:

```javascript
const RECOVERY_RATE_INACTIVE = 0.20;
const RECOVERY_RATE_CANCELLATION = 0.15;
```

Estas tasas son hipótesis iniciales de marketing y NO datos científicos ni resultados garantizados.

## Cálculo

```text
inactive_opportunity = inactive_members × average_fee × 0.20

cancellation_opportunity = monthly_cancellations × average_fee × 0.15

estimated_monthly_opportunity =
    inactive_opportunity + cancellation_opportunity

estimated_annual_opportunity =
    estimated_monthly_opportunity × 12
```

### Ejemplo

Con:

```text
30 socios inactivos
40 € cuota media
15 bajas mensuales
```

El cálculo sería:

```text
30 × 40 × 0.20 = 240 €

15 × 40 × 0.15 = 90 €

240 + 90 = 330 €/mes

330 × 12 = 3.960 €/año
```

El resultado debe mostrar:

```text
OPORTUNIDAD ESTIMADA

330 €/mes

Hasta 3.960 €/año como referencia estimada
```

No utilizar lenguaje como:

```text
"Estás perdiendo 330 € al mes."
"GymRevenue te hará recuperar 3.960 €."
```

Utilizar lenguaje como:

```text
"Tu oportunidad estimada de recuperación podría ser de 330 €/mes."
```

Incluir siempre una nota:

> Esta cifra es una estimación orientativa basada en los datos introducidos. No representa dinero perdido de forma exacta ni garantiza ingresos recuperables.

## Importante para futuras versiones

No añadir más variables ni crear una fórmula más compleja hasta disponer de datos reales de gimnasios.

Las tasas deben poder cambiarse sin rehacer la interfaz.

Cuando tengamos datos reales, analizar:

- porcentaje real de recuperación de inactivos;
- porcentaje real de recuperación de bajas;
- diferencias por tipo de gimnasio;
- cuota media;
- antigüedad del socio;
- frecuencia de asistencia;
- comportamiento previo a la baja.

La fórmula V1 existe para validar conversión, no para construir todavía un modelo predictivo perfecto.

---

# 48. UX DEL RESULTADO

El resultado debe aparecer sin recargar la página.

Debe contener como mínimo:

```text
OPORTUNIDAD ESTIMADA
330 €/mes

≈ 3.960 €/año

Desglose
────────────────────────
Socios inactivos       240 €/mes
Bajas mensuales         90 €/mes
────────────────────────

¿Quieres descubrir exactamente dónde está la oportunidad?

[ ANALIZAR MI GIMNASIO GRATIS ]
```

El desglose es importante porque aumenta la transparencia y permite entender de dónde sale la cifra.

El CTA posterior al resultado debe abrir o mostrar el formulario de captación.

No obligar al usuario a registrarse para consultar el resultado.

---

# 49. FORMULARIO DESPUÉS DEL RESULTADO

Una vez mostrado el resultado, solicitar únicamente los datos necesarios para contactar con el interesado.

Campos:

```text
Nombre
Email
Nombre del gimnasio
Software que utilizas (opcional)
Consentimiento
```

Los datos de la calculadora deben conservarse y asociarse al lead.

El lead debe guardar también:

```text
members
average_fee
inactive_members
monthly_cancellations
estimated_opportunity
```

De esta forma podremos saber posteriormente qué tipo de gimnasio está mostrando interés y qué oportunidades estima la calculadora.

No solicitar teléfono en esta fase.

No solicitar dirección física.

No solicitar datos de socios individuales.

---

# 50. IMPLEMENTACIÓN TÉCNICA DE LA CALCULADORA

La lógica debe estar separada de la presentación.

## Frontend

Utilizar:

- Blade.
- Tailwind existente.
- JavaScript vanilla.

No introducir React, Vue, Livewire u otro framework para esta funcionalidad.

La calculadora debe:

1. Capturar inputs.
2. Validarlos en frontend.
3. Calcular el resultado instantáneamente.
4. Formatear cantidades en euros correctamente.
5. Mostrar el desglose.
6. Mostrar/ocultar el resultado de forma accesible.
7. Llevar el foco al resultado cuando corresponda.
8. Mantener la experiencia correcta en móvil.

## Backend

Aunque el resultado se muestre instantáneamente con JavaScript, Laravel debe poder recibir y validar los datos.

El backend será la fuente de confianza para cualquier dato que se guarde.

Debe utilizar:

- Form Request o validación equivalente.
- CSRF.
- Tipos y límites razonables.
- Rate limiting cuando el endpoint sea público y pueda abusarse.
- Protección anti-spam en el formulario de lead.

---

# 51. ORDEN EXACTO DE TRABAJO PARA OPENCODE — SIGUIENTE TAREA

Cuando el usuario pida a OpenCode continuar el proyecto, debe seguir este orden y no adelantarse a fases futuras.

## STEP 38 — Inspeccionar el estado actual

Antes de modificar nada:

1. Inspeccionar `routes/web.php`.
2. Inspeccionar `resources/views`.
3. Localizar la calculadora existente en Home.
4. Localizar componentes Blade relacionados con calculadora.
5. Revisar `resources/js` y la configuración Vite existente.
6. Revisar si ya existe algún script JavaScript para la calculadora.
7. Revisar estado de migraciones y base de datos.
8. No recrear archivos que ya existan.

## STEP 39 — Implementar calculadora V1

Implementar únicamente:

- inputs;
- validación frontend;
- fórmula V1;
- resultado instantáneo;
- desglose;
- formato monetario;
- CTA posterior al resultado;
- responsive;
- accesibilidad básica.

No implementar todavía:

- login;
- registro;
- dashboard privado;
- CSV;
- IA;
- integraciones;
- pagos;
- automatizaciones;
- WhatsApp;
- email marketing automático.

## STEP 40 — Implementar backend del flujo de cálculo

Crear o adaptar el endpoint necesario para que Laravel pueda validar los datos.

La lógica de presentación puede ejecutarse en JavaScript para UX inmediata, pero nunca guardar datos sin validación backend.

## STEP 41 — Implementar captación del lead

Después del resultado:

```text
Resultado
   ↓
CTA
   ↓
Formulario
   ↓
POST /lead
   ↓
Validación
   ↓
gym_leads
   ↓
/gracias
```

El formulario debe conservar los datos de la calculadora mediante campos ocultos o sesión de forma segura, evitando confiar en valores manipulables del cliente sin volver a validarlos.

## STEP 42 — Base de datos

Utilizar la tabla `gym_leads` definida anteriormente.

No crear todavía tablas de usuarios del SaaS ni de socios del gimnasio.

## STEP 43 — Testing

Probar como mínimo:

### Caso válido

```text
350 socios
40 €
30 inactivos
15 bajas
```

Resultado esperado:

```text
330 €/mes
3.960 €/año
```

### Casos inválidos

- valores negativos;
- cuota 0;
- campos vacíos;
- texto donde se espera número;
- inactivos > socios;
- valores excesivamente grandes;
- envío repetido del formulario;
- manipulación de valores desde frontend.

### Responsive

Comprobar:

- móvil;
- tablet;
- desktop.

### Calidad

Comprobar:

```text
npm run build
```

y las pruebas/validaciones Laravel disponibles en el proyecto.

No dejar errores de consola.

---

# 52. DESPUÉS DE TERMINAR LA CALCULADORA

Una vez implementado el flujo y comprobado que funciona correctamente, NO saltar directamente a construir el SaaS completo.

El siguiente objetivo será:

```text
CALCULADORA FUNCIONAL
       ↓
CAPTACIÓN DE LEADS
       ↓
PUBLICAR
       ↓
CONSEGUIR PRIMEROS GIMNASIOS INTERESADOS
       ↓
OBSERVAR RESPUESTAS
       ↓
VALIDAR PROBLEMA
       ↓
VALIDAR DISPOSICIÓN A PROBAR/PAGAR
       ↓
ENTONCES CONSTRUIR MVP REAL
```

No construir el dashboard completo hasta que exista evidencia de interés.

La primera versión del producto real deberá empezar posteriormente con importación CSV y análisis de datos reales de gimnasios.

---

# 53. CRITERIO DE DECISIÓN ANTES DE CONSTRUIR EL SAAS

No avanzar a desarrollo profundo únicamente porque la landing esté terminada.

La evidencia que buscamos es:

1. Personas del sector utilizan la calculadora.
2. Algunas dejan sus datos.
3. Algunas aceptan que analicemos sus datos reales.
4. Existe interés en recibir recomendaciones concretas.
5. Existe disposición a probar una solución.
6. Idealmente existe disposición a pagar.

Si la calculadora recibe visitas pero nadie deja datos, revisar propuesta y CTA.

Si dejan datos pero nadie quiere un análisis real, revisar el problema que estamos resolviendo.

Si quieren análisis real, entonces construir el MVP de análisis.

Si además existe disposición a pagar, priorizar monetización.

---

# 54. PROMPT OPERATIVO PARA OPENCODE

Cuando se quiera continuar desde el estado actual, el usuario puede indicar a OpenCode:

```text
Lee AGENTS.md y continúa exactamente desde el estado actual del proyecto.

La landing ya está construida. No rehagas la landing ni añadas funcionalidades futuras.

La siguiente tarea es STEP 38 en adelante: implementar la Calculadora V1 de GymRevenue y dejar funcionando el flujo completo hasta la captación del lead.

Primero inspecciona el proyecto existente y reutiliza los componentes actuales.

Implementa únicamente:

1. Calculadora sin registro previo.
2. Campos: número de socios, cuota mensual media, socios inactivos y bajas mensuales.
3. Validación frontend y backend.
4. Cálculo instantáneo con JavaScript vanilla.
5. Fórmula definida en AGENTS.md.
6. Resultado con oportunidad mensual y anual.
7. Desglose de la estimación.
8. CTA para analizar el gimnasio.
9. Formulario con nombre, email, gimnasio, software opcional y consentimiento.
10. Guardado del lead y de los datos de la calculadora en MySQL.
11. Redirección a /gracias.
12. Responsive, accesibilidad, seguridad y testing.

No implementes login, registro, dashboard, CSV, IA, integraciones, pagos, automatizaciones ni WhatsApp.

Antes de modificar archivos, inspecciona qué existe actualmente. No sobrescribas ni recrees componentes innecesariamente.

Al terminar, ejecuta las comprobaciones disponibles, incluyendo npm run build y tests/validaciones Laravel, y dime exactamente qué archivos has modificado, qué has implementado y qué queda pendiente.
```

Este prompt debe considerarse una guía operativa y siempre debe prevalecer el contenido completo de este AGENTS.md sobre cualquier interpretación distinta del prompt.

