# Desarrollador

## Última actualización

2026-06-19

## Resumen humano

MS0–MS3 multisite aplicados localmente. Registro, Textos y CSS ya tienen separación por sitio/red según el plan; queda QA conjunto antes de commit/push o avanzar a MS4.

## Descubierto

- El checkbox newsletter estaba en `form-register.php` y en el panel registro de `form-combined.php`.
- El render inline se controla desde `render_wrapper()` en `class-auth-forms.php`.
- El enlace de inicio fuera del contenedor quedaba visualmente desplazado a la derecha.
- Los textos WYSIWYG existentes usan `wp_editor()` y se guardan con `wp_kses_post()`.

## Hecho

- Ajuste toast: posición fija para que sea visible aunque se guarde desde zonas bajas de la página.
- Ajuste backend: toast AJAX unificado en pantallas AuthGate, tab Estilo renombrado a CSS y botón Guardar CSS movido bajo el editor.
- Ajuste logo: quitado logo de la pantalla protegida del sitio y enlazado a home el logo personalizado de la URL AuthGate.
- Presets CSS actualizados: blanco toma los ajustes visuales aportados por el usuario y oscuro replica tamaños/interacciones adaptando colores.
- Fix QA: pestaña Estilo vuelve a mostrarse en network admin para mantener el CSS global heredable.
- Fix QA: añadido redirect de `/wp-admin` no logueado al slug de login configurado cuando `block_wp_login` está activo.
- Fix QA: header de AuthGate por site añade enlace a configuración global de red.

- MS2: `get_string()` usa fallback sitio → red → default mediante `get('str_*')`.
- MS2: añadida sección de textos por sitio en `render_site_page()`.
- MS2: `save_site_settings()` guarda overrides de textos por sitio.
- MS2: submit site-level dispara `tinyMCE.triggerSave()` para guardar WYSIWYG.

- MS3: `custom_css` y `custom_css_enabled` pasan a scope `site_with_network_fallback`.
- MS3: añadido `custom_css_mode` por sitio con valores `inherit`, `override` y `disabled`.
- MS3: añadida sección “Estilo de este sitio” en pantalla site-level.
- MS3: `custom_css_enabled()` desactiva CSS propio si el sitio marca modo `disabled`.
- MS3: guardado site-level sanitiza CSS local con la sanitización existente.

- Fix QA multisite: network admin permite cambiar `registration` desde AuthGate.
- Fix QA multisite: network admin oculta pestañas Textos y Estilo.
- Fix QA multisite: site admin ya no muestra ni guarda registro local.
- Fix QA multisite: site admin usa wrapper visual 22MW.
- Fix QA multisite: campos de texto por site muestran el valor heredado como placeholder/ayuda.

- MS1: añadidos helpers `network_allows_user_registration()` y `network_registration_label()`.
- MS1: `registration_enabled()` respeta la política global de red y `users_can_register` del sitio.
- MS1: eliminado guardado de registro/WooCommerce password desde pantalla de red.
- MS1: añadido bloque Registro en la pantalla site-level de cada web.
- MS1: `save_site_settings()` guarda `users_can_register` y WooCommerce password por sitio.

- MS0: añadido mapa explícito de scopes multisite en `includes/class-auth-settings.php`.
- MS0: añadidos helpers `setting_scope()`, `setting_uses_site_option()` y `option_name()`.
- MS0: `update_setting()` y `get()` usan el mapa de scopes, manteniendo compatibilidad con el comportamiento anterior.
- Validado `php -l includes/class-auth-settings.php`: ok.

- Añadido helper `AuthGate_Forms::is_mailmint_available()`.
- Ocultado checkbox newsletter si Mail Mint no está disponible.
- Añadido enlace “Ir a la página de inicio” solo en render inline.
- Reubicado el enlace de inicio al final del contenido inline, usando clases de enlace del formulario.
- Validado `php -l` en archivos PHP tocados.
- Validado `git diff --check`.
- Bloque B: añadido campo `inline_intro_html` en ajustes.
- Bloque B: renderizado solo en modo inline de login, registro y combinado.
- Bloque B QA: centrado frontend y toolbar WYSIWYG ampliada.
- Bloque C: añadida pestaña `Textos`.
- Bloque C: quitado bloque de textos de la pestaña General.
- Bloque C: añadido guardado independiente `authgate_save_strings` con nonce propio.
- Bloque D: añadida pestaña `CSS propio`.
- Bloque D: añadido guardado independiente `authgate_save_css` con nonce propio.
- Bloque D: CSS frontend se carga solo si el checkbox está activo.
- Bloque D: presets blanco y oscuro añadidos como texto copiable.
- Bloque D: preset blanco queda activo por defecto y define caja, inputs, botones y espaciado sin forzar fuentes.
- Bloque D: ajustado preset blanco para reducir inputs, mejorar textos e incluir logo/título/descripción de página protegida dentro de la card.
- Bloque D: aplicado al preset oscuro el mismo patrón visual del preset blanco.
- Bloque D: extraída base común para que blanco y oscuro compartan tamaños, espaciados y estructura; solo cambian colores.

## Pendiente

- Repetir QA multisite conjunto tras fixes de QA MS1/MS2/MS3.

## No volver a investigar

- MS0 no cambia scopes efectivos de Textos/CSS; solo introduce mapa y helpers.
- `users_can_register` y `woocommerce_registration_generate_password` están declarados como scope site para preparar MS1.
- Bloque A no toca settings de admin ni base de datos.
- El enlace de inicio no debe aparecer en popup.
- Bloque B guarda HTML permitido mediante `wp_kses_post()`.
- Bloque C conserva sanitización previa por tipo de texto.
- Bloque D bloquea patrones CSS peligrosos básicos: `@import`, `javascript:`, `expression()`, `behavior` y `-moz-binding`.
- MS3 reutiliza la sanitización CSS existente para el CSS local por sitio.

## Riesgos o bloqueos

- No hay bloqueo técnico confirmado.

## Próximo paso recomendado

- Validar MS1/MS2/MS3 en multisite real con al menos dos subsites.
