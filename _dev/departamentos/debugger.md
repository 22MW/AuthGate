# Debugger

## Última actualización

2026-06-10

## Resumen humano

Incidencia abierta: el registro frontend muestra `Se ha producido un error. Inténtalo de nuevo.` en local con WooCommerce activo. MCP WooCommerce ya responde y confirma entorno real.

## Descubierto

- El mensaje corresponde al string genérico `error_generic`.
- Sitio confirmado por MCP: `https://plugins.local`.
- AuthGate activo: `AuthGate/authgate.php` versión `1.1.0`.
- WooCommerce activo: versión `10.7.0`.
- Entorno: local, PHP `8.3.30`, WP debug desactivado.
- Tabla `m22w_authgate_log` existe.
- WooCommerce My account page ID `319` configurada con shortcode presente.
- Ajustes WooCommerce de cuenta: `woocommerce_enable_myaccount_registration = no`, `woocommerce_enable_signup_and_login_from_checkout = no`, `woocommerce_registration_generate_password = yes`.

## Hecho

- MCP WooCommerce verificado en lectura.
- Se identificó que el siguiente descarte debe centrarse en AJAX `authgate_register` y ajustes de registro.

## Pendiente

- Confirmar HTTP status, payload, respuesta JSON y logs al enviar registro.
- Confirmar `users_can_register` de WordPress si se puede consultar por MCP/diagnóstico seguro.
- Revisar si falla `users_can_register`, nonce, GDPR, antibot, WooCommerce o `wp_create_user`.

## No volver a investigar

- El error reportado no es de reset password; es de registro.
- WooCommerce sí está activo.
- MCP WooCommerce sí funciona ahora.

## Riesgos o bloqueos

- Sin respuesta AJAX/log no se puede confirmar causa raíz.
- Cambiar settings de registro alteraría estado; requiere permiso explícito.

## Próximo paso recomendado

- Capturar la petición `admin-ajax.php` con `action=authgate_register` en Network y contrastarla con `ajax_register`.

---

## Incidencia 2026-06-17 — Fatal en admin tras Fase 1 22MW-BACK

## Error

- Fatal: `Undefined constant "AUTHGATE_URL"` en `includes/class-auth-settings.php`.

## Hechos confirmados

- `authgate.php` no define `AUTHGATE_URL` ni `AUTHGATE_VERSION`.
- Fase 1 encoló `authgate-back.css` y `authgate-back.js` usando esas constantes inexistentes.

## Hecho

- Corregido el encolado para usar `$asset_url = plugin_dir_url(dirname(__FILE__))` y versión local `1.1.1`.
- Validado `php -l includes/class-auth-settings.php`: ok.
- Validado `git diff --check`: ok.

## No volver a investigar

- AuthGate 1.1.1 no tiene constantes globales `AUTHGATE_URL` / `AUTHGATE_VERSION`; no usarlas sin definirlas antes.
