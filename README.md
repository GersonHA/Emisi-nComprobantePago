# Contrato del Servicio - Equipo D
**Nombre:** API de Comprobantes
**Versión:** 1.0.0
**Endpoints:** ver tabla abajo (recurso base: `/comprobantes`)
**Tipo de servicio:** REST (Síncrono). PHP vanilla, sin framework, sin base de datos (persistencia en `data.json`).

**Levantar el servidor de desarrollo:**
```
php -S 127.0.0.1:8000 index.php
```

| Método | Ruta                    | Descripción              |
|--------|-------------------------|--------------------------|
| GET    | /                       | Info del servicio        |
| GET    | /comprobantes           | Listar todos              |
| GET    | /comprobantes/{id}      | Obtener uno                |
| POST   | /comprobantes           | Crear                       |
| PUT    | /comprobantes/{id}      | Reemplazar                  |
| DELETE | /comprobantes/{id}      | Eliminar                    |

**POST /comprobantes — Recibe (Formato de entrada): JSON**
```json
{
  "serie": "F001",
  "numero": 1,
  "vendedor": { "nombre": "Juan Perez", "codigo": "V001" },
  "cliente": { "tipo_documento": "DNI", "numero_documento": "12345678", "nombre": "Cliente Test" },
  "items": [
    { "descripcion": "Producto A", "cantidad": 2, "precio_unitario": 10.5 }
  ],
  "metodo_pago": "YAPE"
}
```

**Respuesta exitosa (201 Created)**
```json
{
  "serie": "F001",
  "numero": 1,
  "vendedor": { "nombre": "Juan Perez", "codigo": "V001" },
  "cliente": { "tipo_documento": "DNI", "numero_documento": "12345678", "nombre": "Cliente Test" },
  "items": [
    { "descripcion": "Producto A", "cantidad": 2, "precio_unitario": 10.5, "subtotal": 21 }
  ],
  "subtotal": 21,
  "descuento": 0,
  "total": 21,
  "metodo_pago": "YAPE",
  "id": 1,
  "fecha": "2026-08-28T16:30:00+00:00"
}
```

**Errores**
```json
{ "error": "Falta el campo requerido: vendedor" }
```
_(422 Unprocessable Entity — validación de campos)_
```json
{ "error": "Comprobante no encontrado", "id": 999 }
```
_(404 Not Found — GET/PUT/DELETE sobre un id inexistente)_
```json
{ "error": "Ya existe un comprobante con esa serie y número", "serie": "F001", "numero": 1 }
```
_(409 Conflict — serie + número duplicados)_
```json
{ "error": "ID inválido", "recibido": "abc" }
```
_(400 Bad Request — el id de la ruta no es numérico)_
