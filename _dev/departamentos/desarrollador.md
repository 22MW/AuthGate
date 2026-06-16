# Desarrollador

## Última actualización

2026-06-16

## Resumen humano

Bloques A, B y C cerrados. Bloque D implementado localmente: pestaña “CSS propio”, activación frontend, CodeMirror, sanitización y presets.

## Descubierto

- El checkbox newsletter estaba en `form-register.php` y en el panel registro de `form-combined.php`.
- El render inline se controla desde `render_wrapper()` en `class-auth-forms.php`.
- El enlace de inicio fuera del contenedor quedaba visualmente desplazado a la derecha.
- Los textos WYSIWYG existentes usan `wp_editor()` y se guardan con `wp_kses_post()`.

## Hecho

- Añadido helper `AuthGate_Forms::is_mailmint_available()`.
- Ocultado checkbox newsletter si Mail Mint no está disponible.
- Añadido enlace “Ir a la página de inicio” solo en render inline.
- Reubicado el enlace de inicio al final del contenido inline, usando clases de enlace del formulario.
- Validado `php -l` en archivos PHP tocados.
- Validado `git diff --check`.
- Bloque B: añadido campo `inline_intro_html` en ajustes.
- Bloque B: renderizado solo en modo inline de login, registro y combinado.
- Bloque B QA: centrado frontend y toolbar WYSIWYG ampliada.
- Bloque C: añadida pestaña `Textos`.
- Bloque C: quitado bloque de textos de la pestaña General.
- Bloque C: añadido guardado independiente `authgate_save_strings` con nonce propio.
- Bloque D: añadida pestaña `CSS propio`.
- Bloque D: añadido guardado independiente `authgate_save_css` con nonce propio.
- Bloque D: CSS frontend se carga solo si el checkbox está activo.
- Bloque D: presets blanco y oscuro añadidos como texto copiable.
- Bloque D: preset blanco queda activo por defecto y define caja, inputs, botones y espaciado sin forzar fuentes.
- Bloque D: ajustado preset blanco para reducir inputs, mejorar textos e incluir logo/título/descripción de página protegida dentro de la card.
- Bloque D: aplicado al preset oscuro el mismo patrón visual del preset blanco.
- Bloque D: extraída base común para que blanco y oscuro compartan tamaños, espaciados y estructura; solo cambian colores.

## Pendiente

- QA manual del Bloque D.

## No volver a investigar

- Bloque A no toca settings de admin ni base de datos.
- El enlace de inicio no debe aparecer en popup.
- Bloque B guarda HTML permitido mediante `wp_kses_post()`.
- Bloque C conserva sanitización previa por tipo de texto.
- Bloque D bloquea patrones CSS peligrosos básicos: `@import`, `javascript:`, `expression()`, `behavior` y `-moz-binding`.

## Riesgos o bloqueos

- No hay bloqueo técnico confirmado.

## Próximo paso recomendado

- Validar pestaña CSS, guardado, checkbox de activación y render frontend.
