<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listeneditor</title>
    <link rel="icon" type="image/x-icon" href="film-reel-svgrepo-com.svg">
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

      # Returns the header text of a given tables name (the name of the .json and its id)
      function getHeader($tableName) {
        switch($tableName) {
          case 'movies': return 'Filme';
          case 'series': return 'Serien';
          case 'ps5': return 'Playstation 5';
          case 'ps4': return 'Playstation 4';
          case 'ps3': return 'Playstation 3';
          case 'ps2': return 'Playstation 2';
          case 'ps1': return 'Playstation 1';
          case 'xone': return 'Xbox One';
          case 'x360': return 'Xbox 360';
          case 'nds': return 'Nintendo DS';
          case 'wii': return 'Wii';
          case 'wiiu': return 'Wii U';
          case 'steam': return 'Steam';
          case 'gog': return 'GOG';
          case 'epic': return 'Epic';
          case 'amazon': return 'Amazon';
          case 'ubisoft': return 'Ubisoft';
          case 'ea': return 'EA';
          case 'physical': return 'Physical';
        }
      }

      # Creates the collapsible elongated button which shows / hides the table
      function createCollapsible($tableName) {
        $header = getHeader($tableName);
        return <<<EOL
          <div class="collapsible">
              <h2>$header</h2>
              <button 
                id="{$tableName}_add_row" 
                class="btn_add_row" 
                onclick="addRow($tableName)"
                onmouseover="onButtonHover(true)"
                onmouseleave="onButtonHover(false)">
                Neuer Eintrag
              </button>
              <label class="switch">
                <input
                  id="{$tableName}_switch"
                  type="checkbox"
                  onchange="changeEditMode($tableName)">
                <span class="slider round"></span>
              </label>
            </div>
          EOL;
      }

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
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> -->
  <script src="./jQuery.js"></script>
  <script src="./editor.js"></script>
</html>
