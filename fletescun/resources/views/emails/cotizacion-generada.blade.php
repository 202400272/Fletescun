@component('mail::message')
# 📧 Nueva Cotización Generada

Hola Javier,

Se ha generado exitosamente una nueva cotización en el sistema de FletesCun.

## Detalles de la Cotización

**Folio:** {{ $folio }}

**Cliente:** {{ $cliente->nombre }}  
**Teléfono:** {{ $cliente->telefono }}  
**Correo:** {{ $cliente->correo }}

**Origen:** {{ $cotizacion->direccion_origen }}  
**Destino:** {{ $cotizacion->direccion_destino }}

**Modalidad:** {{ $cotizacion->tipo_servicio }}  
**Estado:** {{ $cotizacion->estatus }}

**Fechas:**
- Generada: {{ $cotizacion->creado_en->format('d/m/Y H:i') }}
- Fecha ideal de servicio: {{ $cotizacion->fecha_ideal ? \Carbon\Carbon::parse($cotizacion->fecha_ideal)->format('d/m/Y') : '—' }}

---

## Documentos Adjuntos

Este correo incluye dos documentos:

1. **CartaPorte_{{ $folio }}.docx** - Carta Porte completa con inventario y costos
2. **AnexoFotografico_{{ $folio }}.pdf** - Fotografías y resumen visual

---

## Acciones Recomendadas

1. Revisar los documentos adjuntos
2. Validar los costos calculados
3. Contactar al cliente si es necesario
4. Confirmar o ajustar la cotización en el panel administrativo

---

**Sistema de Cotización FletesCun**  
Generado automáticamente: {{ now()->format('d/m/Y H:i:s') }}
@endcomponent
