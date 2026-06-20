# Decisiones

## Última actualización

2026-06-19

## Decisiones confirmadas

- AuthGate se trabaja desde `app/public/wp-content/plugins/AuthGate/`.
- La rama de trabajo actual es `mishaAuthDev`.
- El repo del plugin es independiente del workspace Kilo.
- `_dev/` se usa como memoria operativa interna y no debe ir a ZIP/deploy público.
- Si el registro está desactivado, AuthGate oculta completamente la parte de registro en frontend.
- Si WooCommerce está activo, AuthGate duplica en su backend la opción `woocommerce_registration_generate_password`.
- QA P1 fue confirmado como OK por el usuario.
- El atributo elegido para texto de botón popup es `label`.
- En roadmap pre-release, el campo WYSIWYG bajo logo se mostrará en todas las variantes inline: login, register y combined.
- Bloque B queda limitado a modo inline; no se muestra en popup.
- Bloque B queda cerrado tras QA: texto centrado y toolbar WYSIWYG ampliada.
- El tab “Textos” guardará separado por tab, no con un único formulario global.
- Bloque C usa pestaña “Textos” independiente y no guarda textos desde la pestaña General.
- El texto del enlace a inicio se configura con la clave `link_to_home`.
- QA Bloque C confirmado por el usuario antes de commit/push.
- El CSS propio se guardará con sanitización.
- El editor CSS debe ser tipo CodeMirror.
- El CSS propio tendrá checkbox de activación.
- Los presets CSS serán blanco y oscuro, como texto copiable; blanco será referencia inicial.
- Bloque D se implementa en pestaña propia “CSS propio” con guardado independiente.
- El preset blanco es el CSS inicial por defecto y queda activado salvo que el usuario lo desactive.
- Release production `1.1.1` autorizado explícitamente por el usuario.
- Release production `1.2.0` publicada correctamente.
- `deploy-release.sh` se usa desde `_dev/deploy-release.sh` para publicar GitHub release, ZIP, merge a `main` y tag.
- El sistema visual 22MW acordado aplica a backend/admin, no frontend público.

## Decisiones multisite confirmadas

- En multisite, la política global de red manda sobre el registro de usuarios.
- Si la red bloquea registro de usuarios, ningún sitio debe poder activarlo desde AuthGate.
- El registro queda como política global de red desde AuthGate; los sitios no muestran control local de registro.
- `users_can_register` no se usará como control visible por sitio en el flujo multisite actual.
- `woocommerce_registration_generate_password` debe tratarse como ajuste por sitio porque WooCommerce puede variar por subsite.
- Textos deben poder configurarse por sitio con fallback: sitio → red → default plugin.
- Estilo/CSS debe poder configurarse por sitio con modo: heredar de red, sobrescribir o desactivar.
- Red debe conservar defaults globales y políticas de herencia, no sustituir todos los ajustes de cada web.
- En network admin no se mostrarán pestañas principales de Textos ni Estilo; esas pantallas quedan para cada site.

## Pendientes de decisión

- Definir si `block_wp_login` será global de red, por sitio o con política de red.
- Definir si `login_slug`, `reset_slug` y `login_slug_redirect` serán por sitio con fallback o globales de red.
- Definir si `max_attempts` y `blacklist` serán globales, por sitio o combinados.
- Definir cómo mostrar la opción nativa multisite `registration` en la UI de red.
- Definir si se migran valores existentes o solo se usan como fallback de red.
- Si el sitio debe confiar en cabeceras de proxy/CDN para IP real.
- AJAX de tabs queda para Fase 4 futura.

## No reabrir sin motivo

- No usar el repo raíz del workspace para versionar AuthGate.
- No trabajar directamente en `main` para desarrollo diario.
- No publicar `_dev/` en release pública.
- No modificar settings WooCommerce/WordPress fuera del alcance aprobado.
- No hacer release estable, tag ni deploy sin autorización explícita.
- En multisite no resolver Textos/Estilo como global único si el objetivo es separar webs.
