# Desarrollador

## Última actualización

2026-06-16

## Resumen humano

Bloque A quedó pusheado como dev `1.1.0.3`. Bloque B implementado localmente: WYSIWYG bajo logo/cabecera para formularios inline login, registro y combinado.

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

## Pendiente

- QA manual del Bloque B.

## No volver a investigar

- Bloque A no toca settings de admin ni base de datos.
- El enlace de inicio no debe aparecer en popup.
- Bloque B guarda HTML permitido mediante `wp_kses_post()`.

## Riesgos o bloqueos

- No hay bloqueo técnico confirmado.

## Próximo paso recomendado

- Validar que el texto aparece en inline login/register/combined y no en popup.
