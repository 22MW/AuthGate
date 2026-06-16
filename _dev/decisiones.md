# Decisiones

## Última actualización

2026-06-16

## Decisiones confirmadas

- AuthGate se trabaja desde `app/public/wp-content/plugins/AuthGate/`.
- La rama de trabajo actual es `mishaAuthDev`.
- El repo del plugin es independiente del workspace Kilo.
- `_dev/` se usa como memoria operativa interna y no debe ir a ZIP/deploy público.
- AuthGate debe poder cambiar directamente la opción nativa WordPress `users_can_register`.
- Si el registro está desactivado, AuthGate oculta completamente la parte de registro en frontend.
- Si WooCommerce está activo, AuthGate duplica en su backend la opción `woocommerce_registration_generate_password`.
- Las versiones dev usan formato de cuatro números; la versión dev actual es `1.1.0.6`.
- `Stable tag` no se cambia en dev; queda para release estable.
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

## Pendientes de decisión

- Si el sitio debe confiar en cabeceras de proxy/CDN para IP real.
- Qué versión pública consolidará los dev cuando se prepare release estable.
- Qué versión pública consolidará los dev cuando se prepare release estable.

## No reabrir sin motivo

- No usar el repo raíz del workspace para versionar AuthGate.
- No trabajar directamente en `main` para desarrollo diario.
- No publicar `_dev/` en release pública.
- No modificar settings WooCommerce/WordPress fuera del alcance aprobado.
- No hacer release estable, tag ni deploy sin autorización explícita.
