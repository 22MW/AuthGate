# Roadmap

## Urgente

- QA manual del Bloque A.

## Recomendado

### Bloque A — bajo riesgo

- Hecho local: ocultar checkbox newsletter si Mail Mint no está activo.
- Hecho local: en modo inline, añadir enlace “Ir a la página de inicio” hacia `home_url()`.
- Pendiente: QA manual.
- Si QA OK: bump dev `1.1.0.3`, release notes, commit y push.

### Bloque B — riesgo medio

- Añadir campo WYSIWYG bajo el logo/cabecera visual del formulario.
- Mostrarlo solo en modo inline.
- Mostrarlo en login, register y combined.
- Si está vacío, no renderizar nada.

### Bloque C — riesgo medio

- Separar “Textos del formulario” a un tab propio.
- Cada tab debe guardar por separado.

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
