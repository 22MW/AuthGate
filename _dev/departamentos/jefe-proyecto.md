# Jefe de Proyecto

## Última actualización

2026-06-16

## Resumen humano

P1 implementada, versionada como dev `1.1.0.1`, pusheada a `mishaAuthDev` y QA confirmada por el usuario. Queda preparar release estable solo si el usuario lo pide.

## Descubierto

- El fallo de registro inicial era por `users_can_register` desactivado en WordPress.
- AuthGate ahora gestiona esa opción desde su backend.
- AuthGate también gestiona la opción WooCommerce de enviar enlace de configuración de contraseña.

## Hecho

- P1 implementada.
- Bump dev `1.1.0.1` aplicado.
- Push dev realizado.
- QA P1 confirmado por el usuario.
- `CHANGELOG.md` actualizado con comentarios de `1.1.0.1`.
- Memoria operativa actualizada.

## Pendiente

- Preparar release estable solo con autorización explícita.

## No volver a investigar

- La P1 ya está en `mishaAuthDev` con commit `87216ff`.
- QA P1 OK confirmado por el usuario.
- No hacer release estable sin permiso explícito.

## Riesgos o bloqueos

- ZIP/release debe excluir `_dev/` y `.kilo/`.

## Próximo paso recomendado

- Preparar release estable si el usuario lo solicita explícitamente.
