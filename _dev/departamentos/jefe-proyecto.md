# Jefe de Proyecto

## Última actualización

2026-06-16

## Resumen humano

Roadmap pre-release consolidado automáticamente en `_dev/`. AuthGate tiene P1 cerrada, popup `label` en dev `1.1.0.2` y un nuevo paquete de mejoras antes de release ordenado por bloques.

## Descubierto

- El usuario quiere varias mejoras antes de release, no preparar release todavía.
- Las mejoras afectan frontend, admin, WYSIWYG, tabs y CSS propio.
- El orden debe minimizar riesgo: primero UX simple, después WYSIWYG/tabs, CSS al final.

## Hecho

- P1 implementada y QA OK.
- Popup `label` implementado y pusheado como `1.1.0.2`.
- Decisiones funcionales pre-release consolidadas.
- Roadmap por bloques A/B/C/D creado.

## Pendiente

- Ejecutar Bloque A si el usuario lo autoriza.
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

- Ejecutar Bloque A: Mail Mint condicional + enlace de inicio inline.
