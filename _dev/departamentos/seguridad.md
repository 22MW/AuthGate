# Seguridad

## Última actualización

2026-06-10

## Resumen humano

Seguridad requiere revisión dirigida. Hay AJAX, nonces, rate limit, blacklist, logs SQL, updater externo y uninstall.

## Descubierto

- AJAX frontend usa nonces según revisión previa.
- Rate limit por IP existe.
- IP por defecto fue endurecida con `REMOTE_ADDR`, proxy por filtro.
- Updater depende de GitHub Releases.

## Hecho

- Riesgos iniciales identificados.

## Pendiente

- Auditar AJAX, sanitización, escaping, capabilities, SQL, updater y uninstall.
- Revisar impacto de blacklist/rate limit con proxy/CDN.

## No volver a investigar

- No guardar secretos en `_dev/`.

## Riesgos o bloqueos

- MCP no configurado limita verificación real del entorno.
- Registro fallando impide validación completa del flujo seguro.

## Próximo paso recomendado

- Revisión de seguridad específica después de confirmar causa del registro.
