# Estado del plugin

## Última actualización

2026-06-17

## Resumen humano

AuthGate prepara release estable `1.2.1` con QA multisite confirmado por el usuario. MS0–MS3 quedan consolidados; MS4 permanece fuera de alcance.

## Estado general

Release `1.2.1` en preparación desde `mishaAuthDev`. QA multisite confirmado por el usuario; pendiente publicación GitHub/ZIP.

## Hecho

- Plugin localizado en `app/public/wp-content/plugins/AuthGate/`.
- Archivo principal confirmado: `authgate.php`.
- Rama de trabajo: `mishaAuthDev`.
- Release `1.1.1` publicada previamente en GitHub.
- Fase 1 `22mw-back`: shell admin, header, menú horizontal y dark/light.
- Fase 2/Fase 2B: componentes visuales, switches, tablas, notices y selector buscable de Exclusiones.
- Fase 3: submenú vertical interno en General por anclas/JS, sin AJAX ni cambios de guardado.
- AuthGate sincronizado con assets base de la skill `22mw-back`: `assets/css/22mw-back.css` y `assets/js/22mw-back.js`.
- AuthGate normalizado con opción A: markup backend usa `.mw22-back*`; `authgate-back.css/js` quedan para piezas específicas.
- Corrección visual post-QA: bloques con fondo blanco en oscuro neutralizados por CSS scoped a `.authgate-back`.
- QA manual confirmado por el usuario para backend/admin, Textos, Estilo, General, guardados y comprobaciones frontend principales.
- Release GitHub publicada: `https://github.com/22MW/AuthGate/releases/tag/v1.2.0`.
- ZIP publicado: `https://github.com/22MW/AuthGate/releases/download/v1.2.0/authgate.zip`.
- `_dev/` excluido de rama `release` y ZIP público por script interno.
- Trabajo comercial interno subido a `mishaAuthDev` dentro de `_dev/comercial/`.
- MS0: mapa de scopes multisite aplicado.
- MS1: registro por sitio respetando política de red aplicado.
- MS2: textos por sitio con fallback sitio → red → default aplicado.
- MS3: CSS por sitio con modo heredar, sobrescribir o desactivar aplicado.
- QA multisite MS1/MS2/MS3 confirmado por el usuario antes de `1.2.1`.

## En curso

- Preparación de release estable `1.2.1`.

## Bloqueado

- No hay bloqueo técnico confirmado para `1.2.1`.

## Próximo paso recomendado

- Publicar release estable `1.2.1` y verificar ZIP/GitHub release.

## No volver a investigar

- Ruta real del plugin: `app/public/wp-content/plugins/AuthGate/`.
- Rama de trabajo: `mishaAuthDev`.
- Release GitHub `v1.1.1` publicada correctamente.
- Release GitHub `v1.2.0` publicada correctamente.
- `_dev/` puede estar en `mishaAuthDev`, pero nunca debe entrar en rama `release`, `main` pública limpia ni ZIP.
- `_dev/comercial/` es trabajo interno y no debe aparecer en release público.
- Script de release interno: `_dev/deploy-release.sh`.
- Token local permitido en `_dev/.env`; nunca incluirlo en ZIP/release.
- El rediseño visual 22MW aplica al backend/admin, no al frontend público.
- Fase 4 AJAX admin queda aplazada por riesgo y no debe hacerse de paso.
