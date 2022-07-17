# Back-end portion

For setup instructions read `README.md` that is located one level up.

## Basic info

- `public/index.php` is the entry point.
- `src/main.php` is the main file where all logic resides.
- Classes from `lib` are autoloaded automatically.
- Database structure can be seen from `src/initial_db_structure.sql` file.
  - There are some test data inserts in that file. Delete them for clean start.

## API

See `src/main.php` for code. API endpoints are chosen with switch statement. All API endpoints return JSON strings (except `/print_tree`) with specific structure:

    echo json_encode([
        'success' => $success, // can be 0 or 1
        'error_msg' => $error_msg, // string
        'data' => $data, // any data, typically array.
    ]);

### List of endpoints

- GET: `/print_tree` - Echo out the dependency tree.
- GET: `/get_tree` - Return the list of dependencies.
  - Note: shareable rental properties that have 0 parents are not shown in the tree as root rental properties (they should have at least one parent).
- GET: `/get_all` - Return the list of all rental properties.
- GET: `/get_relatives_of?rental_property_id=<int>` - Return the "flat" list of specified rental property relatives.
- POST: `/create_new?title=<string>&shareable=<1 or 0>` - Create new rental property and insert it into the database.
- POST: `assign_parent?rental_property_id=<int>&parent_id=<int>` - Assign the specified parent to the specified rental property.
  - Shareable rental properties can be assigned many parents.
  - Non-shareable rental properties can be assigned only one parent. So when assigning a new parent to a non-shareable rental property, then we "move" it to a new place.
