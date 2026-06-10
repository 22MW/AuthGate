# Roadmap

## Urgente

- Diagnosticar por qué el registro frontend devuelve `Se ha producido un error. Inténtalo de nuevo.` en local con WooCommerce activo.
- Confirmar si el fallo viene de ajustes de registro WordPress/WooCommerce, validación AJAX, nonce, GDPR, antibot o creación de usuario.
- Validar que los cambios P0 aplicados no introducen regresiones en login, registro, reset y bloqueo de `wp-login.php`.

## Recomendado

- Usar MCP WooCommerce para diagnóstico de solo lectura cuando aporte datos reales.
- Ejecutar auditoría de seguridad dirigida sobre AJAX, nonces, capabilities, sanitización, escaping, SQL y uninstall.
- Ejecutar QA funcional mínimo: login, registro, lost password, reset, páginas protegidas, WooCommerce My Account.
- Revisar exclusión de `_dev/` en release/ZIP público.

## Futuro

- Definir soporte explícito para proxy/CDN si se necesita confiar en cabeceras de IP.
- Revisar documentación pública y changelog antes de una nueva release.
- Preparar checklist release cuando el plugin esté validado.

## Bloqueado

- Fix del registro bloqueado hasta confirmar causa raíz.
- Cambios de settings WordPress/WooCommerce bloqueados hasta permiso explícito.

## Descartado

- No hacer refactor general dentro del diagnóstico actual.
- No mezclar release, commit o deploy con la corrección funcional.
