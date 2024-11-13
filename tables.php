<?php
  # Global Variables
  define('BACKGROUND_LIGHT', "#e3e3e3");
  define('BACKGROUND_DARK', "#cdcdcd");
  define('GREEN_LIGHT', "#739C40");
  define('GREEN_DARK', "#668A39");
  define('RED_LIGHT', "#B65151");
  define('RED_DARK', "#A44949");

  function createDescriptor($id, $row, $tableName, $content, $isEditable, $isEven) {
    $color = $isEven ? BACKGROUND_DARK : BACKGROUND_LIGHT;

    if($isEditable) {
      return <<<EOL
        <td class="cell_descriptor" style="background-color: $color">
          <span class="row_text" id="{$tableName}_{$row}_{$id}" contenteditable="true" onchange="onChange({$tableName}_{$row}_{$id})">{$content}</span>
        </td>
      EOL;
    } else {
      return <<<EOL
        <td class="cell_descriptor" id="{$tableName}_{$row}_{$id}" style="background-color: $color">{$content}</td>
      EOL;
    }
  }

  function createTextField($id, $row, $tableName, $content, $isEditable, $isEven) {
    $green = $isEven ? GREEN_DARK : GREEN_LIGHT;
    $red = $isEven ? RED_DARK : RED_LIGHT;
    $color = $content != "-" ? $green : $red;

    if($isEditable) {
      return <<<EOL
        <td class="cell_textfield" style="background-color: $color">
          <span class="row_text" id="{$tableName}_{$row}_{$id}" contenteditable="true" onchange="onChange({$tableName}_{$row}_{$id})">{$content}</span>
        </td>
      EOL;
    } else {
      return <<<EOL
        <td class="cell_textfield" id="{$tableName}_{$row}_{$id}" style="background-color: $color">{$content}</td>
      EOL;
    }
  }

  function createCheckbox($id, $row, $tableName, $content, $isEditable, $isEven) {
    $green = $isEven ? GREEN_DARK : GREEN_LIGHT;
    $red = $isEven ? RED_DARK : RED_LIGHT;
    $color = $content == "true" ? $green : $red;
    $contentText = $content == "true" ? "true" : "-";
    $contentState = $content == "true" ? "checked" : "";

    if($isEditable) {
      return <<<EOL
        <td class="cell_checkbox" style="background-color: $color">
          <input type="checkbox" id="{$tableName}_{$row}_{$id}" onchange="onValueChange({$tableName}_{$row}_{$id})" {$contentState} />
        </td>
      EOL;
    } else {
      return <<<EOL
        <td class="cell_checkbox" id="{$tableName}_{$row}_{$id}" style="background-color: $color">{$contentText}</td>
      EOL;
    }
  }

  function createBodyCell($id, $row, $tableName, $type, $content, $isEditable, $isEven) {
    switch($type) {
      case "descriptor":
        return createDescriptor($id, $row, $tableName, $content, $isEditable, $isEven);
        break;
      case "textfield":
        return createTextField($id, $row, $tableName, $content, $isEditable, $isEven);
        break;
      case "checkbox":
        return createCheckbox($id, $row, $tableName, $content, $isEditable, $isEven);
        break;
      default:
        echo "Error: The given tpye '$type' is an unknown cell type!";
        exit;
      }
  }

  # The 'useFirstRow' option is uesed when creating empty rows from a small columns data set.
  function createTableRow($tableName, $i, $columns, $isEditable, $useFirstRow) {
    $row = "<tr id=\"{$tableName}_entry_{$i}\" class=\"row_entry\">";
    for ($j = 0; $j < count($columns); $j++) {
      if($useFirstRow) { 
        $row .= createBodyCell($j, $i, $tableName, $columns[$j]->type, $columns[$j]->data[0], $isEditable, $i % 2 == 0);
      } else {
        $row .= createBodyCell($j, $i, $tableName, $columns[$j]->type, $columns[$j]->data[$i], $isEditable, $i % 2 == 0);
      }
    }
    $row .= "</tr>";
    return $row;
  }

  function createTableRows($tableName, $columns, $isEditable) {
    # To enable calling this function from the events.php file, a check was implemented to see
    # if the $columns variable is null, if it is, it will be loaded from file
    if($columns == null) {
      $tableData = file_get_contents("./data/tables/$tableName.json", "$tableName.json");
      $columns = json_decode($tableData)->columns;
    }
    $rows = "";
    $rowCount = count($columns[0]->data);
    for ($i = 0; $i < $rowCount; $i++) {
      $rows .= createTableRow($tableName, $i, $columns, $isEditable, false);
    }  
    return $rows;
  }

  function createHeaderCell($tableName, $columnID, $column) {
    return <<<EOL
      <th scope="row" 
      id="{$tableName}_header_{$columnID}"
      onclick="sortForColumn($columnID, $tableName)">
        $column->name
      </th>
      EOL;
  }

  # Creates a table from the given table data
  function createTable($tableName, $columns, $isEditable) {
    # Build the head of the table
    $returnElement = <<<EOL
      <div id="{$tableName}_container" class="table_container content">
        <table>
          <thead id="{$tableName}_head">
            <tr>
      EOL;
    for ($i = 0; $i < count($columns); $i++) {
      $returnElement .= createHeaderCell($tableName, $i, $columns[$i]);
    }
    $returnElement .= <<<EOL
            </tr>
          </thead>
          <tbody id="$tableName">
      EOL;

    $returnElement .= createTableRows($tableName, $columns, $isEditable);

    $returnElement .= <<<EOL
          </tbody>
        </table>
      </div>
    EOL;
    return $returnElement;
  }

  # Creates a table from the given data
  function createTableRowsFromData($tableData) {
    $tag = $tableData->tag;
    $columns = $tableData->columns;
    $returnElement = "";
    for ($i=0; $i < count($columns[0]->data); $i++) {   
      $returnElement .= "\n<tr id=\"{$tag}_entry_{$i}\" class=\"row_entry\">";
      for ($j=0; $j < count($columns); $j++) {
        $returnElement .= "\n\t";
        $returnElement .= createBodyCell($i, $j, $tag, $columns[$j]->type, $columns[$j]->data[$i], false, $i % 2 == 0);
      }
      $returnElement .= "</tr>";
    }
    return $returnElement;
  }

  # Creates the collapsible elongated button which shows / hides the table
  function createCollapsible($header, $tableName) {
    return <<<EOL
      <div class="collapsible">
          <h2 id="header_{$tableName}">$header <label id=header_{$tableName}_count>[0]</label></h2>
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
            <span class="slider round" id="{$tableName}_slider"></span>
          </label>
        </div>
      EOL;
  }
  
  # Creates a Group of tables
  function createGroup($group) {
    $returnedElement = "<h2 class=\"separator\">$group->name</h2>";
    foreach ($group->tables as $tableName) {
      # Load the table from json into an object
      $tableData = file_get_contents("./data/tables/$tableName.json", "$tableName.json");
      $table = json_decode($tableData);
      if(!is_object($table)) {
        return "Error: The file '$table.json' could not be parsed! ($tableName)";
        exit;
      }
      $returnedElement .= createCollapsible($table->header, $table->tag);
      $returnedElement .= createTable($table->tag, $table->columns, false);
    }
    return $returnedElement;
  }

  # Create the header with a given header text
  function createHeader($headerText) {
    return <<<EOL
      <header>
        <h1>$headerText</h1>
      </header>
    EOL;
  }
?>
