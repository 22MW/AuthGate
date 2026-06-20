# Debugger

## Última actualización

2026-06-19

## Resumen humano

Incidencia multisite post-release `1.2.0`: AuthGate mezcla ajustes globales de red, ajustes por sitio y opciones nativas por sitio. Esto explica que Registro, Textos y Estilo no se comporten como espera el usuario en una red multisite.

## Descubierto

- En `1.2.0`, `AuthGate_Settings::is_network()` devuelve `is_multisite()`.
- En multisite, `get()` usa `get_site_option()` para casi todo.
- `subsite_keys()` solo contiene `excluded_pages` y `mailmint_list_id`.
- Textos se guardan como `site_option` mediante `str_*`.
- CSS propio se guarda como `site_option` porque `custom_css` y `custom_css_enabled` no son subsite keys.
- `users_can_register` se lee/guarda con `get_option()`/`update_option()` desde una UI que aparece en pantalla principal/red.
- `woocommerce_registration_generate_password` también se lee/guarda por sitio actual.
- Exclusions y Mail Mint sí tienen pantalla por sitio y guardado por sitio.

## Hecho

- QA adicional corregido: Estilo vuelve a estar visible en red para editar CSS global; `/wp-admin` no logueado redirige al slug AuthGate; site header enlaza a configuración global.

- QA parcial del usuario recibido y corregido: registro pasa a control global de red, se ocultan Textos/Estilo en network admin, se quita registro local en site admin, site admin adopta diseño 22MW y los strings muestran mejor la herencia.

- MS2 aplicado para corregir Textos globales en multisite: ahora pueden tener override por sitio con fallback a red/default.

- MS1 aplicado para corregir incoherencia principal de Registro multisite: red manda y cada sitio guarda su propio `users_can_register`.
- MS1 evita guardar WooCommerce password desde la pantalla de red y lo guarda por sitio.

- MCP WooCommerce verificado en lectura en diagnóstico anterior.
- Fatal `AUTHGATE_URL` corregido en fase 22MW-BACK.
- Diagnóstico multisite realizado sin tocar código ni base de datos.
- Causa probable/confirmada documentada: política multisite inconsistente.

## Pendiente

- Confirmar en entorno multisite real la opción nativa de red `registration` y sus valores actuales.
- Validar con dos subsites que Registro, Textos y CSS se comportan distinto tras implementar el plan.
- Revisar flush de rewrites si `login_slug`/`reset_slug` pasan a ser por sitio.

## No volver a investigar

- El error reportado no es de reset password; es de registro/multisite settings.
- WooCommerce puede variar por sitio.
- En `1.2.0`, Textos y CSS son globales de red en multisite. Desde MS2, Textos ya tienen override por sitio.
- Desde MS1, Registro ya no debe configurarse desde pantalla de red; se configura por sitio.
- En `1.2.0`, Exclusions y Mail Mint ya son por sitio.
- Registro no está correctamente separado: se muestra en red, pero guarda opciones por sitio actual.
- Si red multisite bloquea registro de usuarios, AuthGate no debe permitir que un sitio lo active.

## Riesgos o bloqueos

- Sin entorno multisite real no se puede confirmar el valor actual de `registration` ni el flujo exacto de UI.
- Cambiar settings de registro altera comportamiento de usuarios; requiere permiso explícito.
- Crear usuarios de prueba altera estado; requiere permiso explícito.

## Próximo paso recomendado

- Fix mínimo por fases: MS0 mapa de scopes, MS1 Registro por sitio respetando red, MS2 Textos por sitio, MS3 Estilo por sitio, MS4 General separado y MS5 QA multisite.

---

## Incidencia 2026-06-17 — Fatal en admin tras Fase 1 22MW-BACK

## Error

- Fatal: `Undefined constant "AUTHGATE_URL"` en `includes/class-auth-settings.php`.

## Hechos confirmados

- `authgate.php` no define `AUTHGATE_URL` ni `AUTHGATE_VERSION`.
- Fase 1 encoló `authgate-back.css` y `authgate-back.js` usando esas constantes inexistentes.

## Hecho

- QA parcial del usuario recibido y corregido: registro pasa a control global de red, se ocultan Textos/Estilo en network admin, se quita registro local en site admin, site admin adopta diseño 22MW y los strings muestran mejor la herencia.

- Corregido el encolado para usar `$asset_url = plugin_dir_url(dirname(__FILE__))` y versión local `1.1.1`.
- Validado `php -l includes/class-auth-settings.php`: ok.
- Validado `git diff --check`: ok.

## No volver a investigar

- AuthGate 1.1.1 no tiene constantes globales `AUTHGATE_URL` / `AUTHGATE_VERSION`; no usarlas sin definirlas antes.
