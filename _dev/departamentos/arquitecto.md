# Arquitecto

## Última actualización

2026-06-19

## Resumen humano

AuthGate ya tiene aplicada localmente la arquitectura multisite MS0–MS3: mapa de scopes, Registro por sitio, Textos por sitio y CSS por sitio con fallback. Queda MS4 para separar General y MS5 QA.

## Descubierto

- Bootstrap principal en `authgate.php`.
- Formularios y AJAX en `includes/class-auth-forms.php`.
- Ajustes en `includes/class-auth-settings.php`.
- Updater en `includes/class-github-updater.php`.
- WooCommerce en `integrations/woocommerce/class-wc-integration.php`.
- `AuthGate_Settings::is_network()` devuelve `is_multisite()`, no distingue entre network admin, site admin y frontend.
- `subsite_keys()` solo separa por sitio `excluded_pages` y `mailmint_list_id`.
- Textos `authgate_str_*` son globales de red en multisite.
- `custom_css` y `custom_css_enabled` son globales de red en multisite.
- `users_can_register` y `woocommerce_registration_generate_password` son opciones por sitio, pero están en la pantalla principal/red.
- Pantalla por sitio actual solo contiene Exclusiones y Mail Mint.

## Hecho

- MS2 implementado: Textos por sitio con fallback sitio → red → default.
- MS2: pantalla site-level incluye sección “Textos de este sitio”; campos vacíos heredan texto global/default.
- MS2: `get_string()` resuelve a través de `get('str_*')` y el scope `site_with_network_fallback`.
- MS3 implementado: Estilo/CSS por sitio con modos heredar, sobrescribir y desactivar.
- MS3: CSS global de red queda como fallback para subsites que heredan.
- MS3: CSS local vacío no rompe fallback cuando el modo es heredar.

- MS1 implementado: Registro de usuarios se gestiona por sitio y respeta la política global de red.
- MS1: pantalla de red muestra la política nativa multisite y deriva configuración del sitio a Ajustes > AuthGate de cada web.
- MS1: pantalla por sitio permite guardar `users_can_register` y WooCommerce password local.

- Mapa inicial de responsabilidades identificado.
- MS0 implementado: constantes de scope multisite, `multisite_scope_map()`, `setting_scope()`, `setting_uses_site_option()` y `option_name()`.
- MS0 mantiene comportamiento actual: network para Textos/CSS/logo/slugs/seguridad y site para `excluded_pages`, `mailmint_list_id`, `users_can_register` y WooCommerce password.
- Diagnóstico arquitectónico multisite realizado sin tocar código.
- Plan de fases multisite guardado en `_dev/roadmap.md`.
- Decisiones multisite guardadas en `_dev/decisiones.md`.

## Pendiente

- QA conjunto MS1/MS2/MS3.
- Implementar MS4: General separado red/sitio.
- Revisar rewrites si slugs pasan a ser por sitio.

## No volver a investigar

- El plugin usa estructura PHP clásica, sin build Node confirmado.
- En `1.2.0`, `excluded_pages` y `mailmint_list_id` ya son por sitio.
- Textos y CSS ya tienen override por sitio en la rama de trabajo local.
- Registro multisite no tiene política única: UI de red + `update_option()` por sitio actual.
- Red multisite debe mandar si bloquea registro de usuarios.

## Riesgos o bloqueos

- Cambios de IP/proxy pueden requerir diseño configurable si hay CDN.
- Cambiar scopes sin fallback puede hacer parecer que se pierden textos/CSS configurados.
- Slugs por sitio pueden requerir flush de rewrite rules por subsite.
- WooCommerce puede estar activo en unos sitios y en otros no.
- Registro multisite depende también de la opción nativa de red `registration`.

## Próximo paso recomendado

- Siguiente paso técnico: QA multisite conjunto MS1/MS2/MS3 antes de MS4.
