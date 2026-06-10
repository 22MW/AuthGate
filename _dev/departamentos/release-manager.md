# Release Manager

## Última actualización

2026-06-10

## Resumen humano

Rama dev corregida según regla de release: el push a dev debe llevar versión de cuatro números y entrada operativa. Se preparó `1.1.0.1` como versión dev.

## Descubierto

- Versión estable base: `1.1.0`.
- Rama actual: `mishaAuthDev`.
- Último push dev previo no llevaba bump dev; corregido con `1.1.0.1`.
- `_dev/` no debe incluirse en ZIP/deploy público.

## Hecho

- Cabecera del plugin actualizada a `1.1.0.1`.
- `_dev/release-notes.md` actualizado con entrada `Dev 1.1.0.1`.
- `Stable tag` de `readme.txt` se mantiene en `1.1.0` porque no es release estable.

## Pendiente

- Validar QA manual antes de preparar release estable.
- Consolidar dev en versión pública de tres números cuando se prepare release.
- Excluir `_dev/` y `.kilo/` del ZIP público.

## No volver a investigar

- Las versiones dev usan formato `MAJOR.MINOR.PATCH.DEV`.
- No cambiar `Stable tag` en rama dev salvo release estable aprobada.

## Riesgos o bloqueos

- QA manual de P1 pendiente.
- No hay ZIP limpio preparado.

## Próximo paso recomendado

- Commit/push del bump dev `1.1.0.1` si se confirma publicar la corrección en rama dev.
