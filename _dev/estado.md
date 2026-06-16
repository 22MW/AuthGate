# Estado del plugin

## Última actualización

2026-06-16

## Resumen humano

AuthGate está en rama dev `mishaAuthDev`. La P1 de control de registro quedó implementada y QA OK. La mejora popup con `label` fue implementada, versionada como `1.1.0.2` y pusheada. Bloque A pre-release está implementado y QA OK: Mail Mint condicional y enlace de inicio en modo inline.

## Estado general

Dev `1.1.0.3` preparada para cerrar Bloque A; QA Bloque A confirmado por el usuario.

## Hecho

- Plugin localizado en `app/public/wp-content/plugins/AuthGate/`.
- Archivo principal confirmado: `authgate.php`.
- Versión dev actual declarada: `1.1.0.3`.
- Rama actual confirmada: `mishaAuthDev`.
- P1 implementada y QA OK.
- Mejora popup pusheada con commit `58a9622 feat: add popup shortcode labels`.
- Bloque A: checkbox newsletter se oculta si Mail Mint no está activo.
- Bloque A: enlace “Ir a la página de inicio” en render inline.
- QA Bloque A confirmado por el usuario.

## En curso

- Commit y push de `1.1.0.3` para cerrar Bloque A.

## Bloqueado

- Release estable bloqueada hasta permiso explícito.
- Release estable bloqueada hasta cerrar o descartar mejoras pre-release elegidas.

## Próximo paso recomendado

- Commit y push de `1.1.0.3`; después ejecutar Bloque B.

## No volver a investigar

- Ruta real del plugin: `app/public/wp-content/plugins/AuthGate/`.
- Rama de trabajo: `mishaAuthDev`.
- Versión dev actual declarada: `1.1.0.3`.
- QA Bloque A OK confirmado por el usuario.
- Atributo elegido para texto de popup: `label`.
- Bloque A: Mail Mint condicional + enlace inicio inline.
- No hacer release estable, tag ni deploy sin autorización explícita.
