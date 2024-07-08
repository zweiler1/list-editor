<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>list-editor</title>
    <link rel="icon" type="image/x-icon" href="table-tree-svgrepo-com.svg">
    <style>
      @import url("styles/variables.css");
      @import url("styles/table.css");
      @import url("styles/slider.css");
      @import url("styles/collapsible.css");
      @import url("styles/editor.css");
    </style>
    <?php
      # Enable debugging
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);

      # Loads the tables.json into a object
      $tablesData = file_get_contents("./data/tables.json", "tables.json");
      $tables = json_decode($tablesData);

      # Imports the functions from the tables.php file in order to use them
      require('tables.php');
    ?>
  </head>
  <body>
    <?php echo createHeader($tables->header); ?>
    <main>
      <div class="table_splitter">
        <?php
          foreach ($tables->groups as $group) {
            echo createGroup($group);
          }
        ?>
      </div>
    </main>
    <footer>
      <p class="copyright">&copy; 2024 Finger weg, des is meins.</p>
    </footer>
  </body>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="./editor.js"></script>
</html>
