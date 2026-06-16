# Estado del plugin

## Última actualización

2026-06-16

## Resumen humano

AuthGate está en rama dev `mishaAuthDev`. La P1 de control de registro quedó implementada y QA OK. La mejora popup con `label` fue implementada y pusheada. Bloque A quedó pusheado como `1.1.0.3`. Bloque B está implementado como `1.1.0.4`, pendiente de QA manual.

## Estado general

Dev `1.1.0.4` preparada con Bloque B; pendiente QA manual.

## Hecho

- Plugin localizado en `app/public/wp-content/plugins/AuthGate/`.
- Archivo principal confirmado: `authgate.php`.
- Versión dev actual declarada: `1.1.0.4`.
- Rama actual confirmada: `mishaAuthDev`.
- P1 implementada y QA OK.
- Mejora popup pusheada con commit `58a9622 feat: add popup shortcode labels`.
- Bloque A: checkbox newsletter se oculta si Mail Mint no está activo.
- Bloque A: enlace “Ir a la página de inicio” en render inline.
- QA Bloque A confirmado por el usuario.
- Bloque A pusheado en commit `bdd7d6e`.
- Bloque B: WYSIWYG “Texto bajo el logo” añadido para render inline.

## En curso

- QA manual del Bloque B.

## Bloqueado

- Release estable bloqueada hasta permiso explícito.
- Release estable bloqueada hasta cerrar o descartar mejoras pre-release elegidas.

## Próximo paso recomendado

- Validar Bloque B en login, registro y combinado inline.

## No volver a investigar

- Ruta real del plugin: `app/public/wp-content/plugins/AuthGate/`.
- Rama de trabajo: `mishaAuthDev`.
- Versión dev actual declarada: `1.1.0.4`.
- QA Bloque A OK confirmado por el usuario.
- Bloque B no debe aparecer en popup.
- Atributo elegido para texto de popup: `label`.
- Bloque A: Mail Mint condicional + enlace inicio inline.
- No hacer release estable, tag ni deploy sin autorización explícita.
