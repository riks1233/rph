This is the rental properties hometask written in vanilla PHP, MySQL and React.

# Setup instructions

The thing was developed on Windows using [WampServer](https://wampserver.com/en), which includes out-of-the-box ready Apache server with MySQL and PHP. This tutorial includes instructions on how to set up the project locally (development mode) with WampServer.

## Back-end portion

- Run WampServer.
- Create a WampServer virtual host for this project. Video tutorial on WampServer virtual host addition: https://www.youtube.com/watch?v=PoBvZZmt9Hs
  - In the "Name of the `Virtual Host`" put (**Notice the dash here, not underline**):

        rph-back

  - In the "Complete absolute path" put path to `rph_back/public` directory (assuming that wamp is installed in `C:/wamp/` and the project is in `www` directory):

        C:/wamp/www/rph_back/public

- Create the project's MySQL database and import its initial structure
  - Open the PhpMyAdmin panel through WampServer tray icon, log into the panel (Default user is `root` without password), open SQL query runner and run

        CREATE DATABASE rental_properties_hometask;

  - Import the `initial_db_structure.sql` SQL statements into the `rental_properties_hometask` database.

    In PhpMyAdmin panel, choose `rental_properties_hometask` database, open SQL query runner, copy and paste `initial_db_structure.sql` file contents into there and run the query.
- Create `rph_back/config/config.inc` file from `rph_back/config/config.inc.dist` template file and configure the former as needed.

      cp rph_back/config/config.inc.dist rph_back/config/config.inc

  Default user is `root` without password. Host is `localhost`. Hence, dev instance config should look like this:

      'https_required' => false,
      'is_dev_environment' => true,
      'display_errors' => false,

      'mysql_host' => 'localhost',
      'mysql_dbname' => 'rental_properties_hometask',
      'mysql_username' => 'root',
      'mysql_password' => '',

      // List of allowed origins (CORS policy).
      'access_control_allowed_origins' => [
          'http://localhost:3000',
          'http://localhost:8000',
      ],

- The back-end portion should now be accessible from the browser (WampServer might need a full restart). Try requesting

      http://rph-back/
      http://rph-back/print_tree

## Front-end portion

- Create `rph_front/.env` file from `rph_front/.env.dist` template file and configure the former as needed.
- Install npm dependencies and start the React project in development mode.

      cd rph_front
      npm install
      npm start

- And that should be it. Open the app in browser if it didn't open by itself.

      http://localhost:3000

# Further reading

- Read `rph_back/README.md` for server explanations and API endpoints.
- Read `rph_front/README.md` for client-side UI explanations.

# Time spent (in hours)

**Format: `<number of hours>: <explanation>`**

## Back-end

- 1: Reading, understanding the task, questions.
- 2: Setting up the project based on previous projects and experience. Messing with wampserver - wasn't working.
- 4: Set up an `/all` endpoint and got it working as expected. Was researching PDO statements, fetching, and also messing with PHP arrays (indexed vs associative, unset() wasn't working as I expected with the indexed array).
- 3: Set up other endpoints.
- 5: Fixes and polishing

## Front-end

- 2: Tutorials
  - [BradTraversy - React JS Crash Course](https://www.youtube.com/watch?v=w7ejDZ8SWv8)
  - [Classsed - Zustand State Management in React (Better than Redux?)
](https://www.youtube.com/watch?v=jLcF0Az1nx8)
- 10: Building out the UI

**Total time spent**: 27 hours
