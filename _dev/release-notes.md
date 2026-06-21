# Release Notes Operativas

## Última actualización

2026-06-20

## Release 1.2.3 — 2026-06-21

- Release de metadatos del plugin.
- Actualiza Author, Author URI, License URI, requisitos WordPress/PHP y compatibilidad WooCommerce declarada.
- No incluye cambios funcionales.

## Release 1.2.2 — 2026-06-20

- Hotfix estable posterior a `1.2.1`.
- Restaura ajuste WooCommerce de contraseña por site multisite.
- Ajusta copy de registro global en network admin.
- Corrige header, enlace global, toast y margen izquierdo en pantalla AuthGate por site.
- QA manual confirmado por el usuario antes de release.
- Release publicada en GitHub: `https://github.com/22MW/AuthGate/releases/tag/v1.2.2`.
- ZIP `authgate.zip` subido como asset.
- Rama `main` actualizada.
- Rama `release` limpia sin `_dev/`.

## Release 1.2.1 — 2026-06-20

- Consolidación estable de MS0–MS3 multisite, fixes QA y mejoras admin posteriores a `1.2.0`.
- QA multisite confirmado por el usuario antes de preparar release.
- Incluye registro global de red, textos por sitio con fallback, CSS global/heredado/override por site, redirect `/wp-admin`, ajustes de logo y toast admin unificado.
- `_dev/`, `.kilo/`, secretos y contenido interno quedan excluidos del ZIP/release público.
- Release publicada en GitHub: `https://github.com/22MW/AuthGate/releases/tag/v1.2.1`.
- ZIP `authgate.zip` subido como asset.
- Rama `main` actualizada.
- Rama `release` limpia sin `_dev/`.

## Dev 1.2.0.3 — 2026-06-20

- Quitado el logo de la pantalla protegida renderizada dentro del tema.
- Mantenido el logo en la URL personalizada AuthGate.
- El logo personalizado de la URL AuthGate enlaza ahora a la home.

## Dev 1.2.0.2 — 2026-06-20

- MS0–MS3 multisite: mapa de scopes, registro global de red, textos por sitio con fallback y CSS por sitio con herencia/sobrescritura/desactivación.
- Restaurada la pestaña global “Estilo” en network admin para editar el CSS heredable.
- Añadido enlace a configuración global desde la pantalla AuthGate de cada site.
- Corregida redirección de `/wp-admin` no logueado hacia el slug AuthGate configurado.
- Actualizados presets CSS blanco y oscuro con los ajustes visuales confirmados.
- QA manual parcial recibido; queda repetir QA conjunto multisite antes de release estable.


## Release 1.2.0 — 2026-06-17

- Preparada versión estable `1.2.0` para consolidar el rediseño backend/admin 22MW.
- Incluye shell visual 22MW, navegación horizontal, submenús verticales, dark/light, selector buscable de exclusiones y normalización `22mw-back`.
- QA manual confirmado por el usuario antes de preparar release.
- No entra contenido comercial en el ZIP/release público; todo lo comercial permanece en `_dev/` y debe quedar excluido.

## Release 1.1.1 — 2026-06-16

- Consolidación de devs `1.1.0.1` a `1.1.0.7`.
- Release production autorizado por el usuario.
- `deploy-release.sh` revisado y ajustado para excluir `_dev/` y `.kilo/` del ZIP/rama release.
- Script movido a `_dev/deploy-release.sh` y adaptado para leer `_dev/.env`.
- Release publicada en GitHub: `https://github.com/22MW/AuthGate/releases/tag/v1.1.1`.
- ZIP `authgate.zip` subido como asset.
- Rama `main` actualizada.

## Dev 1.1.0.7 — 2026-06-16

- Bloque D: añadida pestaña “CSS propio”.
- Bloque D: añadido checkbox para activar/desactivar CSS frontend.
- Bloque D: añadido editor CodeMirror para CSS.
- Bloque D: añadidos presets copiables blanco y oscuro.
- Bloque D: sanitización conservadora del CSS antes de guardar.
- Bloque D: preset blanco activo por defecto con estilos base de caja, inputs, botones y espaciado.
- Ajuste visual Bloque D: card protegida incluye logo, título y descripción; inputs reducidos y textos equilibrados.
- Ajuste visual Bloque D: logo protegido mantiene proporción y se muestra completo.
- Ajuste visual Bloque D: preset oscuro actualizado con proporciones del preset blanco.
- Ajuste visual Bloque D: preset blanco actualizado con los tamaños manuales confirmados.
- Ajuste visual Bloque D: presets blanco y oscuro sincronizados para compartir estructura y tamaños.

## Dev 1.1.0.6 — 2026-06-16

- Bloque C: separada la edición de “Textos del formulario” a una pestaña propia.
- Bloque C: los textos se guardan con acción y nonce independientes.
- Bloque C: añadido `link_to_home` a textos configurables.

## Dev 1.1.0.5 — 2026-06-16

- Ajuste QA Bloque B: texto WYSIWYG centrado en frontend.
- Ajuste QA Bloque B: toolbar WYSIWYG ampliada con más controles de formato.

## Dev 1.1.0.4 — 2026-06-16

- Bloque B: añadido WYSIWYG “Texto bajo el logo” en ajustes.
- Bloque B: el texto se muestra solo en render inline de login, registro y combinado.
- Bloque B: si el campo está vacío no se renderiza nada.

## Dev 1.1.0.3 — 2026-06-16

- Bloque A: ocultado checkbox newsletter cuando Mail Mint no está disponible.
- Bloque A: añadido enlace “Ir a la página de inicio” en formularios inline.
- QA Bloque A confirmado por el usuario.

## Dev 1.1.0.2 — 2026-06-16

- Añadido atributo `label` para personalizar el texto del botón en shortcodes con `mode="popup"`.
- Mantenido y saneado `button_class` para clases CSS adicionales del botón popup.
- Añadidos ejemplos de `label` y `button_class` en la pestaña Shortcodes del backend.

## Dev 1.1.0.1 — 2026-06-10

- Añadida gestión desde AuthGate de `users_can_register`.
- Añadida gestión de `woocommerce_registration_generate_password` cuando WooCommerce está activo.
- Ocultado el registro frontend cuando WordPress no permite registros.
- Ajustado AJAX de registro para respetar el estado nativo de registro.
- Ajustada integración WooCommerce para no reactivar registro si WordPress/AuthGate lo desactiva.
- Creada memoria operativa `_dev/` inicial.
- QA P1 confirmado por el usuario el 2026-06-16.
- `CHANGELOG.md` actualizado con entrada `1.1.0.1`.

## Entrará en la próxima release

- Release `1.2.0` pendiente de commit/tag/publicación cuando el usuario autorice el paso final.

## Queda fuera

- Refactor general.
- Cambios de arquitectura.
- Deploy o publicación.
- Versionado automático.

## Validaciones pendientes

- `php -l` en PHP tocado antes de cerrar.
- `git diff --check`.
- QA manual de login, registro, lost password, reset y páginas protegidas.
- Validación de integración WooCommerce si el flujo de registro depende de WooCommerce.

## Riesgos antes de publicar

- Release `1.2.0` pendiente de autorización explícita para tag/publicación.
- `_dev/` debe excluirse de cualquier ZIP/deploy público.

## Limpieza post-release

- Actualizar `estado.md`, `roadmap.md`, `decisiones.md`, `release-notes.md` y `visual.html`.
- Confirmar que changelog público coincide con lo publicado.
