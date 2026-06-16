# Roadmap

## Urgente

- Bloque D pendiente: CSS propio opcional.

## Recomendado

### Bloque A — bajo riesgo

- Hecho y pusheado en dev `1.1.0.3`.
- QA OK confirmado por el usuario.

### Bloque B — riesgo medio

- Hecho y pusheado en dev `1.1.0.4`.
- Ajuste QA hecho y pusheado en dev `1.1.0.5`.
- QA OK confirmado por el usuario.

### Bloque C — riesgo medio

- Hecho y listo para push en dev `1.1.0.6`.
- QA OK confirmado por el usuario.

### Bloque D — riesgo alto / ejecutar al final

- Añadir CSS propio opcional desde admin.
- Checkbox para activar CSS propio.
- Editor tipo CodeMirror.
- Sanitización del CSS antes de guardar/renderizar.
- Dos presets copiables: blanco y oscuro.
- Preset blanco como referencia inicial.

### Antes de release

- Ejecutar auditoría de seguridad dirigida sobre AJAX, nonces, capabilities, sanitización, escaping, SQL, updater y uninstall.
- Revisar que `_dev/` y `.kilo/` quedan excluidos de cualquier ZIP/deploy público.
- Preparar release estable solo con autorización explícita.

## Futuro

- Definir soporte explícito para proxy/CDN si se necesita confiar en cabeceras de IP.
- Consolidar cambios dev en una versión pública de tres números cuando se prepare release estable.
- Revisar documentación pública y changelog antes de release.

## Bloqueado

- Release estable bloqueada hasta permiso explícito.
- Creación de usuarios de prueba bloqueada hasta permiso explícito si se considera prueba que altera datos.

## Descartado

- No hacer refactor general dentro de estas mejoras pre-release.
- No mezclar release estable, tag o deploy con bloques pendientes.
