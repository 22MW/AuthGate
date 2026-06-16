# Estado del plugin

## Última actualización

2026-06-16

## Resumen humano

AuthGate está en rama dev `mishaAuthDev`. La P1, popup `label`, Bloque A y Bloque B están cerrados. Bloque C está implementado como `1.1.0.6`: pestaña propia para textos y guardado separado.

## Estado general

Dev `1.1.0.6` preparada con Bloque C; QA OK confirmado por el usuario y push autorizado.

## Hecho

- Plugin localizado en `app/public/wp-content/plugins/AuthGate/`.
- Archivo principal confirmado: `authgate.php`.
- Versión dev actual declarada: `1.1.0.6`.
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

## En curso

- Commit y push de Bloque C.

## Bloqueado

- Release estable bloqueada hasta permiso explícito.
- Release estable bloqueada hasta cerrar o descartar mejoras pre-release elegidas.

## Próximo paso recomendado

- Hacer commit y push de Bloque C.

## No volver a investigar

- Ruta real del plugin: `app/public/wp-content/plugins/AuthGate/`.
- Rama de trabajo: `mishaAuthDev`.
- Versión dev actual declarada: `1.1.0.6`.
- Bloque C separa guardado de textos del guardado general.
- QA Bloque A OK confirmado por el usuario.
- Bloque B no debe aparecer en popup.
- QA Bloque B OK confirmado por el usuario.
- Atributo elegido para texto de popup: `label`.
- Bloque A: Mail Mint condicional + enlace inicio inline.
- No hacer release estable, tag ni deploy sin autorización explícita.
