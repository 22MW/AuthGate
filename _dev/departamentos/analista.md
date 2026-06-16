# Analista

## Última actualización

2026-06-16

## Resumen humano

Se definió roadmap funcional pre-release para AuthGate. El usuario cerró decisiones sobre WYSIWYG inline, tab de textos, CSS propio y comportamiento Mail Mint/home link.

## Descubierto

- Mail Mint: si el plugin no está activo, no debe mostrarse checkbox newsletter en registro.
- Inline: debe añadirse enlace “Ir a la página de inicio” hacia home.
- WYSIWYG bajo logo: debe mostrarse en todas las variantes inline: login, register y combined; vacío no renderiza.
- Textos del formulario: deben separarse a un tab propio con guardado por tab.
- CSS propio: debe tener checkbox, sanitización, CodeMirror y presets blanco/oscuro copiables.

## Hecho

- Roadmap funcional ordenado por bloques.
- Decisiones cerradas registradas.

## Pendiente

- Definir criterios de aceptación detallados por bloque antes de implementar cada uno.
- Validar UX exacta del enlace de inicio y posición del contenido WYSIWYG al implementar.

## No volver a investigar

- El atributo de popup es `label`.
- WYSIWYG inline aplica a login/register/combined.
- CSS propio debe ser sanitizado y usar CodeMirror.

## Riesgos o bloqueos

- CSS propio puede romper visual si se aplica sin control.
- Guardado por tab requiere revisar estructura de formularios admin.

## Próximo paso recomendado

- Bloque A: criterios simples y ejecución mínima.
