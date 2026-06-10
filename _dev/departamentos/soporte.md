# Soporte

## Última actualización

2026-06-10

## Resumen humano

Soporte debe guiar al usuario para reproducir y capturar el error de registro sin tocar configuración real innecesariamente.

## Descubierto

- Usuario está en local.
- WooCommerce está activo y confirmado por MCP.
- Error visible: `Se ha producido un error. Inténtalo de nuevo.`.
- WooCommerce tiene desactivado el registro en Mi cuenta y durante checkout.
- My Account existe y tiene shortcode.

## Hecho

- MCP WooCommerce confirmado operativo.
- Se pidió revisar Network y logs para obtener respuesta de `authgate_register`.

## Pendiente

- Guiar al usuario para capturar payload/respuesta AJAX.
- Confirmar si el registro nativo WordPress `users_can_register` está activado.
- Explicar que no se cambiarán settings sin permiso.

## No volver a investigar

- No asumir causa del error sin respuesta AJAX/log.
- WooCommerce está activo en este entorno.

## Riesgos o bloqueos

- Mensaje genérico no identifica causa por sí solo.
- Cambios de settings pueden alterar comportamiento del sitio.

## Próximo paso recomendado

- Pedir captura de Network de `admin-ajax.php?action=authgate_register` y confirmar si `users_can_register` está activo.
