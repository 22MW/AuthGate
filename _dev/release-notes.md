# Release Notes Operativas

## Última actualización

2026-06-16

## Dev 1.1.0.3 — 2026-06-16

- Bloque A: ocultado checkbox newsletter cuando Mail Mint no está disponible.
- Bloque A: añadido enlace “Ir a la página de inicio” en formularios inline.
- QA Bloque A confirmado por el usuario.

## Dev 1.1.0.2 — 2026-06-16

- Añadido atributo `label` para personalizar el texto del botón en shortcodes con `mode="popup"`.
- Mantenido y saneado `button_class` para clases CSS adicionales del botón popup.
- Añadidos ejemplos de `label` y `button_class` en la pestaña Shortcodes del backend.

## Dev 1.1.0.1 — 2026-06-10

- Añadida gestión desde AuthGate de `users_can_register`.
- Añadida gestión de `woocommerce_registration_generate_password` cuando WooCommerce está activo.
- Ocultado el registro frontend cuando WordPress no permite registros.
- Ajustado AJAX de registro para respetar el estado nativo de registro.
- Ajustada integración WooCommerce para no reactivar registro si WordPress/AuthGate lo desactiva.
- Creada memoria operativa `_dev/` inicial.
- QA P1 confirmado por el usuario el 2026-06-16.
- `CHANGELOG.md` actualizado con entrada `1.1.0.1`.

## Entrará en la próxima release

- `1.1.0.1`, `1.1.0.2` y `1.1.0.3` pendientes de consolidar en versión estable cuando el usuario autorice release.

## Queda fuera

- Refactor general.
- Cambios de arquitectura.
- Deploy o publicación.
- Versionado automático.

## Validaciones pendientes

- `php -l` en PHP tocado antes de cerrar.
- `git diff --check`.
- QA manual de login, registro, lost password, reset y páginas protegidas.
- Validación de integración WooCommerce si el flujo de registro depende de WooCommerce.

## Riesgos antes de publicar

- Release estable pendiente de autorización explícita.
- `_dev/` debe excluirse de cualquier ZIP/deploy público.

## Limpieza post-release

- Actualizar `estado.md`, `roadmap.md`, `decisiones.md`, `release-notes.md` y `visual.html`.
- Confirmar que changelog público coincide con lo publicado.
