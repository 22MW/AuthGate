# Desarrollador

## Última actualización

2026-06-16

## Resumen humano

Bloque A quedó pusheado como dev `1.1.0.3`. Bloque B quedó cerrado en `1.1.0.5`. Bloque C implementado localmente: pestaña “Textos” separada y guardado independiente.

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

## Pendiente

- QA manual del Bloque C.

## No volver a investigar

- Bloque A no toca settings de admin ni base de datos.
- El enlace de inicio no debe aparecer en popup.
- Bloque B guarda HTML permitido mediante `wp_kses_post()`.
- Bloque C conserva sanitización previa por tipo de texto.

## Riesgos o bloqueos

- No hay bloqueo técnico confirmado.

## Próximo paso recomendado

- Validar guardado de pestaña General y pestaña Textos por separado.
