<?php

require_once 'lib/autoloader.inc';
require_once 'lib/general.inc';

// Set up configuration from $main_config array.
if ( ! is_file(ROOT_PATH . '/config/config.inc')) {
    exit('Not configured.');
}
require_once 'config/config.inc';
Config::update($main_config);

ini_set('error_log', ROOT_PATH . 'logs/php-error.log');

// HTTPS is required by default, if https_required key is not found in config.
if (Config::get("https_required", true) && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off")) {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}

// Allow specific hosts to access the API. (localhost React app for example.)
if (isset($_SERVER['HTTP_ORIGIN']) && Config::get('is_dev_environment', false)) {
    $http_origin = $_SERVER['HTTP_ORIGIN'];
    $access_control_allowed_origins = Config::get('access_control_allowed_origins', []);
    if (in_array($http_origin, $access_control_allowed_origins)) {
        header("Access-Control-Allow-Origin: $http_origin");
    }
}

Db::init();
Router::init();

$subroute = Router::pop_next_subroute();
switch ($subroute) {
    case 'print_tree':
        print_tree();

        break;
    case 'get_tree':
        $data = get_parent_child_relations_and_root_ids();

        respond_success($data);

        break;
    case 'get_all':
        $rental_properties = get_all_rental_properties();

        respond_success($rental_properties);

        break;
    case 'get_relatives_of':
        $rental_property_id = request_param('rental_property_id');

        $sql = "SELECT
                    parent_id
                FROM parent_child_relations
                WHERE
                    child_id = ?
        ";
        $parent_ids = Db::execute($sql, [$rental_property_id], PDO::FETCH_COLUMN);

        $sibling_ids = [];
        if (count($parent_ids) > 0) {
            $sql = "SELECT
                        child_id
                    FROM parent_child_relations
                    WHERE
                        parent_id IN (" . implode(',', $parent_ids) . ")
                        AND child_id <> ?
            ";
            $sibling_ids = Db::execute($sql, [$rental_property_id], PDO::FETCH_COLUMN);
        }

        $sql = "SELECT
                    child_id
                FROM parent_child_relations
                WHERE
                    parent_id = ?
        ";
        $children_ids = Db::execute($sql, [$rental_property_id], PDO::FETCH_COLUMN);

        $rental_properties = get_all_rental_properties();

        $all_relatives = [];
        foreach ($parent_ids as $parent_id) {
            $all_relatives[] = [
                'title' => $rental_properties[$parent_id]['title'],
                'relation' => 'parent',
            ];
        }
        foreach ($sibling_ids as $sibling_id) {
            $all_relatives[] = [
                'title' => $rental_properties[$sibling_id]['title'],
                'relation' => 'sibling',
            ];
        }
        foreach ($children_ids as $child_id) {
            $all_relatives[] = [
                'title' => $rental_properties[$child_id]['title'],
                'relation' => 'child',
            ];
        }

        $all_relatives[] = [
            'title' => $rental_properties[$rental_property_id]['title'],
            'relation' => null,
        ];

        usort($all_relatives, function($relative_a, $relative_b) {
            return strcmp($relative_a['title'], $relative_b['title']);
        });

        respond_success($all_relatives);

        break;
    case 'create_new':
        should_be_post_request();
        $title = request_param('title');
        $shareable = request_param('shareable');

        if ( ! is_string($title)) {
            respond_error('Bad `title` param');
        }

        if ( ! (is_numeric($shareable) && ($shareable == 0 || $shareable == 1))) {
            respond_error('Bad "shareable" param');
        }

        $sql = "INSERT INTO rental_properties
                    (title, shareable)
                VALUES
                    (?, ?)
        ";
        Db::execute($sql, [$title, $shareable]);

        respond_success();

        break;
    case 'assign_parent':
        should_be_post_request();
        $rental_property_id = request_param('rental_property_id');
        $parent_id = request_param('parent_id');

        if ( ! is_numeric($rental_property_id)) {
            respond_error('Bad `rental_property_id` param');
        }
        $rental_property_id = (int) $rental_property_id;

        if ( ! is_numeric($parent_id)) {
            respond_error('Bad `parent_id` param');
        }
        $parent_id = (int) $parent_id;

        $rental_properties = get_all_rental_properties();
        // We don't care for all that if the rental property is shareable.
        if ($rental_properties[$rental_property_id]['shareable'] == 0) {
            // If the assignable parent is currently the descendant of the rental_property_id.
            if (is_ancestor_of($rental_property_id, $parent_id)) {

                // Find a current parent of the rental property.
                $sql = "SELECT
                            parent_id
                        FROM parent_child_relations
                        WHERE
                            child_id = ?
                ";
                $result = Db::execute($sql, [$rental_property_id]);
                $old_parent_id_of_rental_property = null;

                if (count($result) > 0) {
                    $result = $result[0];
                    $old_parent_id_of_rental_property = $result['parent_id'];
                }

                // Delete current parent dependencies for both.
                $sql = "DELETE FROM parent_child_relations
                        WHERE
                            child_id IN (?, ?)
                ";
                Db::execute($sql, [$parent_id, $rental_property_id]);

                // Put the new parent under the $old_parent_id_of_rental_property if such exists.
                if (is_numeric($old_parent_id_of_rental_property)) {
                    $sql = "INSERT INTO parent_child_relations
                                (parent_id, child_id)
                            VALUES
                                (?, ?)
                    ";
                    Db::execute($sql, [$old_parent_id_of_rental_property, $parent_id]);
                }
            } else {
                // Delete old dependency of the rental property.
                $sql = "DELETE FROM parent_child_relations
                        WHERE
                            child_id = ?
                ";
                Db::execute($sql, [$rental_property_id]);
            }
        }

        $sql = "INSERT INTO parent_child_relations
                    (parent_id, child_id)
                VALUES
                    (?, ?)
        ";
        Db::execute($sql, [$parent_id, $rental_property_id]);

        respond_success();

        break;
    default:
        echo 'API is wokring. This is the default API route.';

        break;
}
/**
 * Check if one rental property is an ancestor of another rental property.
 *
 * @param int $parent_id Possible ancestor ID.
 * @param int $child_id Possible descendant ID.
 *
 * @return bool `true` if $parent_id rental property is the ancestor
 *      of $child_id rental property, `false` otherwise.
 */
function is_ancestor_of($parent_id, $child_id) : bool
{
    $parent_child_relations_and_root_ids = get_parent_child_relations_and_root_ids();
    $parent_child_relations = $parent_child_relations_and_root_ids['parent_child_relations'];
    $traversed_ids = [];
    $ids_to_traverse = [];
    if (array_key_exists($parent_id, $parent_child_relations)) {
        $ids_to_traverse = $parent_child_relations[$parent_id];
    }

    while (count($ids_to_traverse) > 0) {

        $new_ids_to_traverse = [];
        foreach ($ids_to_traverse as $id) {
            if ($id == $child_id) {
                return true;
            }

            $traversed_ids[] = $id;

            if (array_key_exists($id, $parent_child_relations)) {
                $new_ids_to_traverse = array_merge($new_ids_to_traverse, $parent_child_relations[$id]);
            }
        }
        $ids_to_traverse = array_diff($new_ids_to_traverse, $traversed_ids);
    }

    return false;
}

/**
 * Get an array of all rental properties and their values.
 *
 * @return array Array of rental properties with
 *      rental property IDs as array keys.
 */
function get_all_rental_properties() : array
{
    $sql = "SELECT
                id,
                title,
                shareable
            FROM rental_properties
    ";
    return array_map('reset', Db::execute($sql, [], PDO::FETCH_GROUP));
}

/**
 * Get parent-child relations between rental properties and
 * also get root ids.
 *
 * @return array Array with keys:
 *
 * - 'root_ids': An array of root rental properties IDs.
 * - 'parent_child_relations': An array of parent-child relations,
 *      where keys (parent IDs) point to an array of their children IDs.
 */
function get_parent_child_relations_and_root_ids() : array
{
    $rental_properties = get_all_rental_properties();
    // Mark all existing rental properties as root objects (will filter out non-root objects further along).
    // Exclude shareable rental properties, those can not be root objects.
    $root_ids = [];
    foreach ($rental_properties as $rental_property_id => $rental_property_data) {
        if ($rental_property_data['shareable'] == 0) {
            $root_ids[] = $rental_property_id;
        }
    }

    $sql = "SELECT
                parent_id,
                child_id
            FROM parent_child_relations
    ";
    $parent_child_relations_raw = Db::execute($sql);

    // Construct an array of parent-child relations grouped by parent id.
    $parent_child_relations = [];
    foreach ($parent_child_relations_raw as $relation) {
        $parent_id = $relation['parent_id'];
        $child_id = $relation['child_id'];

        // Filter out non-root objects from $root_objects array.
        if (($key = array_search($child_id, $root_ids)) !== false) {
            unset($root_ids[$key]);
        }

        if (array_key_exists($parent_id, $parent_child_relations)) {
            $parent_child_relations[$parent_id][] = $child_id;
        } else {
            $parent_child_relations[$parent_id] = [$child_id];
        }
    }

    return [
        'root_ids' => array_values($root_ids),
        'parent_child_relations' => $parent_child_relations,
    ];
}

/**
 * `echo` out a tree of rental properties.
 */
function print_tree() : void
{
    $parent_child_relations_and_root_ids = get_parent_child_relations_and_root_ids();
    $parent_child_relations = $parent_child_relations_and_root_ids['parent_child_relations'];
    $root_ids = $parent_child_relations_and_root_ids['root_ids'];

    $rental_properties = get_all_rental_properties();

    foreach ($root_ids as $root_id) {
        print_children_tree_recursive($parent_child_relations, $rental_properties, $root_id);
    }
}

function print_children_tree_recursive($parent_child_relations, $rental_properties, $rental_property_id, $level = 1) : void
{
    echo str_repeat('—', $level) . ' ' . $rental_properties[$rental_property_id]['title'] . '<br>';
    if (array_key_exists($rental_property_id, $parent_child_relations)) {
        $children_ids = $parent_child_relations[$rental_property_id];
        foreach ($children_ids as $child_id) {
            print_children_tree_recursive($parent_child_relations, $rental_properties, $child_id, $level + 1);
        }
    }
}
