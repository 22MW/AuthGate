# Roadmap

## Urgente

- Plan multisite settings por red/sitio guardado; pendiente decidir implementación por fases.

## Recomendado

### Multisite settings por red/sitio

| Código | Nombre | Estado | Prioridad | Qué contiene | Archivos probables o áreas | Validación prevista | Bloqueos/dependencias |
|---|---|---|---|---|---|---|---|
| MS0 | Mapa de scopes multisite | hecho | alta | Definidas constantes de scope, mapa explícito y helpers compatibles sin cambiar comportamiento efectivo | `includes/class-auth-settings.php`, `_dev/decisiones.md` | `php -l` y `git diff --check` OK | Base lista para MS1 |
| MS1 | Registro global de red | hecho | alta | La red gestiona la política nativa `registration`; los sites ya no muestran control local de registro | `includes/class-auth-settings.php` | QA multisite confirmado por usuario | Cerrado para 1.2.1 |
| MS2 | Textos por sitio con fallback | hecho | alta | Textos editables por sitio; campos vacíos muestran herencia red/default como placeholder/ayuda | `includes/class-auth-settings.php`, frontend que usa strings | QA multisite confirmado por usuario | Cerrado para 1.2.1 |
| MS3 | Estilo/CSS por sitio con fallback | hecho | alta | Pantalla site-level con diseño 22MW y modo heredar, sobrescribir o desactivar CSS; frontend resuelve sitio → red → default | `includes/class-auth-settings.php`, `includes/class-auth-forms.php` | QA multisite confirmado por usuario | Cerrado para 1.2.1 |
| MS4 | General red/sitio separado | pendiente | media | Separar logo, intro, slugs, redirect, exclusiones, Mail Mint y seguridad según scope | `includes/class-auth-settings.php`, rewrites | Guardado claro por contexto | Rewrites/flush multisite |
| MS5 | QA multisite | pendiente | alta | Pruebas con red + 2 subsites | QA manual, `tester-qa.md`, `visual.html` | Checklist multisite completo | No crear usuarios sin permiso |

- Validar updater/instalación desde una versión anterior con el ZIP `1.2.0` en entorno seguro.
- Revisar documentación pública solo si aparecen dudas de soporte tras la publicación.
- Mantener la landing comercial interna en `_dev/comercial/` hasta decidir publicación externa.

## Futuro

### Backend/admin visual 22MW

- Fase 4 futura: carga dinámica por AJAX admin solo si aporta valor real.
- Extender el patrón `22mw-back` a otros plugins después de validar AuthGate como piloto.

### Comercial

- Añadir pantallazos/GIFs reales a `_dev/comercial/`.
- Decidir si la landing comercial pasa a `https://22mw.online/`.
- Validar demanda antes de fijar pricing definitivo.

### Técnico

- Definir soporte explícito para proxy/CDN si se necesita confiar en cabeceras de IP.

## Bloqueado

- Sin urgente abierto tras release `1.2.1`.
- Creación de usuarios de prueba bloqueada hasta permiso explícito si se considera prueba que altera datos.

## Descartado

- No hacer refactor general dentro de estas mejoras.
- No mezclar cambios comerciales internos con el ZIP/release público.
- No incluir `_dev/`, `.kilo/`, secretos, logs ni backups en ZIP/release.
- No avanzar con AJAX admin como “ya que estamos”.
- No resolver multisite con un checkbox global único si se necesitan webs con textos/estilos distintos.

## Cerrado

- Release `1.1.1` publicada.
- Release `1.2.0` publicada.
- Release `1.2.1` publicada.
- Backend/admin visual 22MW Fases 1, 2, 2B y 3 consolidadas en `1.2.0`.
- Base reusable `22mw-back` sincronizada dentro de AuthGate.
- Duplicación de estilos base eliminada: base común en `22mw-back.*`, overrides específicos en `authgate-back.*`.
- QA manual confirmado por el usuario antes de release `1.2.0`.
- MS0–MS3 multisite con QA confirmado para `1.2.1`.
