# Release Notes Operativas

## Última actualización

2026-06-10

## Entrará en la próxima release

- Pendiente de confirmar después de validar el registro y cerrar el diagnóstico actual.

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

- Registro frontend falla actualmente con mensaje genérico según usuario.
- MCP WordPress/WooCommerce no está operativo por falta de credenciales.
- `_dev/` debe excluirse de cualquier ZIP/deploy público.

## Limpieza post-release

- Actualizar `estado.md`, `roadmap.md`, `decisiones.md`, `release-notes.md` y `visual.html`.
- Confirmar que changelog público coincide con lo publicado.
