This is a rental properties hometask written in vanilla PHP, React and which uses MySQL database.

# Setup instructions

The thing was developed on Windows using [WampServer](https://wampserver.com/en), which includes out-of-the-box ready Apache server with MySQL and PHP. This tutorial includes instructions on how to set up the project locally with WampServer.

## Back-end portion

- Run WampServer.
- Create a WampServer virtual host for this project. Video tutorial on WampServer vhost addition: https://www.youtube.com/watch?v=PoBvZZmt9Hs
  - In the "Name of the `Virtual Host`" put (**Notice the dash here, not underline**):

        rph-back

  - In the "Complete absolute path" put path to project's `public` directory (assuming that wamp is installed in `C:/wamp/` and the project is in `www` directory):

        C:/wamp/www/rph_back/public

- Create the project's MySQL database and import its initial structure
  - Open the PhpMyAdmin panel through WampServer tray icon, log into the panel, open SQL query runner and run

        CREATE DATABASE rental_properties_hometask

  - Import the `initial_db_structure.sql` SQL statements into the `rental_properties_hometask` database.

  In PhpMyAdmin panel, choose `rental_properties_hometask` database, open SQL query runner, copy and paste `initial_db_structure.sql` file contents into there and run the query.
- Go to `rph_back/config/` directory and create `config.inc` file from `config.inc.dist` template file and configure the former as needed.

      cd rph_back
      cp config/config.inc.dist config/config.inc

  Default user is `root` without password. Host is `localhost`. Hence, dev instance config should look like this:

      'https_required' => false,
      'is_dev_environment' => true,

      'mysql_host' => 'localhost',
      'mysql_dbname' => 'rental_properties_hometask',
      'mysql_username' => 'root',
      'mysql_password' => '',

- The back-end portion should now be accessible from the browser (WampServer might need a full restart). Try requesting

      http://rental-properties-hometask-backend/show_all

## Front-end portion

-
