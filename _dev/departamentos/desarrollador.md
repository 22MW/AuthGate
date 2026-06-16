# Desarrollador

## Última actualización

2026-06-16

## Resumen humano

Bloque A implementado localmente en modo ahorro. Tras revisión visual del usuario, el enlace “Ir a la página de inicio” se movió dentro del formulario inline para aparecer abajo, junto al estilo de enlaces del formulario.

## Descubierto

- El checkbox newsletter estaba en `form-register.php` y en el panel registro de `form-combined.php`.
- El render inline se controla desde `render_wrapper()` en `class-auth-forms.php`.
- El enlace de inicio fuera del contenedor quedaba visualmente desplazado a la derecha.

## Hecho

- Añadido helper `AuthGate_Forms::is_mailmint_available()`.
- Ocultado checkbox newsletter si Mail Mint no está disponible.
- Añadido enlace “Ir a la página de inicio” solo en render inline.
- Reubicado el enlace de inicio al final del contenido inline, usando clases de enlace del formulario.
- Validado `php -l` en archivos PHP tocados.
- Validado `git diff --check`.

## Pendiente

- QA manual del Bloque A después del ajuste visual.
- Si QA OK, bump dev `1.1.0.3`, release notes, commit y push.

## No volver a investigar

- Bloque A no toca settings de admin ni base de datos.
- El enlace de inicio no debe aparecer en popup.

## Riesgos o bloqueos

- No hay bloqueo técnico confirmado.

## Próximo paso recomendado

- Validar frontend inline y popup.
