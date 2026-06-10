# Arquitecto

## Última actualización

2026-06-10

## Resumen humano

Arquitectura simple basada en archivo principal, clases en `includes/`, plantillas internas, assets y una integración WooCommerce separada.

## Descubierto

- Bootstrap principal en `authgate.php`.
- Formularios y AJAX en `includes/class-auth-forms.php`.
- Ajustes en `includes/class-auth-settings.php`.
- Updater en `includes/class-github-updater.php`.
- WooCommerce en `integrations/woocommerce/class-wc-integration.php`.

## Hecho

- Mapa inicial de responsabilidades identificado.

## Pendiente

- Revisar filtros/hook points relevantes antes de nuevos cambios.
- Validar que la integración WooCommerce no duplica creación de usuarios.

## No volver a investigar

- El plugin usa estructura PHP clásica, sin build Node confirmado.

## Riesgos o bloqueos

- Cambios de IP/proxy pueden requerir diseño configurable si hay CDN.

## Próximo paso recomendado

- Revisar flujo técnico de `ajax_register` y filtro `authgate_create_user`.
