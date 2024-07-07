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

  function createTableRows($tableName, $isEditable) {
    # Load da data
    $tableData = file_get_contents("./data/$tableName.json", "$tableName.json");
    $jsonData = json_decode($tableData);

    $rows = "";
    # Creating the table rows
    if ($jsonData != null) {
      for ($i=0; $i < count($jsonData->data); $i++) {
        $rows .= createRowFromData($i, $jsonData->data[$i], $tableName, $isEditable);
      }
    }
    return $rows;
  }

  function createTable($tableName, $isEditable) {
    $h3 = "SNr";
    if($tableName == "movies" || $tableName == "series") {
      $h3 = "Typ";
    }

    $table =  <<<EOL
      <div id="{$tableName}_container" class="table_container content">
        <table>
          <thead>
            <tr>
              <th scope="row" id="{$tableName}_name">Name</th>
              <th scope="row" id="{$tableName}_year">Jahr</th>
              <th scope="row" id="{$tableName}_type">$h3</th>
              <th scope="row" id="{$tableName}_name1">name1</th>
              <th scope="row" id="{$tableName}_name2">name2</th>
              <th scope="row" id="{$tableName}_name3">name3</th>
              <th scope="row" id="{$tableName}_name4">name4</th>
              <th scope="row" id="{$tableName}_name5">name5</th>
              <th scope="row" id="{$tableName}_name6">name6</th>
            </tr>
          </thead>
          <tbody id="$tableName">
    EOL;
    $table .= createTableRows($tableName, $isEditable);
    $table .= <<<EOL
          </tbody>
        </table>
      </div>
    EOL;
    return $table;
  }
?>