# Contrato del Servicio - Equipo D
**Nombre:** GenerarComprobanteElectrónico
**Versión:** 1.0
**Endpoint:** POST /generar_comprobante.php
**Tipo de servicio:** Proceso (Asíncrono)

**Recibe (Formato de entrada): JSON**
```json
{
  "id_pedido": 1024,
  "ruc_cliente": "20123456789",
  "total_pagado": 150.50,
  "producto_comprados": [
    {"id_producto": 1, "cantidad": 2}
  ]
}

{
  "id_comprobante": "F001-4521",
  "estado_emision": "Procesando",
  "fecha_emision": "2026-08-28 16:30:00"
}

{
  "error": "Faltan parámetros obligatorios",
  "codigo": 400
}

{
  "error": "El id_pedido no existe",
  "codigo": 404
}