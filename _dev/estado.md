# Estado del plugin

## Última actualización

2026-06-17

## Resumen humano

AuthGate publicó release production `1.1.1` en GitHub. La P1, popup `label` y Bloques A/B/C/D quedaron consolidados.

## Estado general

Release `1.1.1` publicada en GitHub. Rama `main` actualizada y tag remoto `v1.1.1` existente.

## Hecho

- Plugin localizado en `app/public/wp-content/plugins/AuthGate/`.
- Archivo principal confirmado: `authgate.php`.
- Versión estable en preparación: `1.1.1`.
- Rama actual confirmada: `mishaAuthDev`.
- P1 implementada y QA OK.
- Mejora popup pusheada con commit `58a9622 feat: add popup shortcode labels`.
- Bloque A: checkbox newsletter se oculta si Mail Mint no está activo.
- Bloque A: enlace “Ir a la página de inicio” en render inline.
- QA Bloque A confirmado por el usuario.
- Bloque A pusheado en commit `bdd7d6e`.
- Bloque B: WYSIWYG “Texto bajo el logo” añadido para render inline.
- QA Bloque B: texto centrado y más controles del editor aplicados y pusheados.
- Bloque B QA OK confirmado por el usuario.
- Bloque C: textos movidos a pestaña propia con guardado independiente.
- Bloque C: texto “Ir a la página de inicio” añadido como `link_to_home` configurable.
- QA Bloque C OK confirmado por el usuario.
- Bloque D: CSS propio opcional implementado localmente.
- Bloque D: CSS propio activo por defecto y preset blanco cargado como fallback.
- Bloque D: card de página protegida incluye logo, título y descripción dentro del mismo bloque visual.
- Bloque D: logo de página protegida ajustado para mantener proporción y verse entero.
- Bloque D: cambios visuales del preset blanco replicados en preset oscuro.
- Bloque D: ajustes manuales de tamaño aplicados también al preset blanco.
- Bloque D: presets blanco y oscuro sincronizados; solo cambian colores.
- QA Bloque D OK confirmado por el usuario.
- `_dev/deploy-release.sh` revisado y ajustado para excluir `_dev/` y `.kilo/`.
- GitHub release publicada: `https://github.com/22MW/AuthGate/releases/tag/v1.1.1`.
- ZIP `authgate.zip` subido como asset de release.
- Rama `main` actualizada por el script.

## En curso

- Seguimiento post-release.
- Fase comercial inicial: posicionamiento, anuncios y landing mínima.
- Landing comercial de trabajo creada en `_dev/comercial/landing-authgate.html`.
- Web de desarrollo comercial definida: `https://22mw.online/`.
- Landing definitiva de trabajo en `_dev/comercial/authgate-landing.html` sigue en revisión visual.
- Fase 1 implementada localmente: backend/admin AuthGate con shell visual `22mw-back`, menú horizontal y dark/light por `localStorage`.

## Bloqueado

- No hay bloqueo de release abierto.

## Próximo paso recomendado

- Validar visualmente Fase 1 de `22mw-back` en Ajustes > AuthGate antes de avanzar a componentes/formularios.

## No volver a investigar

- Ruta real del plugin: `app/public/wp-content/plugins/AuthGate/`.
- Rama de trabajo: `mishaAuthDev`.
- Versión estable preparada: `1.1.1`.
- Release production autorizado por el usuario el 2026-06-17.
- Release GitHub `v1.1.1` publicada correctamente.
- Bloque D solo carga CSS si está activado.
- Bloque C separa guardado de textos del guardado general.
- QA Bloque A OK confirmado por el usuario.
- Bloque B no debe aparecer en popup.
- QA Bloque B OK confirmado por el usuario.
- Atributo elegido para texto de popup: `label`.
- Bloque A: Mail Mint condicional + enlace inicio inline.
- No hacer release estable, tag ni deploy sin autorización explícita.
- Script de release interno movido a `_dev/deploy-release.sh`.
- Token local permitido en `_dev/.env`; nunca incluirlo en ZIP/release.

- El rediseño visual 22MW acordado ahora aplica al backend/admin, no al frontend público.
