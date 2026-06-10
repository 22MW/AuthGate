# Tester QA

## Última actualización

2026-06-10

## Resumen humano

P1 lista para QA manual. El registro debe aparecer/desaparecer según `users_can_register`, y el campo contraseña debe depender de la opción WooCommerce de enlace de configuración.

## Descubierto

- Entorno confirmado por MCP: local `https://plugins.local`.
- WooCommerce confirmado activo: `10.7.0`.
- My Account configurada y visible.

## Hecho

- Validación técnica ejecutada: `php -l` en PHP tocado.
- `git diff --check` sin salida.

## Pendiente

- Desactivar registro desde AuthGate y comprobar:
  - `[authgate_register]` no muestra formulario.
  - `[authgate_auth]` muestra solo login.
  - No aparece tab ni enlace de registro.
- Activar registro desde AuthGate y comprobar que registro vuelve.
- Alternar opción WooCommerce de enlace de contraseña y comprobar campo contraseña visible/oculto.
- Crear usuario de prueba solo si se autoriza prueba con alteración de datos.

## No volver a investigar

- No ejecutar pruebas que alteren datos reales en producción.
- Entorno actual es local.

## Riesgos o bloqueos

- La prueba de email WooCommerce requiere crear usuario o disparar correo; altera estado.

## Próximo paso recomendado

- QA manual visual y funcional sin crear usuario; después pedir permiso si se quiere probar creación real.
