<?php
  function printArray($array) {
    foreach ($array as $key => $value) {
      echo $key;
      echo " : ";
      echo $value;
      echo " ### ";
    }
  }

  function json_validate_custom($data) {
    if (is_object(json_decode($data))) {
      return true;
    } else {
      return false;
    }
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['tableName']) && isset($_POST['data']) && isset($_POST['id']) && isset($_POST['isEditable'])) {
      // Method call when requesting a single new empty row (add row called)
      $tableName = $_POST['tableName'];
      $data = $_POST['data'];
      $id = $_POST['id'];
      $isEditable = $_POST['isEditable'];
      require_once("tables.php");
      $row = createRowFromData($id, $data, $tableName, $isEditable);
      echo $row;
      exit;
    } else if (isset($_POST['tableName']) && isset($_POST['isEditable'])) {
      // Method call when requestng to create a whole new table (edit mode changed)
      // Change the tables editable state and reload it
      $tableName = $_POST['tableName'];
      $isEditable = $_POST['isEditable'] === 'true' ? true : false;
      require_once("tables.php");
      $tableRows = createTableRows($tableName, $isEditable);
      // Send the data back
      echo $tableRows;
      exit;
    }  else if (isset($_POST['tableName']) && isset($_POST['data'])) {
      // Save the given json data to the file
      $tableName = $_POST['tableName'];
      $data = $_POST['data'];
      if(json_validate_custom($data)) {
        $file = fopen("data/$tableName.json",'w+');
        if(is_resource($file)) {
          fwrite($file, $data);
          fclose($file);
        } else {
          echo "Error: File is not a resource: ";
          echo "$file";
          exit;
        }
      } else {
        echo "Error: Json file not valid!";
        printArray($_POST);
        exit;
      }
    } else {
      echo "Error: Invalid parameters.";
      exit;
    }
  }
?>
