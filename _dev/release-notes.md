# Release Notes Operativas

## Última actualización

2026-06-10

## Dev 1.1.0.1 — 2026-06-10

- Añadida gestión desde AuthGate de `users_can_register`.
- Añadida gestión de `woocommerce_registration_generate_password` cuando WooCommerce está activo.
- Ocultado el registro frontend cuando WordPress no permite registros.
- Ajustado AJAX de registro para respetar el estado nativo de registro.
- Ajustada integración WooCommerce para no reactivar registro si WordPress/AuthGate lo desactiva.
- Creada memoria operativa `_dev/` inicial.

## Entrará en la próxima release

- Pendiente de consolidar desde `1.1.0.1` después de QA manual.

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

- QA manual de registro activo/inactivo pendiente.
- Validación WooCommerce de enlace de configuración de contraseña pendiente.
- `_dev/` debe excluirse de cualquier ZIP/deploy público.

## Limpieza post-release

- Actualizar `estado.md`, `roadmap.md`, `decisiones.md`, `release-notes.md` y `visual.html`.
- Confirmar que changelog público coincide con lo publicado.
