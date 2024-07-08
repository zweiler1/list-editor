<?php
  # Global Variables
  define('BACKGROUND_LIGHT', "#e3e3e3");
  define('BACKGROUND_DARK', "#cdcdcd");
  define('GREEN_LIGHT', "#739C40");
  define('GREEN_DARK', "#668A39");
  define('RED_LIGHT', "#B65151");
  define('RED_DARK', "#A44949");

  function createRowFromData($id, &$data, $tableName, $isEditable) {
    $data = (object) $data;
    $isEven = $id % 2 == 0;
    $grey = $isEven ? BACKGROUND_DARK : BACKGROUND_LIGHT;
    $green = $isEven ? GREEN_DARK : GREEN_LIGHT;
    $red = $isEven ? RED_DARK : RED_LIGHT;

    $colorYear = $data->year != "-" ? $green : $red;
    $colorType = $data->type != "-" ? $green : $red;
    $colorName1 = empty($data->name1) || $data->name1 != "true" ? $red : $green;
    $colorName2 = empty($data->name2) || $data->name2 != "true" ? $red : $green;
    $colorName3 = empty($data->name3) || $data->name3 != "true" ? $red : $green;
    $colorName4 = empty($data->name4) || $data->name4 != "true" ? $red : $green;
    $colorName5 = empty($data->name5) || $data->name5 != "true" ? $red : $green;
    $colorName6 = empty($data->name6) || $data->name6 != "true" ? $red : $green;

    if($isEditable) {
      $name1 = empty($data->name1) ? "" : ($data->name1 == "true" ? "checked" : "");
      $name2 = empty($data->name2) ? "" : ($data->name2 == "true" ? "checked" : "");
      $name3 = empty($data->name3) ? "" : ($data->name3 == "true" ? "checked" : "");
      $name4 = empty($data->name4) ? "" : ($data->name4 == "true" ? "checked" : "");
      $name5 = empty($data->name5) ? "" : ($data->name5 == "true" ? "checked" : "");
      $name6 = empty($data->name6) ? "" : ($data->name6 == "true" ? "checked" : "");

      return <<<EOL
        <tr id="{$tableName}_entry_{$id}" class="row_entry">
          <td class="row_cell_name" style="background-color: $grey">
            <span class="row_text" id="{$tableName}_name_{$id}" contenteditable="true"  onchange="onChange({$tableName}_name_{$id})">{$data->name}</span>
          </td>
          <td class="row_cell_year" style="background-color: $colorYear">
            <span class="row_text" id="{$tableName}_year_{$id}" contenteditable="true" onchange="onChange({$tableName}_year_{$id})">{$data->year}</span>
          </td>
          <td class="row_cell_type" style="background-color: $colorType">
            <span class="row_text" id="{$tableName}_type_{$id}" contenteditable="true" onchange="onChange({$tableName}_type_{$id})">{$data->type}</span>
          </td>
          <td class="row_cell_human" style="background-color: $colorName1" >
            <input type="checkbox" id="{$tableName}_name1_{$id}" onchange="onValueChange({$tableName}_name1_{$id})" {$name1} />
          </td>
          <td class="row_cell_human" style="background-color: $colorName2">
            <input type="checkbox" id="{$tableName}_name2_{$id}" onchange="onValueChange({$tableName}_name2_{$id})" {$name2} />
          </td>
          <td class="row_cell_human" style="background-color: $colorName3">
            <input type="checkbox" id="{$tableName}_name3_{$id}" onchange="onValueChange({$tableName}_name3_{$id})" {$name3} />
          </td>
          <td class="row_cell_human" style="background-color: $colorName4">
            <input type="checkbox" id="{$tableName}_name4_{$id}" onchange="onValueChange({$tableName}_name4_{$id})"{$name4} />
          </td>
          <td class="row_cell_human" style="background-color: $colorName5">
            <input type="checkbox" id="{$tableName}_name5_{$id}" onchange="onValueChange({$tableName}_name5_{$id})"{$name5} />
          </td>
          <td class="row_cell_human" style="background-color: $colorName6">
            <input type="checkbox" id="{$tableName}_name6_{$id}" onchange="onValueChange({$tableName}_name6_{$id})"{$name6} />
          </td>
        </tr>
        EOL;
    }
    else {
      $name1 = empty($data->name1) ? "-" : $data->name1;
      $name2 = empty($data->name2) ? "-" : $data->name2;
      $name3 = empty($data->name3) ? "-" : $data->name3;
      $name4 = empty($data->name4) ? "-" : $data->name4;
      $name5 = empty($data->name5) ? "-" : $data->name5;
      $name6 = empty($data->name6) ? "-" : $data->name6;

      return <<<EOL
      <tr id="{$tableName}_entry_{$id}" class="row_entry">
        <td class="row_cell_name" id="{$tableName}_name_{$id}" style="background-color: $grey">{$data->name}</td>
        <td class="row_cell_year" id="{$tableName}_year_{$id}" style="background-color: $colorYear">{$data->year}</td>
        <td class="row_cell_type" id="{$tableName}_type_{$id}" style="background-color: $colorType">{$data->type}</td>
        <td class="row_cell_human" id="{$tableName}_name1_{$id}" style="background-color: $colorName1">{$data->name1}</td>
        <td class="row_cell_human" id="{$tableName}_name2_{$id}" style="background-color: $colorName2">{$data->name2}</td>
        <td class="row_cell_human" id="{$tableName}_name3_{$id}" style="background-color: $colorName3">{$data->name3}</td>
        <td class="row_cell_human" id="{$tableName}_name4_{$id}" style="background-color: $colorName4">{$data->name4}</td>
        <td class="row_cell_human" id="{$tableName}_name5_{$id}" style="background-color: $colorName5">{$name5}</td>
        <td class="row_cell_human" id="{$tableName}_name6_{$id}" style="background-color: $colorName6">{$name6}</td>
      </tr>
      EOL;
    }
  }

  function createTableRows($tableName, $columns, $isEditable) {
    $rows = "";
    return $rows;
  }

  function createHeaderCell($tableName, $columnID, $column) {
    return "<th scope=\"row\" id=\"{$tableName}_header_{$columnID}\">$column->name</th>";
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

  # Creates the collapsible elongated button which shows / hides the table
  function createCollapsible($header, $tableName) {
    return <<<EOL
      <div class="collapsible">
          <h2 id="header_{$tableName}">$header</h2>
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
