# Desarrollador

## Última actualización

2026-06-16

## Resumen humano

Añadida mejora pre-release para shortcodes popup: ahora aceptan `label` para personalizar el texto del botón y mantienen `button_class` para clases CSS.

## Descubierto

- El modo popup ya existía y usaba el texto global `btn_popup`.
- `button_class` ya existía.
- Faltaba personalización de texto por shortcode.

## Hecho

- Añadido atributo `label` a `[authgate_login]`, `[authgate_register]` y `[authgate_auth]`.
- Sanitizado `label` con texto seguro.
- Saneadas clases de `button_class` por clase individual.
- Añadidos ejemplos en la pestaña Shortcodes del backend.
- Validado `php -l` en `class-auth-forms.php` y `class-auth-settings.php`.
- Validado `git diff --check`.

## Pendiente

- Commit y push de `1.1.0.2`.

## No volver a investigar

- El atributo elegido por decisión del usuario es `label`, no `button_text`.

## Riesgos o bloqueos

- No hay bloqueo técnico confirmado.

## Próximo paso recomendado

- Probar shortcodes popup con `label` en frontend.
