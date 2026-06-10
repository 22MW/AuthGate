# Jefe de Proyecto

## Última actualización

2026-06-10

## Resumen humano

Memoria operativa inicial creada para coordinar AuthGate sin mezclar diagnóstico, desarrollo, QA, seguridad y release.

## Descubierto

- Plugin en `app/public/wp-content/plugins/AuthGate/`.
- Rama actual: `mishaAuthDev`.
- Hay cambios locales sin commit.
- Incidencia actual: registro frontend devuelve error genérico.

## Hecho

- Estructura `_dev/` inicial definida.
- Estado, roadmap, decisiones, release notes y vista visual iniciales creados.

## Pendiente

- Diagnóstico específico del registro.
- Configurar MCP si se quiere diagnóstico de WordPress/WooCommerce real.
- Delegar seguridad y QA cuando el registro quede estabilizado.

## No volver a investigar

- No trabajar AuthGate desde el repo raíz del workspace.
- No hacer commit/push/release sin autorización explícita.

## Riesgos o bloqueos

- Registro no funciona según usuario.
- MCP WooCommerce no conectado.

## Próximo paso recomendado

- Delegar diagnóstico del bug de registro al Debugger Plugin.
