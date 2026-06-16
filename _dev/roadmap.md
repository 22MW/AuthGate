# Roadmap

## Urgente

- Release production `1.1.1` en preparación.

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

- Hecho local: CSS propio opcional desde admin.
- Hecho local: checkbox de activación.
- Hecho local: editor CodeMirror.
- Hecho local: sanitización conservadora antes de guardar.
- Hecho local: presets copiables blanco y oscuro.
- Hecho local: preset blanco activo/cargado por defecto.
- QA OK confirmado por el usuario.

### Antes de release

- Validar PHP y diff.
- Revisar que `_dev/` y `.kilo/` quedan excluidos de cualquier ZIP/deploy público.
- Ejecutar `deploy-release.sh` con autorización explícita.

## Futuro

- Definir soporte explícito para proxy/CDN si se necesita confiar en cabeceras de IP.
- Revisar documentación pública tras release si se detectan dudas de soporte.
- Revisar documentación pública y changelog antes de release.

## Bloqueado

- Release production autorizado para `1.1.1`.
- Creación de usuarios de prueba bloqueada hasta permiso explícito si se considera prueba que altera datos.

## Descartado

- No hacer refactor general dentro de estas mejoras pre-release.
- No mezclar release estable, tag o deploy con bloques pendientes.
