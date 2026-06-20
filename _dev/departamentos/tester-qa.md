# Tester QA

## Última actualización

2026-06-19

## Resumen humano

QA multisite parcial recibido: se corrigieron los puntos reportados y queda repetir validación conjunta MS1/MS2/MS3.

## Descubierto

- Entorno confirmado por MCP: local `https://plugins.local`.
- WooCommerce confirmado activo: `10.7.0`.
- AuthGate permite gestionar registro WordPress y opción WooCommerce de contraseña.
- Usuario confirmó QA P1 OK.
- MS1/MS2/MS3 requieren validación conjunta en red multisite con al menos 2 subsites.
- Hallazgos QA: registro debía controlarse desde red, Textos/Estilo no debían mostrarse en network admin, registro local por site no afectaba, site admin no tenía diseño 22MW y strings parecían vacíos sin herencia clara.

## Hecho

- QA multisite MS1/MS2/MS3 confirmado por el usuario antes de release `1.2.1`.

- Validación técnica previa: `php -l` en PHP tocado.
- `git diff --check` sin salida antes del push dev.
- QA manual P1 confirmada por el usuario.
- Preparado alcance de QA conjunto MS1/MS2/MS3.
- Fixes QA aplicados sobre los cinco hallazgos reportados.

## Pendiente

- Revalidar site-level multisite: badge Config global enlaza a red, toast guarda correctamente y no aparece línea blanca izquierda.
- Revalidar en multisite que cada site con WooCommerce muestra y guarda “Contraseña WooCommerce”.
- Ajuste toast: posición fija para que sea visible aunque se guarde desde zonas bajas de la página.
- Ajuste backend: toast AJAX unificado en pantallas AuthGate, tab Estilo renombrado a CSS y botón Guardar CSS movido bajo el editor.
- Ajuste logo: quitado logo de la pantalla protegida del sitio y enlazado a home el logo personalizado de la URL AuthGate.
- Presets CSS actualizados: blanco toma los ajustes visuales aportados por el usuario y oscuro replica tamaños/interacciones adaptando colores.
- Revalidar que Estilo global aparece en red y puede heredarse desde sites.
- Revalidar que `/wp-admin` no logueado redirige a `/acceder/` si ese es el slug configurado.
- Revalidar enlace “Config global” desde AuthGate de cada site.
- Validar que la red activa/desactiva registro desde AuthGate.
- Validar que los sites ya no muestran control local de registro.
- Validar textos distintos por subsite y fallback a red/default.
- Validar CSS heredado, sobrescrito y desactivado por subsite.
- Validar que Exclusiones y Mail Mint siguen sin cruzarse entre sitios.
- Validar que network admin no muestra pestañas Textos/Estilo.
- Validar que site admin respeta diseño 22MW.

## No volver a investigar

- Entorno actual es local.
- P1 ya está implementada en `1.1.0.1`.
- QA P1 OK confirmado por el usuario.
- No crear usuarios de prueba sin permiso explícito si se considera prueba con efecto.

## Riesgos o bloqueos

- QA multisite no ejecutado todavía.

## Próximo paso recomendado

- Ejecutar QA manual MS1/MS2/MS3 junto con el usuario antes de commit/push o MS4.
