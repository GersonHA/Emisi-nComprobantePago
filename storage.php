<?php
/**
 * storage.php
 * Capa de almacenamiento "en memoria" para la API de comprobantes.
 *
 * ⚠️ IMPORTANTE: el servidor de desarrollo de PHP (php -S) re-ejecuta
 * el script en cada request, por lo que una variable `static` dentro
 * de una función NO persiste entre requests.
 *
 * Para que los datos sobrevivan entre requests (y entre reinicios
 * del servidor) usamos un archivo JSON plano. NO es una base de
 * datos: no hay motor SQL, no hay servidor, no hay dependencias.
 * Solo un archivo que vive junto a index.php.
 *
 * Si quieres "memoria pura" (se pierde al apagar el server), comenta
 * las líneas marcadas con [FILE] y verás que los IDs se reinician en
 * cada request — no es viable para una API CRUD.
 */

declare(strict_types=1);

const DATA_FILE = __DIR__ . '/data.json'; // [FILE] ruta del archivo de datos

/**
 * Lee el archivo de datos. Si no existe, devuelve una estructura vacía.
 */
function load_data(): array
{
    if (!file_exists(DATA_FILE)) { // [FILE]
        return ['comprobantes' => [], 'next_id' => 1]; // [FILE]
    } // [FILE]
    $raw = file_get_contents(DATA_FILE); // [FILE]
    if ($raw === false || $raw === '') { // [FILE]
        return ['comprobantes' => [], 'next_id' => 1]; // [FILE]
    } // [FILE]
    $data = json_decode($raw, true); // [FILE]
    if (!is_array($data)) { // [FILE]
        return ['comprobantes' => [], 'next_id' => 1]; // [FILE]
    } // [FILE]
    if (!isset($data['comprobantes']) || !is_array($data['comprobantes'])) { // [FILE]
        $data['comprobantes'] = []; // [FILE]
    } // [FILE]
    if (!isset($data['next_id']) || !is_int($data['next_id'])) { // [FILE]
        $data['next_id'] = count($data['comprobantes']) + 1; // [FILE]
    } // [FILE]
    return $data; // [FILE]
}

/**
 * Persiste el estado actual al archivo JSON.
 */
function save_data(array $data): void
{
    $json = json_encode(
        $data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    file_put_contents(DATA_FILE, $json, LOCK_EX); // [FILE]
}

/**
 * Genera el siguiente ID disponible.
 */
function next_id(): int
{
    $data = load_data(); // [FILE]
    $id = $data['next_id']; // [FILE]
    $data['next_id'] = $id + 1; // [FILE]
    save_data($data); // [FILE]
    return $id; // [FILE]
}

/**
 * Busca un comprobante por ID. Devuelve null si no existe.
 */
function find_comprobante(int $id): ?array
{
    $data = load_data(); // [FILE]
    foreach ($data['comprobantes'] as $c) {
        if ($c['id'] === $id) {
            return $c;
        }
    }
    return null;
}

/**
 * Verifica si ya existe un comprobante con la misma (serie, numero).
 * $exclude_id permite ignorar el comprobante que se está editando.
 */
function exists_serie_numero(string $serie, int $numero, ?int $exclude_id = null): bool
{
    $data = load_data(); // [FILE]
    foreach ($data['comprobantes'] as $c) {
        if ($exclude_id !== null && $c['id'] === $exclude_id) {
            continue;
        }
        if ($c['serie'] === $serie && $c['numero'] === $numero) {
            return true;
        }
    }
    return false;
}

/**
 * Inserta un comprobante (ya con id y fecha asignados).
 */
function add_comprobante(array $comprobante): array
{
    $data = load_data(); // [FILE]
    $data['comprobantes'][] = $comprobante; // [FILE]
    save_data($data); // [FILE]
    return $comprobante;
}

/**
 * Reemplaza completamente un comprobante por ID.
 * Devuelve el comprobante nuevo, o null si no existe.
 */
function replace_comprobante(int $id, array $nuevo): ?array
{
    $data = load_data(); // [FILE]
    foreach ($data['comprobantes'] as $i => $c) { // [FILE]
        if ($c['id'] === $id) { // [FILE]
            $nuevo['id'] = $id; // [FILE]
            $data['comprobantes'][$i] = $nuevo; // [FILE]
            save_data($data); // [FILE]
            return $nuevo; // [FILE]
        } // [FILE]
    } // [FILE]
    return null;
}

/**
 * Elimina un comprobante por ID. Devuelve true si lo eliminó.
 */
function delete_comprobante(int $id): bool
{
    $data = load_data(); // [FILE]
    foreach ($data['comprobantes'] as $i => $c) { // [FILE]
        if ($c['id'] === $id) { // [FILE]
            array_splice($data['comprobantes'], $i, 1); // [FILE]
            save_data($data); // [FILE]
            return true; // [FILE]
        } // [FILE]
    } // [FILE]
    return false;
}

/**
 * Devuelve todos los comprobantes.
 */
function all_comprobantes(): array
{
    $data = load_data(); // [FILE]
    return array_values($data['comprobantes']); // [FILE]
}
