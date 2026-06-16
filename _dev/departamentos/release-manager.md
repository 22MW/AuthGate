# Release Manager

## Última actualización

2026-06-16

## Resumen humano

Release production `1.1.1` autorizado por el usuario. Se consolidan los devs `1.1.0.1` a `1.1.0.7` y se usará `deploy-release.sh` para GitHub release, ZIP, merge a `main` y tag.

## Descubierto

- Script real de release: `deploy-release.sh`.
- El script publica GitHub release, sube `authgate.zip`, mergea `release` en `main` y crea tag.
- El script necesitaba excluir `_dev/` y `.kilo/` antes de crear rama/ZIP de release.

## Hecho

- Versión estable preparada: `1.1.1`.
- `readme.txt` preparado con `Stable tag: 1.1.1`.
- `CHANGELOG.md` preparado con entrada `1.1.1`.
- `deploy-release.sh` ajustado para excluir `_dev/` y `.kilo/`.
- Bloques A/B/C/D consolidados para release.

## Pendiente

- Validar PHP y diff.
- Commit y push de preparación en `mishaAuthDev`.
- Ejecutar `deploy-release.sh`.
- Confirmar URL de GitHub release.

## No volver a investigar

- `_dev/` y `.kilo/` no deben ir en ZIP/release pública.
- `deploy-release.sh` requiere working tree limpio antes de ejecutarse.
- `deploy-release.sh` requiere `GITHUB_TOKEN` disponible en `.env.local` o entorno.

## Riesgos o bloqueos

- Si falta `GITHUB_TOKEN`, el script se detendrá.
- Si el tag `v1.1.1` ya existe, el script se detendrá.

## Próximo paso recomendado

- Validar, commitear preparación y ejecutar release production `1.1.1`.
