# Estado del plugin

## Última actualización

2026-06-16

## Resumen humano

AuthGate está en rama dev `mishaAuthDev`. La P1 de control de registro quedó implementada y QA OK. Se añadió una mejora de shortcodes popup con `label` para personalizar el texto del botón y `button_class` para clases.

## Estado general

Dev preparada como `1.1.0.2`; pendiente commit/push de la mejora popup.

## Hecho

- Plugin localizado en `app/public/wp-content/plugins/AuthGate/`.
- Archivo principal confirmado: `authgate.php`.
- Versión dev actual declarada: `1.1.0.2`.
- Rama actual confirmada: `mishaAuthDev`.
- P1 implementada y QA OK.
- Mejora local: atributo `label` en popups de `[authgate_login]`, `[authgate_register]` y `[authgate_auth]`.
- Mejora local: ejemplos añadidos en backend de Shortcodes.
- Validación técnica de la mejora popup: `php -l` y `git diff --check` OK.

## En curso

- QA manual de shortcodes popup con `label` y `button_class`.

## Bloqueado

- Push de la mejora popup pendiente de ejecutar.
- Release estable bloqueada hasta permiso explícito.

## Próximo paso recomendado

- Probar: `[authgate_login mode="popup" label="Entrar" button_class="btn btn-secondary"]`.
- Probar: `[authgate_register mode="popup" label="Crear cuenta"]`.
- Hacer commit y push de `1.1.0.2`.

## No volver a investigar

- Ruta real del plugin: `app/public/wp-content/plugins/AuthGate/`.
- Rama de trabajo: `mishaAuthDev`.
- Versión dev declarada: `1.1.0.2`.
- Atributo elegido para texto de popup: `label`.
- No hacer release estable, tag ni deploy sin autorización explícita.
