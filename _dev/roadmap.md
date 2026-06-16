# Roadmap

## Urgente

- Ningún punto urgente confirmado tras QA P1 OK.

## Recomendado

- Ejecutar auditoría de seguridad dirigida sobre AJAX, nonces, capabilities, sanitización, escaping, SQL y uninstall.
- Revisar que `_dev/` y `.kilo/` quedan excluidos de cualquier ZIP/deploy público.
- Preparar release estable solo con autorización explícita.

## Futuro

- Definir soporte explícito para proxy/CDN si se necesita confiar en cabeceras de IP.
- Consolidar `1.1.0.1` en una versión pública de tres números cuando se prepare release estable.
- Revisar documentación pública y changelog antes de release.

## Bloqueado

- Release estable bloqueada hasta permiso explícito.
- Creación de usuarios de prueba bloqueada hasta permiso explícito si se considera prueba que altera datos.

## Descartado

- No hacer refactor general dentro de esta P1.
- No mezclar release estable, tag o deploy con QA pendiente.
