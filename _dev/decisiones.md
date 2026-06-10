# Decisiones

## Última actualización

2026-06-10

## Decisiones confirmadas

- AuthGate se trabaja desde `app/public/wp-content/plugins/AuthGate/`.
- La rama de trabajo actual es `mishaAuthDev`.
- El repo del plugin es independiente del workspace Kilo.
- No se hará commit, push, tag, release ni deploy sin autorización explícita.
- `_dev/` se usa como memoria operativa interna y no debe ir a ZIP/deploy público.
- El P0 aplicado fue de alcance mínimo; no autoriza refactor ni funcionalidades nuevas.
- MCP WooCommerce está operativo para consultas de diagnóstico.

## Pendientes de decisión

- Si se permite consultar o cambiar ajustes WordPress no cubiertos por MCP WooCommerce.
- Si se permite probar el registro creando usuarios de prueba en local.
- Si el sitio debe confiar en cabeceras de proxy/CDN para IP real.
- Qué entra en una futura release después de validar el registro.

## No reabrir sin motivo

- No usar el repo raíz del workspace para versionar AuthGate.
- No trabajar directamente en `main` para desarrollo diario.
- No publicar `_dev/` en release pública.
- No modificar settings WooCommerce/WordPress sin permiso explícito.
