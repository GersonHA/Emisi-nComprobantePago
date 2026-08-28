<?php
/**
 * index.php — API de comprobantes (PHP vanilla, sin framework, sin BD).
 *
 * Levantar el servidor de desarrollo:
 *   php -S 127.0.0.1:8000 index.php
 *
 * Endpoints:
 *   GET    /                       -> info del servicio
 *   GET    /comprobantes           -> listar todos
 *   GET    /comprobantes/{id}      -> obtener uno
 *   POST   /comprobantes           -> crear
 *   PUT    /comprobantes/{id}      -> reemplazar
 *   DELETE /comprobantes/{id}      -> eliminar
 *
 * Todos los request/response son application/json.
 */

declare(strict_types=1);

/**
 * Tasa del IGV (Impuesto General a las Ventas) en Perú.
 * El 18% se aplica sobre la base gravable (subtotal - descuento).
 */
const IGV_RATE = 0.18;

require_once __DIR__ . '/storage.php';

// ──────────────────────────────────────────────────────────────────────
// Headers
// ──────────────────────────────────────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ──────────────────────────────────────────────────────────────────────
// Parsing de la URI
// ──────────────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$uri    = '/' . trim($uri, '/');

// Si la URI trae el nombre del script (ej: /index.php/comprobantes), lo limpiamos
$script = basename($_SERVER['SCRIPT_NAME'] ?? '');
if ($script !== '' && str_ends_with($uri, '/' . $script)) {
    $uri = substr($uri, 0, -strlen('/' . $script));
    if ($uri === '') {
        $uri = '/';
    }
}

$segments = array_values(array_filter(explode('/', $uri), fn($s) => $s !== ''));
$resource = $segments[0] ?? '';
$id_raw   = $segments[1] ?? null;

// Rutas extra (más de /recurso/id) → 404
if (count($segments) > 2) {
    respond(404, ['error' => 'Ruta no encontrada', 'uri' => $uri]);
}

// ──────────────────────────────────────────────────────────────────────
// Despacho
// ──────────────────────────────────────────────────────────────────────
try {
    if ($resource === '' || $resource === 'index.php') {
        info_servicio();
    }

    if ($resource !== 'comprobantes') {
        respond(404, ['error' => 'Recurso no encontrado', 'recurso' => $resource]);
    }

    $id = null;
    if ($id_raw !== null) {
        if (!ctype_digit($id_raw)) {
            respond(400, ['error' => 'ID inválido', 'recibido' => $id_raw]);
        }
        $id = (int) $id_raw;
    }

    match (true) {
        $method === 'GET'    && $id === null => listar(),
        $method === 'GET'    && $id !== null => obtener($id),
        $method === 'POST'   && $id === null => crear(),
        $method === 'PUT'    && $id !== null => actualizar($id),
        $method === 'DELETE' && $id !== null => eliminar($id),
        default => respond(405, [
            'error'  => 'Método no permitido para esta ruta',
            'metodo' => $method,
            'ruta'   => $uri,
        ]),
    };
} catch (InvalidArgumentException $e) {
    respond(422, ['error' => $e->getMessage()]);
} catch (Throwable $e) {
    respond(500, ['error' => 'Error interno del servidor', 'detalle' => $e->getMessage()]);
}

// ──────────────────────────────────────────────────────────────────────
// Handlers
// ──────────────────────────────────────────────────────────────────────

function info_servicio(): void
{
    respond(200, [
        'servicio' => 'API de Comprobantes',
        'version'  => '1.0.0',
        'endpoints' => [
            'GET    /',
            'GET    /comprobantes',
            'GET    /comprobantes/{id}',
            'POST   /comprobantes',
            'PUT    /comprobantes/{id}',
            'DELETE /comprobantes/{id}',
        ],
        'nota' => 'Los datos viven en memoria. Se reinician al detener el servidor.',
    ]);
}

function listar(): void
{
    $comprobantes = all_comprobantes();
    respond(200, [
        'total'        => count($comprobantes),
        'comprobantes' => $comprobantes,
    ]);
}

function obtener(int $id): void
{
    $c = find_comprobante($id);
    if ($c === null) {
        respond(404, ['error' => 'Comprobante no encontrado', 'id' => $id]);
    }
    respond(200, $c);
}

function crear(): void
{
    $input = read_json();
    $data  = build_comprobante($input);

    if (exists_serie_numero($data['serie'], $data['numero'])) {
        respond(409, [
            'error' => 'Ya existe un comprobante con esa serie y número',
            'serie' => $data['serie'],
            'numero' => $data['numero'],
        ]);
    }

    $data['id']    = next_id();
    $data['fecha'] = date('c'); // ISO 8601, automático
    $data = reorder_with_id_fecha_first($data);
    add_comprobante($data);
    respond(201, $data);
}

function actualizar(int $id): void
{
    $existente = find_comprobante($id);
    if ($existente === null) {
        respond(404, ['error' => 'Comprobante no encontrado', 'id' => $id]);
    }
    $input = read_json();
    $data  = build_comprobante($input);

    if (exists_serie_numero($data['serie'], $data['numero'], exclude_id: $id)) {
        respond(409, [
            'error' => 'Ya existe otro comprobante con esa serie y número',
            'serie' => $data['serie'],
            'numero' => $data['numero'],
        ]);
    }

    $data['id']    = $id;
    $data['fecha'] = $existente['fecha']; // la fecha original se preserva
    $data = reorder_with_id_fecha_first($data);
    $guardado = replace_comprobante($id, $data);
    respond(200, $guardado);
}

/**
 * Reordena el array del comprobante para que `id` y `fecha` queden al inicio.
 */
function reorder_with_id_fecha_first(array $data): array
{
    $id    = $data['id']    ?? null;
    $fecha = $data['fecha'] ?? null;
    unset($data['id'], $data['fecha']);
    $head = array_filter(['id' => $id, 'fecha' => $fecha], fn($v) => $v !== null);
    return $head + $data;
}

function eliminar(int $id): void
{
    $existente = find_comprobante($id);
    if ($existente === null) {
        respond(404, ['error' => 'Comprobante no encontrado', 'id' => $id]);
    }
    delete_comprobante($id);
    respond(200, [
        'ok'      => true,
        'id'      => $id,
        'mensaje' => 'Comprobante eliminado',
    ]);
}

// ──────────────────────────────────────────────────────────────────────
// Construcción + validación de un comprobante
// ──────────────────────────────────────────────────────────────────────

function build_comprobante(array $input): array
{
    foreach (['serie', 'numero', 'vendedor', 'cliente', 'items', 'metodo_pago'] as $campo) {
        if (!array_key_exists($campo, $input)) {
            throw new InvalidArgumentException("Falta el campo requerido: $campo");
        }
    }

    // serie
    $serie = strtoupper(trim((string) $input['serie']));
    if ($serie === '') {
        throw new InvalidArgumentException('serie no puede estar vacía');
    }

    // numero
    $numero = $input['numero'];
    if (!is_int($numero) && !(is_string($numero) && ctype_digit($numero))) {
        throw new InvalidArgumentException('numero debe ser un entero positivo');
    }
    $numero = (int) $numero;
    if ($numero <= 0) {
        throw new InvalidArgumentException('numero debe ser mayor a 0');
    }

    // vendedor
    $vendedor = build_vendedor($input['vendedor']);

    // cliente
    $cliente = build_cliente($input['cliente']);

    // items
    $items = build_items($input['items']);

    // cálculos del servidor (incluyen IGV)
    // El precio_unitario de los items incluye IGV, así que la suma de los items
    // es el total BRUTO (lo que paga el cliente). El subtotal del comprobante
    // es la base gravable (sin IGV) y se obtiene dividiendo entre 1.18.
    $items_gross   = array_sum(array_map(fn($i) => $i['precio_total'], $items));
    $descuento     = isset($input['descuento']) ? (float) $input['descuento'] : 0.0;
    if ($descuento < 0) {
        $descuento = 0.0;
    }
    $descuento     = min($descuento, $items_gross);
    $total_gravable = $items_gross - $descuento;
    $subtotal      = round($total_gravable / (1 + IGV_RATE), 2);
    $igv           = round($total_gravable - $subtotal, 2);
    $total         = round($subtotal + $igv, 2);

    // metodo_pago
    $metodos_validos = ['EFECTIVO', 'TARJETA', 'YAPE', 'PLIN', 'TRANSFERENCIA'];
    $metodo_pago = strtoupper(trim((string) $input['metodo_pago']));
    if (!in_array($metodo_pago, $metodos_validos, true)) {
        throw new InvalidArgumentException(
            'metodo_pago inválido. Usa uno de: ' . implode(', ', $metodos_validos)
        );
    }

    return [
        'serie'       => $serie,
        'numero'      => $numero,
        'vendedor'    => $vendedor,
        'cliente'     => $cliente,
        'items'       => $items,
        'descuento'   => round($descuento, 2),
        'metodo_pago' => $metodo_pago,
        'subtotal'    => $subtotal,    // base gravable (sin IGV)
        'igv'         => $igv,         // 18% de la base gravable
        'total'       => $total,       // lo que paga el cliente (con IGV)
    ];
}

function build_vendedor(array $v): array
{
    $nombre = trim((string) ($v['nombre'] ?? ''));
    $codigo = trim((string) ($v['codigo'] ?? ''));
    if ($nombre === '' || $codigo === '') {
        throw new InvalidArgumentException('vendedor requiere nombre y codigo');
    }
    return ['nombre' => $nombre, 'codigo' => $codigo];
}

function build_cliente(array $c): array
{
    $tipos_validos = ['RUC', 'DNI', 'CE', 'ND'];
    $tipo = strtoupper(trim((string) ($c['tipo_documento'] ?? '')));
    if (!in_array($tipo, $tipos_validos, true)) {
        throw new InvalidArgumentException(
            'cliente.tipo_documento debe ser uno de: ' . implode(', ', $tipos_validos)
        );
    }

    $numero  = trim((string) ($c['numero_documento'] ?? ''));
    $cliente = trim((string) ($c['Cliente'] ?? ''));

    // Para ND, si el cliente no envía documento o nombre, el server rellena
    // con valores por defecto representativos.
    if ($tipo === 'ND') {
        if ($numero === '')  $numero  = '999999999';
        if ($cliente === '') $cliente = 'Clientes-Varios';
    } else {
        if ($numero === '') {
            throw new InvalidArgumentException(
                'cliente.numero_documento es obligatorio cuando tipo_documento no es ND'
            );
        }
        if ($cliente === '') {
            throw new InvalidArgumentException('cliente.Cliente es obligatorio');
        }
    }

    return [
        'tipo_documento'   => $tipo,
        'numero_documento' => $numero,
        'Cliente'          => $cliente,
    ];
}

function build_items(array $items): array
{
    if (empty($items)) {
        throw new InvalidArgumentException('items debe contener al menos un ítem');
    }
    $out = [];
    foreach ($items as $idx => $it) {
        if (!is_array($it)) {
            throw new InvalidArgumentException("items[$idx] debe ser un objeto");
        }
        $desc = trim((string) ($it['descripcion'] ?? ''));
        $cant = $it['cantidad'] ?? null;
        $pu   = $it['precio_unitario'] ?? null;

        if ($desc === '') {
            throw new InvalidArgumentException("items[$idx].descripcion es obligatoria");
        }
        if (!is_numeric($cant) || (float) $cant <= 0) {
            throw new InvalidArgumentException("items[$idx].cantidad debe ser un número > 0");
        }
        if (!is_numeric($pu) || (float) $pu < 0) {
            throw new InvalidArgumentException("items[$idx].precio_unitario debe ser un número >= 0");
        }
        $cant = (float) $cant;
        $pu   = (float) $pu;
        $out[] = [
            'descripcion'     => $desc,
            'cantidad'        => $cant,
            'precio_unitario' => $pu,
            'precio_total'    => round($cant * $pu, 2),
        ];
    }
    return $out;
}

// ──────────────────────────────────────────────────────────────────────
// Helpers
// ──────────────────────────────────────────────────────────────────────

function read_json(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        throw new InvalidArgumentException('Cuerpo de la petición vacío');
    }
    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new InvalidArgumentException('JSON inválido: ' . json_last_error_msg());
    }
    if (!is_array($data)) {
        throw new InvalidArgumentException('JSON debe ser un objeto o arreglo');
    }
    return $data;
}

function respond(int $code, $data): void
{
    http_response_code($code);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
