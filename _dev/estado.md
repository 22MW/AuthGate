# Estado del plugin

## Última actualización

2026-06-10

## Resumen humano

AuthGate está instalado y activo en el WordPress local `https://plugins.local`. WooCommerce también está activo. MCP WooCommerce responde en modo lectura. Se implementó P1 mínima: AuthGate permite gestionar el registro nativo de WordPress y la opción WooCommerce de enviar enlace de configuración de contraseña; si el registro está desactivado, la parte de registro se oculta en frontend.

## Estado general

Parcial: P1 implementada en código, pendiente de QA manual.

## Hecho

- Plugin localizado en `app/public/wp-content/plugins/AuthGate/`.
- Archivo principal confirmado: `authgate.php`.
- Versión detectada: `1.1.0.1`.
- Text domain detectado: `authgate`.
- Repo Git propio confirmado.
- Rama actual confirmada: `mishaAuthDev`.
- MCP WooCommerce operativo en lectura.
- AuthGate activo en WordPress: `AuthGate/authgate.php` versión `1.1.0` antes del bump local dev.
- WooCommerce activo: `woocommerce/woocommerce.php` versión `10.7.0`.
- Entorno local confirmado: `wp_environment_type: local`.
- P1 aplicada: control backend para `users_can_register`.
- P1 aplicada: control backend para `woocommerce_registration_generate_password` si WooCommerce está activo.
- P1 aplicada: frontend oculta registro cuando WordPress no permite registro.
- P1 aplicada: AJAX `authgate_register` queda bloqueado si registro está desactivado.

## En curso

- QA manual de P1: activar/desactivar registro y verificar frontend.
- Validar comportamiento WooCommerce de contraseña/enlace.

## Bloqueado

- Ningún bloqueo técnico confirmado tras la implementación.

## Próximo paso recomendado

- Probar desde backend AuthGate: desactivar registro, guardar, comprobar que no aparece registro en `[authgate_auth]` ni `[authgate_register]`.
- Activar registro, guardar, comprobar que el formulario vuelve.
- Con WooCommerce activo, alternar “Enviar enlace de configuración de contraseña” y verificar campo contraseña visible/oculto.

## No volver a investigar

- Ruta real del plugin: `app/public/wp-content/plugins/AuthGate/`.
- Archivo principal: `authgate.php`.
- Versión actual declarada en código: `1.1.0.1`.
- Rama de trabajo: `mishaAuthDev`.
- Sitio local confirmado por MCP: `https://plugins.local`.
- WooCommerce activo confirmado por MCP: `10.7.0`.
- AuthGate activo confirmado por MCP: `1.1.0`.
- PHP entorno MCP: `8.3.30`.
- Prefijo BD: `m22w_`.
- No hacer commit, push, tag, release ni deploy sin autorización explícita.
