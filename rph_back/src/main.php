<?php

require_once 'lib/autoloader.inc';
require_once 'lib/general.inc';
require_once 'config.php';

ini_set('error_log', ROOT_PATH . 'logs/php-error.log');

// HTTPS is required by default.
if (Config::get("https_required", true) && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off")) {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}

Db::init();
Router::init();

$subroute = Router::pop_next_subroute();
switch ($subroute) {
    case 'show_all':
        $rental_properties = Db::select_and_group_by_first_param(['id', 'title', 'shareable'], 'rental_properties');
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
        $parent_child_relations = Db::execute($sql);

        // Construct an array of parent-child relations grouped by parent id.
        $parent_child_relations_grouped = [];
        foreach ($parent_child_relations as $relation) {
            $parent_id = $relation['parent_id'];
            $child_id = $relation['child_id'];

            // Filter out non-root objects from $root_objects array.
            if (($key = array_search($child_id, $root_ids)) !== false) {
                unset($root_ids[$key]);
            }

            if (array_key_exists($parent_id, $parent_child_relations_grouped)) {
                $parent_child_relations_grouped[$parent_id][] = $child_id;
            } else {
                $parent_child_relations_grouped[$parent_id] = [$child_id];
            }
        }
        foreach ($root_ids as $root_id) {
            print_tree_recursive($parent_child_relations_grouped, $rental_properties, $root_id);
        }

        // Acutally found out that if we execute
        // static::$_pdo->query("SELECT parent_id, child_id FROM parent_child_relations")->fetchAll(PDO::FETCH_GROUP);
        // inside the Db object, it will return pretty much the same $parent_child_relations_grouped array.
        // But as we still need to find out root IDs, I decided to leave the current implementation of grouping and filtration.

        // echo '<pre>';
        // print_r($parent_child_relations_grouped);
        // echo '</pre>';

        break;
    case 'show_relatives_of':
        $rental_property_id = request_param('rental_property_id');

        $sql = "SELECT
                    parent_id
                FROM parent_child_relations
                WHERE
                    child_id = ?
        ";
        $parent_ids = Db::execute($sql, [$rental_property_id], PDO::FETCH_COLUMN);

        $sql = "SELECT
                    child_id
                FROM parent_child_relations
                WHERE
                    parent_id IN (" . implode(',', $parent_ids) . ")
                    AND child_id <> ?
        ";
        $sibling_ids = Db::execute($sql, [$rental_property_id], PDO::FETCH_COLUMN);

        $sql = "SELECT
                    child_id
                FROM parent_child_relations
                WHERE
                    parent_id = ?
        ";
        $children_ids = Db::execute($sql, [$rental_property_id], PDO::FETCH_COLUMN);

        $rental_properties = Db::select_and_group_by_first_param(['id', 'title', 'shareable'], 'rental_properties');

        $all_relatives = [];
        foreach ($parent_ids as $parent_id) {
            $all_relatives[] = [
                'property' => $rental_properties[$parent_id]['title'],
                'relation' => 'parent',
            ];
        }
        foreach ($sibling_ids as $sibling_id) {
            $all_relatives[] = [
                'property' => $rental_properties[$sibling_id]['title'],
                'relation' => 'sibling',
            ];
        }
        foreach ($children_ids as $child_id) {
            $all_relatives[] = [
                'property' => $rental_properties[$child_id]['title'],
                'relation' => 'child',
            ];
        }

        $all_relatives[] = [
            'property' => $rental_properties[$rental_property_id]['title'],
            'relation' => null,
        ];

        usort($all_relatives, function($relative_a, $relative_b) {
            return strcmp($relative_a['property'], $relative_b['property']);
        });

        respond_success($all_relatives);

        break;
    case 'create_new':
        $title = request_param('title');
        $shareable = request_param('shareable');

        if ( ! is_string($title)) {
            show_error('Bad "title" param');
        }

        if ( ! (is_numeric($shareable) && ($shareable == 0 || $shareable == 1))) {
            show_error('Bad "shareable" param');
        }

        $sql = "INSERT INTO rental_properties
                    (title, shareable)
                VALUES
                    (?, ?)
        ";
        Db::execute($sql, [$title, $shareable]);

        respond_success([
            'id' => Db::last_insert_id()
        ]);

        break;
    case 'assign_parent':
        $rental_property_id = request_param('rental_property_id');
        $parent_id = request_param('parent_id');

        $sql = "INSERT INTO parent_child_relations
                    (parent_id, child_id)
                VALUES
                    (?, ?)
        ";
        Db::execute($sql, [$parent_id, $rental_property_id]);

        // $sql = "delete from parent_child_relations where parent_id = 1 and child_id = 7";
        // Db::execute($sql);

        respond_success();

        break;
    default:
        echo 'Default';

        break;
}

function print_tree_recursive($parent_child_relations_grouped, $rental_properties, $rental_property_id, $level = 1)
{
    echo str_repeat('—', $level) . ' ' . $rental_properties[$rental_property_id]['title'] . '<br>';
    if (array_key_exists($rental_property_id, $parent_child_relations_grouped)) {
        $children_ids = $parent_child_relations_grouped[$rental_property_id];
        foreach ($children_ids as $child_id) {
            print_tree_recursive($parent_child_relations_grouped, $rental_properties, $child_id, $level + 1);
        }
    }
}
