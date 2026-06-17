# Jefe de Proyecto

## Última actualización

2026-06-17

## Resumen humano

AuthGate cerró y publicó release `1.2.0`. El bloque backend/admin 22MW quedó consolidado, con base `22mw-back` reutilizable y overrides específicos de AuthGate. El trabajo comercial permanece como interno en `_dev/` y fuera del release público.

## Descubierto

- AuthGate funciona como piloto del sistema visual `22mw-back`.
- El patrón correcto es base común `22mw-back.*` + overrides específicos por plugin.
- `_dev/` puede vivir en `mishaAuthDev`, pero debe excluirse siempre de release/ZIP.

## Hecho

- Release `1.1.1` publicada previamente.
- Fases 1, 2, 2B y 3 del backend/admin visual completadas.
- Opción A aplicada: markup admin usa clases/data attributes `mw22-back`.
- Duplicación de estilos eliminada entre base `22mw-back` y AuthGate.
- Fondo blanco en Textos/Estilo modo oscuro corregido.
- QA manual confirmado por el usuario.
- Release `1.2.0` publicada en GitHub.
- ZIP `authgate.zip` publicado para `v1.2.0`.
- `visual.html`, `estado.md` y `roadmap.md` consolidados post-release.

## Pendiente

- Seguimiento post-release `1.2.0`.
- Validar updater desde versión anterior si se quiere comprobar flujo real.
- Mantener landing/comercial como trabajo interno hasta decisión de publicación.

## No volver a investigar

- No usar `plan-funcionalidad` para rediseños backend/admin; usar `22mw-back` o `plan-backend-plugin`.
- Fase 4 AJAX admin queda futura y no debe ejecutarse sin decisión nueva.
- `_dev/` no debe entrar en rama `release` ni ZIP.
- Comercial interno en `_dev/comercial/` no forma parte del plugin público.

## Riesgos o bloqueos

- Updater no validado en esta tarea desde instalación real anterior.
- AJAX admin tendría riesgo medio-alto por WP Editor, CodeMirror y guardados existentes.

## Próximo paso recomendado

- Si se continúa técnicamente: validar updater en entorno seguro.
- Si se continúa comercialmente: preparar publicación externa de landing desde `_dev/comercial/`, sin mezclar con release del plugin.
