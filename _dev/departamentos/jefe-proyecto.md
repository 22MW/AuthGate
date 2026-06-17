# Jefe de Proyecto

## Última actualización

2026-06-17

## Resumen humano

Roadmap pre-release consolidado automáticamente en `_dev/`. AuthGate tiene P1 cerrada, popup `label` en dev `1.1.0.2` y un nuevo paquete de mejoras antes de release ordenado por bloques.

## Descubierto

- El usuario quiere varias mejoras antes de release, no preparar release todavía.
- Las mejoras afectan frontend, admin, WYSIWYG, tabs y CSS propio.
- El orden debe minimizar riesgo: primero UX simple, después WYSIWYG/tabs, CSS al final.

## Hecho

- Fase 2/Fase 2B commiteada y pusheada: componentes visuales, switches, tablas, notices y selector buscable de Exclusiones.


- P1 implementada y QA OK.
- Popup `label` implementado y pusheado como `1.1.0.2`.
- Decisiones funcionales pre-release consolidadas.
- Roadmap por bloques A/B/C/D creado.

- Fase 3 implementada localmente en General: submenú vertical interno sin AJAX ni cambios funcionales.

- AuthGate sincronizado localmente con la base portable de la skill `22mw-back`; pendiente validar y commitear si procede.

- Aplicada opción A: AuthGate usa clases/data attributes `mw22-back`; se evita mantener dos sistemas visuales paralelos.

## Pendiente

- Fase 1 `22mw-back` validada, commiteada y pusheada: shell admin, menú horizontal tipo landing y dark/light por `localStorage`.
- Separar rediseño admin de frontend público y de cambios funcionales.
- Mantener cada bloque separado para evitar mezclar riesgos.
- Versionar dev por bloque si se hace push.

## No volver a investigar

- Siguiente bloque recomendado: Bloque A.
- CSS propio queda al final por mayor riesgo.
- Tab “Textos” debe guardar separado por tab.
- WYSIWYG inline aplica a login, register y combined.

## Riesgos o bloqueos

- Release estable bloqueada hasta cerrar mejoras pre-release elegidas.
- CSS propio requiere sanitización y editor tipo CodeMirror.

## Próximo paso recomendado

- Preparar Fase 3: submenús verticales internos por pantalla, sin AJAX tabs ni cambios de guardado.

- No usar `plan-funcionalidad` para rediseños backend/admin; usar `22mw-back` o `plan-backend-plugin` según el nivel de detalle.
