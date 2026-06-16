# Decisiones

## Última actualización

2026-06-16

## Decisiones confirmadas

- AuthGate se trabaja desde `app/public/wp-content/plugins/AuthGate/`.
- La rama de trabajo actual es `mishaAuthDev`.
- El repo del plugin es independiente del workspace Kilo.
- `_dev/` se usa como memoria operativa interna y no debe ir a ZIP/deploy público.
- AuthGate debe poder cambiar directamente la opción nativa WordPress `users_can_register`.
- Si el registro está desactivado, AuthGate oculta completamente la parte de registro en frontend.
- Si WooCommerce está activo, AuthGate duplica en su backend la opción `woocommerce_registration_generate_password`.
- Las versiones dev usan formato de cuatro números; la versión dev actual es `1.1.0.1`.
- `Stable tag` no se cambia en dev; queda para release estable.
- QA P1 fue confirmado como OK por el usuario.
- `CHANGELOG.md` puede registrar entradas dev, manteniendo `Stable tag` sin cambios hasta release estable.

## Pendientes de decisión

- Si el sitio debe confiar en cabeceras de proxy/CDN para IP real.
- Qué versión pública consolidará `1.1.0.1` cuando se prepare release estable.

## No reabrir sin motivo

- No usar el repo raíz del workspace para versionar AuthGate.
- No trabajar directamente en `main` para desarrollo diario.
- No publicar `_dev/` en release pública.
- No modificar settings WooCommerce/WordPress fuera del alcance aprobado.
- No hacer release estable, tag ni deploy sin autorización explícita.
