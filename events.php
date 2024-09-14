<?php
function printArray($array)
{
	foreach ($array as $key => $value) {
		echo $key;
		echo " : ";
		echo $value;
		echo " ### ";
	}
}

function json_validate_custom($data)
{
	if (is_object(json_decode($data))) {
		return true;
	} else {
		return false;
	}
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (
		isset($_POST["tableName"]) &&
		isset($_POST["data"]) &&
		isset($_POST["id"])
	) {
		// Method call when requesting a single new empty row (add row called)
		$tableName = $_POST["tableName"];
		$id = $_POST["id"];
		$data = $_POST["data"];
		require_once "tables.php";
		echo createTableRow($tableName, $id, json_decode($data), true, true);
		exit();
	} elseif (isset($_POST["tableName"]) && isset($_POST["isEditable"])) {
		// Method call when requestng to create a whole new table (edit mode changed)
		// Change the tables editable state and reload it
		$tableName = $_POST["tableName"];
		$isEditable = $_POST["isEditable"] === "true" ? true : false;
		require_once "tables.php";
		echo createTableRows($tableName, null, $isEditable);
		exit();
	} elseif (isset($_POST["tableName"]) && isset($_POST["data"])) {
		// Save the given json data to the file
		$tableName = $_POST["tableName"];
		$data = $_POST["data"];
		if (json_validate_custom($data)) {
			$file = fopen("data/tables/$tableName.json", "w+");
			if (is_resource($file)) {
				fwrite($file, $data);
				fclose($file);
				echo "Successfully saved the table!";
				exit();
			} else {
				echo "Error: File is not a resource: ";
				echo "'$file' ";
				echo "'$tableName'";
				exit();
			}
		} else {
			echo "Error: Json file not valid!";
			printArray($_POST);
			exit();
		}
	} else if (isset($_POST["tableData"])) { 
		// Method Call when changing the sorting order and the table has to be created
		$tableData = $_POST["tableData"];
		if(!json_validate_custom($tableData)) {
			echo "Error: Table Data when trying to sort the table is not in valid json form!";
			exit();
		}
		$tableData = json_decode($tableData);
		require_once "tables.php";
		echo createTableRowsFromData($tableData);
		exit();
	}
	else {
		echo "Error: Invalid parameters.";
		exit();
	}
}
?>
