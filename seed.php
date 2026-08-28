<?php
/**
 * seed.php
 * Crea 15 comprobantes de ejemplo (8 boletas + 7 facturas) en data.json.
 * Seguro de re-ejecutar: si un (serie, numero) ya existe, lo salta.
 *
 * Uso:
 *   php seed.php
 */

declare(strict_types=1);

const IGV_RATE = 0.18;

require_once __DIR__ . '/storage.php';

$comprobantes = [
    // ═══════════════════ BOLETAS (B001-1 a B001-8) ═══════════════════

    // 1) 3 ítems, cliente eventual (VARIOS / ND), YAPE
    [
        'serie' => 'B001', 'numero' => 1,
        'vendedor' => ['nombre' => 'Juan Pérez', 'codigo' => 'V001'],
        'cliente'  => ['tipo_documento' => 'ND', 'numero_documento' => '999999999', 'Cliente' => 'Clientes-Varios'],
        'items' => [
            ['descripcion' => 'Coca Cola 500ml',  'cantidad' => 2, 'precio_unitario' => 5.50],
            ['descripcion' => 'Papas Lays 100g',  'cantidad' => 1, 'precio_unitario' => 3.50],
            ['descripcion' => 'Pan ciabatta',     'cantidad' => 3, 'precio_unitario' => 3.00],
        ],
        'descuento' => 0.00, 'metodo_pago' => 'YAPE',
    ],

    // 2) 5 ítems, persona DNI, EFECTIVO, con descuento
    [
        'serie' => 'B001', 'numero' => 2,
        'vendedor' => ['nombre' => 'María López', 'codigo' => 'V002'],
        'cliente'  => ['tipo_documento' => 'DNI', 'numero_documento' => '45678901', 'Cliente' => 'Carlos Ramírez Torres'],
        'items' => [
            ['descripcion' => 'Arroz 1kg',       'cantidad' => 2, 'precio_unitario' => 7.00],
            ['descripcion' => 'Aceite 1L',       'cantidad' => 1, 'precio_unitario' => 12.00],
            ['descripcion' => 'Azúcar 1kg',      'cantidad' => 1, 'precio_unitario' => 5.50],
            ['descripcion' => 'Fideos 500g',     'cantidad' => 3, 'precio_unitario' => 2.00],
            ['descripcion' => 'Atún en lata',    'cantidad' => 4, 'precio_unitario' => 4.00],
        ],
        'descuento' => 5.00, 'metodo_pago' => 'EFECTIVO',
    ],

    // 3) 8 ítems, persona DNI, PLIN
    [
        'serie' => 'B001', 'numero' => 3,
        'vendedor' => ['nombre' => 'Ana Torres', 'codigo' => 'V003'],
        'cliente'  => ['tipo_documento' => 'DNI', 'numero_documento' => '12345678', 'Cliente' => 'Lucía Mendoza Ríos'],
        'items' => [
            ['descripcion' => 'Inca Kola 500ml',   'cantidad' => 4, 'precio_unitario' => 5.50],
            ['descripcion' => 'Sprite 500ml',      'cantidad' => 2, 'precio_unitario' => 5.00],
            ['descripcion' => 'Galletas Morochas', 'cantidad' => 3, 'precio_unitario' => 2.50],
            ['descripcion' => 'Chocolate Sublime', 'cantidad' => 5, 'precio_unitario' => 2.50],
            ['descripcion' => 'Caramelos Halls',   'cantidad' => 2, 'precio_unitario' => 1.00],
            ['descripcion' => 'Chicles Trident',   'cantidad' => 4, 'precio_unitario' => 1.00],
            ['descripcion' => 'Agua San Luis 625ml','cantidad' => 6, 'precio_unitario' => 2.50],
            ['descripcion' => 'Mentas Eclipse',    'cantidad' => 2, 'precio_unitario' => 1.25],
        ],
        'descuento' => 0.00, 'metodo_pago' => 'PLIN',
    ],

    // 4) 4 ítems, persona CE, TARJETA
    [
        'serie' => 'B001', 'numero' => 4,
        'vendedor' => ['nombre' => 'Carlos Mendoza', 'codigo' => 'V004'],
        'cliente'  => ['tipo_documento' => 'CE', 'numero_documento' => '001234567', 'Cliente' => 'John Smith'],
        'items' => [
            ['descripcion' => 'Licencia Office 365 Personal', 'cantidad' => 1, 'precio_unitario' => 350.00],
            ['descripcion' => 'Mouse Logitech M170',          'cantidad' => 1, 'precio_unitario' => 89.90],
            ['descripcion' => 'Cable HDMI 2m',                'cantidad' => 2, 'precio_unitario' => 15.00],
            ['descripcion' => 'USB Kingston 32GB',            'cantidad' => 1, 'precio_unitario' => 35.00],
        ],
        'descuento' => 0.00, 'metodo_pago' => 'TARJETA',
    ],

    // 5) 10 ítems, VARIOS (cafetería), EFECTIVO, con descuento
    [
        'serie' => 'B001', 'numero' => 5,
        'vendedor' => ['nombre' => 'Rosa García', 'codigo' => 'V005'],
        'cliente'  => ['tipo_documento' => 'ND', 'numero_documento' => '999999999', 'Cliente' => 'Clientes-Varios'],
        'items' => [
            ['descripcion' => 'Sándwich de jamón',     'cantidad' => 2, 'precio_unitario' => 12.00],
            ['descripcion' => 'Café americano',        'cantidad' => 2, 'precio_unitario' => 7.00],
            ['descripcion' => 'Jugo de naranja',       'cantidad' => 2, 'precio_unitario' => 8.00],
            ['descripcion' => 'Queque de vainilla',    'cantidad' => 1, 'precio_unitario' => 8.00],
            ['descripcion' => 'Alfajor',               'cantidad' => 3, 'precio_unitario' => 3.00],
            ['descripcion' => 'Brownie de chocolate',  'cantidad' => 2, 'precio_unitario' => 6.00],
            ['descripcion' => 'Galleta con chispas',   'cantidad' => 4, 'precio_unitario' => 3.00],
            ['descripcion' => 'Empanada de pollo',     'cantidad' => 3, 'precio_unitario' => 5.00],
            ['descripcion' => 'Té helado',             'cantidad' => 2, 'precio_unitario' => 7.00],
            ['descripcion' => 'Pie de manzana',        'cantidad' => 1, 'precio_unitario' => 10.00],
        ],
        'descuento' => 15.00, 'metodo_pago' => 'EFECTIVO',
    ],

    // 6) 6 ítems, persona DNI, TRANSFERENCIA
    [
        'serie' => 'B001', 'numero' => 6,
        'vendedor' => ['nombre' => 'Juan Pérez', 'codigo' => 'V001'],
        'cliente'  => ['tipo_documento' => 'DNI', 'numero_documento' => '78945612', 'Cliente' => 'Patricia Vega Salas'],
        'items' => [
            ['descripcion' => 'Cuaderno universitario 100h', 'cantidad' => 4, 'precio_unitario' => 6.00],
            ['descripcion' => 'Lapicero Pilot azul',        'cantidad' => 6, 'precio_unitario' => 1.50],
            ['descripcion' => 'Lápiz Faber negro',          'cantidad' => 5, 'precio_unitario' => 1.00],
            ['descripcion' => 'Borrador blanco',            'cantidad' => 3, 'precio_unitario' => 0.50],
            ['descripcion' => 'Tajador metálico',           'cantidad' => 2, 'precio_unitario' => 0.50],
            ['descripcion' => 'Regla 30cm',                 'cantidad' => 1, 'precio_unitario' => 2.50],
        ],
        'descuento' => 0.00, 'metodo_pago' => 'TRANSFERENCIA',
    ],

    // 7) 3 ítems, VARIOS, YAPE
    [
        'serie' => 'B001', 'numero' => 7,
        'vendedor' => ['nombre' => 'María López', 'codigo' => 'V002'],
        'cliente'  => ['tipo_documento' => 'ND', 'numero_documento' => '999999999', 'Cliente' => 'Clientes-Varios'],
        'items' => [
            ['descripcion' => 'Impresión B/N A4', 'cantidad' => 50, 'precio_unitario' => 0.50],
            ['descripcion' => 'Anillado',         'cantidad' => 1,  'precio_unitario' => 8.00],
            ['descripcion' => 'Empastado',        'cantidad' => 1,  'precio_unitario' => 35.00],
        ],
        'descuento' => 0.00, 'metodo_pago' => 'YAPE',
    ],

    // 8) 12 ítems, persona DNI, EFECTIVO, con descuento
    [
        'serie' => 'B001', 'numero' => 8,
        'vendedor' => ['nombre' => 'Ana Torres', 'codigo' => 'V003'],
        'cliente'  => ['tipo_documento' => 'DNI', 'numero_documento' => '55667788', 'Cliente' => 'Miguel Sánchez Quispe'],
        'items' => [
            ['descripcion' => 'Detergente Ace 1kg',         'cantidad' => 2, 'precio_unitario' => 18.00],
            ['descripcion' => 'Lavavajillas Ayudín 500g',   'cantidad' => 1, 'precio_unitario' => 12.00],
            ['descripcion' => 'Esponja Scotch-Brite',       'cantidad' => 4, 'precio_unitario' => 2.50],
            ['descripcion' => 'Papel higiénico 4 rollos',   'cantidad' => 3, 'precio_unitario' => 8.00],
            ['descripcion' => 'Jabón líquido 500ml',        'cantidad' => 2, 'precio_unitario' => 10.00],
            ['descripcion' => 'Shampoo Head & Shoulders',   'cantidad' => 1, 'precio_unitario' => 25.00],
            ['descripcion' => 'Acondicionador Sedal',       'cantidad' => 1, 'precio_unitario' => 22.00],
            ['descripcion' => 'Pasta dental Colgate',       'cantidad' => 2, 'precio_unitario' => 8.00],
            ['descripcion' => 'Cepillo dental',             'cantidad' => 3, 'precio_unitario' => 4.00],
            ['descripcion' => 'Desodorante Rexona',         'cantidad' => 2, 'precio_unitario' => 12.00],
            ['descripcion' => 'Toallas húmedas 50u',        'cantidad' => 1, 'precio_unitario' => 15.00],
            ['descripcion' => 'Ambientador Glade',          'cantidad' => 2, 'precio_unitario' => 14.00],
        ],
        'descuento' => 10.00, 'metodo_pago' => 'EFECTIVO',
    ],

    // ═══════════════════ FACTURAS (F001-1 a F001-7) ═══════════════════

    // 9) 7 ítems, EMPRESA RUC, TRANSFERENCIA
    [
        'serie' => 'F001', 'numero' => 1,
        'vendedor' => ['nombre' => 'Juan Pérez', 'codigo' => 'V001'],
        'cliente'  => ['tipo_documento' => 'RUC', 'numero_documento' => '20123456789', 'Cliente' => 'Distribuidora ABC S.A.C.'],
        'items' => [
            ['descripcion' => 'Servicio de consultoría',     'cantidad' => 10, 'precio_unitario' => 80.00],
            ['descripcion' => 'Reunión de seguimiento',      'cantidad' => 4,  'precio_unitario' => 60.00],
            ['descripcion' => 'Informe ejecutivo',           'cantidad' => 1,  'precio_unitario' => 250.00],
            ['descripcion' => 'Presentación PowerPoint',     'cantidad' => 2,  'precio_unitario' => 90.00],
            ['descripcion' => 'Análisis de datos',           'cantidad' => 8,  'precio_unitario' => 70.00],
            ['descripcion' => 'Reporte mensual',             'cantidad' => 1,  'precio_unitario' => 180.00],
            ['descripcion' => 'Capacitación al equipo',      'cantidad' => 3,  'precio_unitario' => 120.00],
        ],
        'descuento' => 0.00, 'metodo_pago' => 'TRANSFERENCIA',
    ],

    // 10) 15 ítems, EMPRESA RUC, TARJETA, con descuento grande
    [
        'serie' => 'F001', 'numero' => 2,
        'vendedor' => ['nombre' => 'María López', 'codigo' => 'V002'],
        'cliente'  => ['tipo_documento' => 'RUC', 'numero_documento' => '20987654321', 'Cliente' => 'Tecnología del Sur S.R.L.'],
        'items' => [
            ['descripcion' => 'Laptop HP Pavilion 15"',      'cantidad' => 2, 'precio_unitario' => 2500.00],
            ['descripcion' => 'Monitor LG 24"',              'cantidad' => 4, 'precio_unitario' => 650.00],
            ['descripcion' => 'Teclado mecánico',            'cantidad' => 4, 'precio_unitario' => 180.00],
            ['descripcion' => 'Mouse inalámbrico',           'cantidad' => 4, 'precio_unitario' => 85.00],
            ['descripcion' => 'Webcam Logitech C920',        'cantidad' => 4, 'precio_unitario' => 320.00],
            ['descripcion' => 'Auriculares con micrófono',   'cantidad' => 4, 'precio_unitario' => 220.00],
            ['descripcion' => 'Hub USB-C 7 puertos',         'cantidad' => 3, 'precio_unitario' => 150.00],
            ['descripcion' => 'Cable de red Cat6 5m',        'cantidad' => 6, 'precio_unitario' => 25.00],
            ['descripcion' => 'Adaptador HDMI-VGA',          'cantidad' => 4, 'precio_unitario' => 35.00],
            ['descripcion' => 'Disco SSD 1TB',               'cantidad' => 3, 'precio_unitario' => 480.00],
            ['descripcion' => 'Memoria RAM 16GB DDR4',       'cantidad' => 4, 'precio_unitario' => 320.00],
            ['descripcion' => 'Licencia Windows 11 Pro',     'cantidad' => 4, 'precio_unitario' => 480.00],
            ['descripcion' => 'Licencia Microsoft 365',      'cantidad' => 4, 'precio_unitario' => 380.00],
            ['descripcion' => 'Mochila para laptop',         'cantidad' => 4, 'precio_unitario' => 150.00],
            ['descripcion' => 'Base refrigerante',           'cantidad' => 4, 'precio_unitario' => 95.00],
        ],
        'descuento' => 500.00, 'metodo_pago' => 'TARJETA',
    ],

    // 11) 9 ítems, EMPRESA RUC, TRANSFERENCIA
    [
        'serie' => 'F001', 'numero' => 3,
        'vendedor' => ['nombre' => 'Ana Torres', 'codigo' => 'V003'],
        'cliente'  => ['tipo_documento' => 'RUC', 'numero_documento' => '20456789123', 'Cliente' => 'Servicios Generales XYZ E.I.R.L.'],
        'items' => [
            ['descripcion' => 'Limpieza profunda oficina 200m²', 'cantidad' => 1, 'precio_unitario' => 350.00],
            ['descripcion' => 'Limpieza de ventanas',            'cantidad' => 8, 'precio_unitario' => 25.00],
            ['descripcion' => 'Desinfección de ambientes',       'cantidad' => 1, 'precio_unitario' => 200.00],
            ['descripcion' => 'Limpieza de alfombras',           'cantidad' => 3, 'precio_unitario' => 80.00],
            ['descripcion' => 'Suministro de papel toalla',      'cantidad' => 12, 'precio_unitario' => 15.00],
            ['descripcion' => 'Jabón líquido antibacterial',     'cantidad' => 6, 'precio_unitario' => 18.00],
            ['descripcion' => 'Ambientador automático',          'cantidad' => 2, 'precio_unitario' => 65.00],
            ['descripcion' => 'Mantenimiento de pisos',          'cantidad' => 1, 'precio_unitario' => 280.00],
            ['descripcion' => 'Personal adicional fin de semana','cantidad' => 2, 'precio_unitario' => 150.00],
        ],
        'descuento' => 0.00, 'metodo_pago' => 'TRANSFERENCIA',
    ],

    // 12) 4 ítems, EMPRESA RUC, TARJETA
    [
        'serie' => 'F001', 'numero' => 4,
        'vendedor' => ['nombre' => 'Carlos Mendoza', 'codigo' => 'V004'],
        'cliente'  => ['tipo_documento' => 'RUC', 'numero_documento' => '20555111222', 'Cliente' => 'Consultora Lima S.A.'],
        'items' => [
            ['descripcion' => 'Auditoría contable',              'cantidad' => 1, 'precio_unitario' => 3500.00],
            ['descripcion' => 'Revisión de estados financieros', 'cantidad' => 1, 'precio_unitario' => 1800.00],
            ['descripcion' => 'Dictamen profesional',           'cantidad' => 1, 'precio_unitario' => 1200.00],
            ['descripcion' => 'Reunión de cierre',               'cantidad' => 4, 'precio_unitario' => 250.00],
        ],
        'descuento' => 0.00, 'metodo_pago' => 'TARJETA',
    ],

    // 13) 11 ítems, EMPRESA RUC, TRANSFERENCIA, con descuento
    [
        'serie' => 'F001', 'numero' => 5,
        'vendedor' => ['nombre' => 'Rosa García', 'codigo' => 'V005'],
        'cliente'  => ['tipo_documento' => 'RUC', 'numero_documento' => '20333444555', 'Cliente' => 'Restaurant El Sabor S.A.C.'],
        'items' => [
            ['descripcion' => 'Pollo entero',         'cantidad' => 15, 'precio_unitario' => 18.00],
            ['descripcion' => 'Arroz 5kg',            'cantidad' => 4,  'precio_unitario' => 32.00],
            ['descripcion' => 'Papa amarilla 1kg',    'cantidad' => 10, 'precio_unitario' => 5.00],
            ['descripcion' => 'Huevos 12u',           'cantidad' => 6,  'precio_unitario' => 12.00],
            ['descripcion' => 'Aceite 5L',            'cantidad' => 3,  'precio_unitario' => 55.00],
            ['descripcion' => 'Cebolla 1kg',          'cantidad' => 8,  'precio_unitario' => 3.50],
            ['descripcion' => 'Tomate 1kg',           'cantidad' => 8,  'precio_unitario' => 5.00],
            ['descripcion' => 'Limón 1kg',            'cantidad' => 6,  'precio_unitario' => 6.00],
            ['descripcion' => 'Ajo 1kg',              'cantidad' => 2,  'precio_unitario' => 18.00],
            ['descripcion' => 'Sal 1kg',              'cantidad' => 4,  'precio_unitario' => 2.50],
            ['descripcion' => 'Comino molido 100g',   'cantidad' => 5,  'precio_unitario' => 4.50],
        ],
        'descuento' => 50.00, 'metodo_pago' => 'TRANSFERENCIA',
    ],

    // 14) 6 ítems, EMPRESA RUC, TARJETA
    [
        'serie' => 'F001', 'numero' => 6,
        'vendedor' => ['nombre' => 'Juan Pérez', 'codigo' => 'V001'],
        'cliente'  => ['tipo_documento' => 'RUC', 'numero_documento' => '20666777888', 'Cliente' => 'Publicidad Creativa S.R.L.'],
        'items' => [
            ['descripcion' => 'Diseño de logo',                'cantidad' => 1, 'precio_unitario' => 800.00],
            ['descripcion' => 'Manual de marca',               'cantidad' => 1, 'precio_unitario' => 1500.00],
            ['descripcion' => 'Tarjetas de presentación 1000u','cantidad' => 2, 'precio_unitario' => 180.00],
            ['descripcion' => 'Diseño de brochure',            'cantidad' => 1, 'precio_unitario' => 450.00],
            ['descripcion' => 'Banner publicitario 2x1m',      'cantidad' => 3, 'precio_unitario' => 120.00],
            ['descripcion' => 'Gestión de redes sociales mensual','cantidad' => 1, 'precio_unitario' => 2200.00],
        ],
        'descuento' => 0.00, 'metodo_pago' => 'TARJETA',
    ],

    // 15) 13 ítems, EMPRESA RUC, TRANSFERENCIA, con descuento grande
    [
        'serie' => 'F001', 'numero' => 7,
        'vendedor' => ['nombre' => 'María López', 'codigo' => 'V002'],
        'cliente'  => ['tipo_documento' => 'RUC', 'numero_documento' => '20777888999', 'Cliente' => 'Constructora Andina S.A.C.'],
        'items' => [
            ['descripcion' => 'Cemento 1 bolsa',         'cantidad' => 30, 'precio_unitario' => 28.00],
            ['descripcion' => 'Fierro 1/2" 9m',          'cantidad' => 20, 'precio_unitario' => 35.00],
            ['descripcion' => 'Arena gruesa m³',         'cantidad' => 4,  'precio_unitario' => 80.00],
            ['descripcion' => 'Piedra chancada m³',      'cantidad' => 3,  'precio_unitario' => 90.00],
            ['descripcion' => 'Ladrillo king kong 18h',  'cantidad' => 500,'precio_unitario' => 1.20],
            ['descripcion' => 'Mayólica 30x30 m²',       'cantidad' => 25, 'precio_unitario' => 35.00],
            ['descripcion' => 'Pintura látex 4GL',       'cantidad' => 8,  'precio_unitario' => 95.00],
            ['descripcion' => 'Tubo PVC 4" 3m',          'cantidad' => 12, 'precio_unitario' => 25.00],
            ['descripcion' => 'Cables eléctricos 12 AWG m','cantidad' => 50,'precio_unitario' => 3.50],
            ['descripcion' => 'Caja de breakers',        'cantidad' => 2,  'precio_unitario' => 180.00],
            ['descripcion' => 'Tornillos autoroscantes 100u','cantidad' => 5, 'precio_unitario' => 35.00],
            ['descripcion' => 'Lija de agua 50u',        'cantidad' => 2,  'precio_unitario' => 28.00],
            ['descripcion' => 'Silicona transparente',   'cantidad' => 6,  'precio_unitario' => 18.00],
        ],
        'descuento' => 200.00, 'metodo_pago' => 'TRANSFERENCIA',
    ],
];

// ═══════════════════ Inserción ═══════════════════

$added   = 0;
$skipped = 0;

foreach ($comprobantes as $idx => $c) {
    // Calcular precio_total de cada ítem
    foreach ($c['items'] as &$it) {
        $it['precio_total'] = round(((float)$it['cantidad']) * ((float)$it['precio_unitario']), 2);
    }
    unset($it);

    $items_gross    = array_sum(array_column($c['items'], 'precio_total'));
    $descuento      = min((float)($c['descuento'] ?? 0), $items_gross);
    $total_gravable = $items_gross - $descuento;
    $subtotal       = round($total_gravable / (1 + IGV_RATE), 2);
    $igv            = round($total_gravable - $subtotal, 2);
    $total          = round($subtotal + $igv, 2);

    $serie  = strtoupper((string)$c['serie']);
    $numero = (int)$c['numero'];

    if (exists_serie_numero($serie, $numero)) {
        $skipped++;
        continue;
    }

    // Armamos el comprobante con id y fecha al inicio
    $id    = next_id();
    $fecha = date('c', time() - ((count($comprobantes) - $idx) * 4 * 3600));

    $c = [
        'id'         => $id,
        'fecha'      => $fecha,
        'serie'      => $serie,
        'numero'     => $numero,
        'vendedor'   => $c['vendedor'],
        'cliente'    => $c['cliente'],
        'items'      => $c['items'],
        'descuento'  => round($descuento, 2),
        'metodo_pago' => $c['metodo_pago'],
        'subtotal'   => $subtotal,
        'igv'        => $igv,
        'total'      => $total,
    ];

    add_comprobante($c);
    $added++;
}

echo "═══════════════════════════════════════\n";
echo "   SEED DE COMPROBANTES COMPLETADO\n";
echo "═══════════════════════════════════════\n";
echo "  ✅ Agregados:        $added\n";
echo "  ⏭️  Ya existían:      $skipped\n";
echo "  📊 Total en sistema: " . count(all_comprobantes()) . "\n";
echo "═══════════════════════════════════════\n";
