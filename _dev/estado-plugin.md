# Estado del Plugin

## Plugin

- Nombre: AuthGate by 22MW / WP AuthGate
- Slug: AuthGate / authgate
- Repositorio: https://github.com/22MW/AuthGate
- Rama actual: mishaAuthDev
- Última actualización: 2026-06-09

---

## Resumen

- Estado general: en desarrollo / estable inicial según versión 1.1.0 publicada.
- Última acción importante: repositorio movido a la ruta activa de WordPress y checkout de `mishaAuthDev`.
- Siguiente paso seguro: auditoría técnica y de seguridad dirigida antes de modificar código funcional.
- Bloqueos activos: no hay bloqueo confirmado.
- Riesgos activos: riesgos visibles pendientes de revisión detallada en seguridad, compatibilidad y release.

---

## Datos técnicos

- Versión plugin: 1.1.0
- Text domain: authgate
- PHP mínimo: 7.4
- WordPress mínimo: 6.2
- Tested up to: 6.7
- WooCommerce requerido: no; integración condicional si `WooCommerce` está activo.
- Dependencias: WordPress; WooCommerce opcional; Mail Mint opcional; GitHub Releases para auto-updater.

---

## Áreas

- Funcionalidad: parcial revisada. Formularios frontend, registro, login, reset, protección de páginas y shortcodes documentados.
- Arquitectura: parcial revisada. Carga por archivo principal, clases en `includes/`, integración WooCommerce separada, plantillas y assets.
- Seguridad: riesgo detectado / requiere revisión específica.
- QA: no revisada funcionalmente.
- Soporte/uso: parcial. `readme.txt` documenta instalación, shortcodes y comportamiento principal.
- Documentación: parcial. Existen `readme.txt` y `CHANGELOG.md`; `_dev/` creado ahora.
- Release/versionado: parcial. Versión 1.1.0 coherente entre cabecera y readme; existe changelog.
- Compatibilidad: parcial. Multisite y WooCommerce declarados; no validados en entorno activo.

---

## Archivos principales

- Archivo principal: `authgate.php`
- Includes: `includes/`
- Assets: `assets/css/auth-forms.css`, `assets/js/auth-forms.js`
- Languages: `languages/`
- Templates: `includes/templates/`
- Integraciones: `integrations/woocommerce/class-wc-integration.php`
- Uninstall: `uninstall.php`
- `_dev/`: `_dev/`

---

## Documentación interna `_dev/`

- Notas: este archivo creado como punto de control inicial.
- Arquitectura: pendiente.
- QA: pendiente.
- Seguridad: pendiente.
- Soporte: pendiente.
- Release: pendiente.
- Decisiones: pendiente.

---

## Git

- Repo inicializado: sí.
- Rama actual: `mishaAuthDev`.
- Ramas remotas detectadas: `origin/main`, `origin/mishaAuthDev`, `origin/release`.
- Cambios pendientes: sí, por creación y actualización de `_dev/estado-plugin.md`.
- Último checkpoint/commit visible en rama: `Release 1.1.0`.
- Avisos: no se hizo commit ni push. Commit/push bloqueado hasta instrucción explícita `APROBADO COMMIT`.

---

## Validaciones

- `php -l`: pendiente; no hay PHP funcional tocado.
- `git diff --check`: correcto, sin salida.
- `error.log`: no revisado.
- QA funcional: no ejecutado.
- Seguridad mínima: revisión inicial documental, no auditoría completa.

---

## Auditoría inicial

### Hechos confirmados

- El plugin carga clases principales desde `authgate.php`.
- Usa shortcodes frontend y AJAX para login, registro, recuperación y reset de contraseña.
- Usa nonces en AJAX frontend (`check_ajax_referer`) según revisión inicial de `class-auth-forms.php`.
- Implementa rate limiting por IP mediante transients.
- Guarda logs en tabla propia `authgate_log`.
- Tiene integración WooCommerce condicional y Mail Mint opcional.
- Tiene auto-updater contra GitHub Releases.
- Tiene `uninstall.php` para limpieza de tabla, opciones y transients.

### Riesgos visibles

- `authgate.php` usa `$_SERVER['REQUEST_URI']` y `$_GET['action']` en bloqueo inicial sin sanitización visible en esa lectura. Requiere revisión de seguridad antes de tocar.
- `uninstall.php` ejecuta borrados SQL amplios por prefijo `authgate_%`; requiere revisar impacto en multisite y opciones no previstas.
- El updater externo depende de GitHub Releases y ZIP esperado `authgate.zip`; requiere validación de flujo release antes de publicar.
- No se detectaron `composer.json`, `package.json` ni tests automatizados en inventario inicial.
- `_dev/` no estaba presente al inicio; creado para separar documentación interna.
- El plugin ya está ubicado en `/Users/22mw/Local Sites/plugins/app/public/wp-content/plugins/AuthGate`, ruta activa de plugins del WordPress local.

### Hipótesis probable

- El plugin está en una fase estable inicial tras release 1.1.0, pero falta validación formal de seguridad, QA funcional y proceso de release.

---

## Release

- Versión preparada: no confirmada.
- Changelog actualizado: sí, hasta 1.1.0.
- ZIP limpio: no revisado.
- `_dev/` excluido: no confirmado; `.gitignore` no excluye `_dev/`.
- `.kilo/` excluido: no aplica dentro del repo clonado según inventario inicial.
- Producción/staging: no confirmado.

---

## Supuestos

- La rama `mishaAuthDev` es la rama de trabajo solicitada por el usuario.
- La incorporación inicial permite crear documentación interna `_dev/estado-plugin.md` sin modificar código funcional.
- No se debe hacer commit ni push hasta recibir `APROBADO COMMIT`.

---

## Siguiente paso recomendado

Delegar una auditoría de seguridad específica sobre `authgate.php`, `includes/class-auth-forms.php`, `includes/class-auth-settings.php`, `includes/class-github-updater.php`, `integrations/woocommerce/class-wc-integration.php` y `uninstall.php`, sin modificar código. Después, decidir una corrección mínima solo si hay hallazgos críticos confirmados.
