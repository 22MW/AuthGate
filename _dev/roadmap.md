# Roadmap

## Urgente

- Seguimiento post-release `1.2.0`.

## Recomendado

- Validar updater/instalación desde una versión anterior con el ZIP `1.2.0` en entorno seguro.
- Revisar documentación pública solo si aparecen dudas de soporte tras la publicación.
- Mantener la landing comercial interna en `_dev/comercial/` hasta decidir publicación externa.

## Futuro

### Backend/admin visual 22MW

- Fase 4 futura: carga dinámica por AJAX admin solo si aporta valor real.
- Extender el patrón `22mw-back` a otros plugins después de validar AuthGate como piloto.
- Revisar site-level multisite si se decide dar soporte visual específico en una fase posterior.

### Comercial

- Añadir pantallazos/GIFs reales a `_dev/comercial/`.
- Decidir si la landing comercial pasa a `https://22mw.online/`.
- Validar demanda antes de fijar pricing definitivo.

### Técnico

- Definir soporte explícito para proxy/CDN si se necesita confiar en cabeceras de IP.

## Bloqueado

- Ningún bloqueo de release abierto.
- Creación de usuarios de prueba bloqueada hasta permiso explícito si se considera prueba que altera datos.

## Descartado

- No hacer refactor general dentro de estas mejoras.
- No mezclar cambios comerciales internos con el ZIP/release público.
- No incluir `_dev/`, `.kilo/`, secretos, logs ni backups en ZIP/release.
- No avanzar con AJAX admin como “ya que estamos”.

## Cerrado

- Release `1.1.1` publicada.
- Release `1.2.0` publicada.
- Backend/admin visual 22MW Fases 1, 2, 2B y 3 consolidadas en `1.2.0`.
- Base reusable `22mw-back` sincronizada dentro de AuthGate.
- Duplicación de estilos base eliminada: base común en `22mw-back.*`, overrides específicos en `authgate-back.*`.
- QA manual confirmado por el usuario antes de release `1.2.0`.
