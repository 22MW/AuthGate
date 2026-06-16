# Release Manager

## Última actualización

2026-06-10

## Resumen humano

Versión dev `1.1.0.1` ya aplicada, commiteada y pusheada a `mishaAuthDev`. No hay release estable ni ZIP público preparado.

## Descubierto

- Versión estable base: `1.1.0`.
- Rama actual: `mishaAuthDev`.
- Commit dev actual: `87216ff release: bump dev version to 1.1.0.1`.
- `_dev/` no debe incluirse en ZIP/deploy público.
- `.kilo/` queda sin trackear y no debe incluirse en release pública salvo decisión explícita.

## Hecho

- Cabecera del plugin actualizada a `1.1.0.1`.
- `_dev/release-notes.md` actualizado con entrada `Dev 1.1.0.1`.
- `Stable tag` de `readme.txt` se mantiene en `1.1.0` porque no es release estable.
- Push dev realizado a `origin/mishaAuthDev`.

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

- Esperar QA manual antes de proponer release estable.
