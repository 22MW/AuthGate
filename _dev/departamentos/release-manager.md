# Release Manager

## Última actualización

2026-06-16

## Resumen humano

Release production `1.1.1` publicado en GitHub. Se consolidaron los devs `1.1.0.1` a `1.1.0.7` mediante `deploy-release.sh`.

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
- GitHub release publicada: `https://github.com/22MW/AuthGate/releases/tag/v1.1.1`.
- ZIP `authgate.zip` subido como asset.
- Rama `main` actualizada.

## Pendiente

- Seguimiento post-release si aparece incidencia.

## No volver a investigar

- `_dev/` y `.kilo/` no deben ir en ZIP/release pública.
- `deploy-release.sh` requiere working tree limpio antes de ejecutarse.
- `deploy-release.sh` requiere `GITHUB_TOKEN` disponible en `.env.local` o entorno.

## Riesgos o bloqueos

- El script terminó con aviso al empujar tag porque GitHub ya había creado el tag remoto al crear la release.
- El resultado útil quedó publicado: release, ZIP, `main` y tag remoto existen.

## Próximo paso recomendado

- No repetir release `v1.1.1`; usar una versión nueva si hay hotfix.
