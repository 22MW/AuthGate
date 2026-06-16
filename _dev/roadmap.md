# Roadmap

## Urgente

- QA manual del Bloque B.

## Recomendado

### Bloque A — bajo riesgo

- Hecho y pusheado en dev `1.1.0.3`.
- QA OK confirmado por el usuario.

### Bloque B — riesgo medio

- Hecho local: añadido campo WYSIWYG “Texto bajo el logo”.
- Hecho local: render solo en modo inline de login, register y combined.
- Hecho local: si está vacío no renderiza nada.
- Pendiente: QA manual.

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
