# Desarrollador

## Última actualización

2026-06-10

## Resumen humano

P1 implementada con cambios mínimos: gestión del registro nativo WP desde AuthGate, opción WooCommerce de enlace de contraseña y ocultación frontend del registro cuando no está permitido.

## Descubierto

- Archivos modificados por P1: `includes/class-auth-settings.php`, `includes/class-auth-forms.php`, `includes/templates/form-combined.php`, `includes/templates/form-register.php`, `integrations/woocommerce/class-wc-integration.php`.
- No hizo falta tocar `assets/js/auth-forms.js`.

## Hecho

- Añadido helper `AuthGate_Settings::registration_enabled()`.
- Añadido control backend para `users_can_register`.
- Añadido control backend para `woocommerce_registration_generate_password` cuando WooCommerce está activo.
- Ocultado registro frontend si `users_can_register` está desactivado.
- Bloqueado AJAX de registro si el registro está desactivado.
- Ajustada integración WooCommerce para no reactivar registro si WordPress/AuthGate lo desactiva.

## Pendiente

- QA manual de backend/frontend.
- Revisión visual del formulario combinado cuando registro está desactivado.
- Confirmar email WooCommerce de configuración de contraseña en local si se permite crear usuario de prueba.

## No volver a investigar

- No tocar JS salvo que QA detecte enlace/tab roto.

## Riesgos o bloqueos

- Cambiar `users_can_register` afecta al registro global del sitio.
- La opción WooCommerce de contraseña afecta a registros WooCommerce nativos y AuthGate.

## Próximo paso recomendado

- Ejecutar QA manual de P1 antes de commit/release.
