# Estado del plugin

## Última actualización

2026-06-16

## Resumen humano

AuthGate está en rama dev `mishaAuthDev` con versión dev `1.1.0.1` ya pusheada. La P1 de control de registro quedó implementada: AuthGate puede gestionar `users_can_register`, duplicar la opción WooCommerce `woocommerce_registration_generate_password` y ocultar el registro frontend cuando WordPress no permite registros.

## Estado general

Dev implementada, pusheada y QA P1 confirmada por el usuario; pendiente decisión de release estable.

## Hecho

- Plugin localizado en `app/public/wp-content/plugins/AuthGate/`.
- Archivo principal confirmado: `authgate.php`.
- Versión dev actual declarada: `1.1.0.1`.
- Text domain detectado: `authgate`.
- Repo Git propio confirmado.
- Rama actual confirmada: `mishaAuthDev`.
- MCP WooCommerce operativo en lectura.
- WooCommerce activo confirmado: `10.7.0`.
- Entorno local confirmado: `wp_environment_type: local`.
- P1 implementada: control backend para `users_can_register`.
- P1 implementada: control backend para `woocommerce_registration_generate_password` si WooCommerce está activo.
- P1 implementada: frontend oculta registro cuando WordPress no permite registro.
- P1 implementada: AJAX `authgate_register` queda bloqueado si registro está desactivado.
- Dev push realizado: commit `87216ff release: bump dev version to 1.1.0.1`.
- QA P1 confirmado por el usuario el 2026-06-16.
- `CHANGELOG.md` actualizado con entrada `1.1.0.1`.

## En curso

- Preparar release estable solo si QA queda OK y el usuario lo autoriza.

## Bloqueado

- Release estable bloqueada hasta permiso explícito.

## Próximo paso recomendado

- Preparar release estable si el usuario lo solicita explícitamente.

## No volver a investigar

- Ruta real del plugin: `app/public/wp-content/plugins/AuthGate/`.
- Archivo principal: `authgate.php`.
- Rama de trabajo: `mishaAuthDev`.
- Versión dev actual: `1.1.0.1`.
- QA P1 OK confirmado por el usuario.
- Sitio local confirmado por MCP: `https://plugins.local`.
- WooCommerce activo confirmado por MCP: `10.7.0`.
- PHP entorno MCP: `8.3.30`.
- Prefijo BD: `m22w_`.
- No hacer release estable, tag ni deploy sin autorización explícita.
