# Roadmap

## Urgente

- Seguimiento post-release `1.1.1`.

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

- Release GitHub `v1.1.1` publicada.
- ZIP `authgate.zip` generado y subido.
- Rama `main` actualizada.

## Futuro

- Definir soporte explícito para proxy/CDN si se necesita confiar en cabeceras de IP.
- Revisar documentación pública tras release si se detectan dudas de soporte.
- Revisar documentación pública y changelog antes de release.

### Backend/admin visual 22MW

- Fase 1 validada, commiteada y pusheada: shell admin 22MW, header, menú horizontal tipo landing y dark/light por `localStorage`.
- Fase 1 excluye frontend público, guardados, AJAX nuevo, submenús internos, switches avanzados y site-level multisite.
- Fase 2 validada, commiteada y pusheada: componentes visuales, formularios, switches, botones, tablas y notices con estilo 22MW; sin cambiar lógica funcional.
- Fase 3 preparada: submenús verticales internos por pantalla, sin AJAX y con navegación local por anclas/JS.
- Fase 4 futura: carga dinámica por AJAX admin si aporta.

- Fase 2B incluida en commit/push: selector buscable de páginas excluidas con chips removibles; mantiene guardado actual `excluded_pages[]`.

- Fase 3 implementada localmente en General: submenú vertical interno por anclas/JS; pendiente validar antes de extender a otras pantallas.

## Bloqueado

- Ningún bloqueo de release abierto.
- Creación de usuarios de prueba bloqueada hasta permiso explícito si se considera prueba que altera datos.

## Descartado

- No hacer refactor general dentro de estas mejoras pre-release.
- No mezclar release estable, tag o deploy con bloques pendientes.
