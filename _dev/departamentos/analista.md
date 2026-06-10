# Analista

## Última actualización

2026-06-10

## Resumen humano

AuthGate cubre autenticación frontend: login, registro, recuperación/reset, protección de páginas y compatibilidad opcional con WooCommerce.

## Descubierto

- El usuario está probando registro en local con WooCommerce activo.
- El mensaje actual es genérico y no permite saber la causa funcional sin payload/log.

## Hecho

- Alcance funcional inicial identificado.

## Pendiente

- Definir criterios de aceptación para registro, login, reset y protección.
- Confirmar comportamiento esperado con WooCommerce activo.

## No volver a investigar

- El plugin no requiere WooCommerce para existir, pero sí tiene integración condicional.

## Riesgos o bloqueos

- Registro frontend fallando bloquea validación funcional básica.

## Próximo paso recomendado

- Pedir evidencia de la petición AJAX `authgate_register` y resultado esperado del flujo.
