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
    <header>
      <h1>Auflistung aller Filme, Serien und Spiele</h1>
    </header>
    <main>
      <div class="table_splitter">
        <h2 class="separator">Filme und Serien</h2>
        <?php 
          echo createCollapsible("movies");
          echo createTable("movies", false);
          echo createCollapsible("series");
          echo createTable("series", false);
        ?> 
        <h2 class="separator">Playstation</h2>
        <?php 
          echo createCollapsible("ps5");
          echo createTable("ps5", false); 
          echo createCollapsible("ps4");
          echo createTable("ps4", false);
          echo createCollapsible("ps3");
          echo createTable("ps3", false);
          echo createCollapsible("ps2");
          echo createTable("ps2", false);
          echo createCollapsible("ps1");
          echo createTable("ps1", false); 
        ?>
        <h2 class="separator">Xbox</h2>
        <?php 
          echo createCollapsible("xone");
          echo createTable("xone", false);
          echo createCollapsible("x360");
          echo createTable("x360", false);
        ?>
        <h2 class="separator">Nintendo</h2>
        <?php
          echo createCollapsible("nds");
          echo createTable("nds", false);
          echo createCollapsible("wii");
          echo createTable("wii", false);
          echo createCollapsible("wiiu");
          echo createTable("wiiu", false);
        ?>
        <h2 class="separator">PC</h2>
        <?php
          echo createCollapsible("steam");
          echo createTable("steam", false);
          echo createCollapsible("gog");
          echo createTable("gog", false);
          echo createCollapsible("epic");
          echo createTable("epic", false);
          echo createCollapsible("amazon");
          echo createTable("amazon", false);
          echo createCollapsible("ubisoft");
          echo createTable("ubisoft", false);
          echo createCollapsible("ea");
          echo createTable("ea", false);
          echo createCollapsible("physical");
          echo createTable("physical", false);
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
