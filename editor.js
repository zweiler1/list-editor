// Colors
let backgroundLight = "#e3e3e3";
let backgroundDark = "#cdcdcd";
let colorGreenLight = "#739C40";
let colorGreenDark = "#668A39";
let colorRedLight = "#B65151";
let colorRedDark = "#A44949";

var editable = {};

var hovering = false;
var requesting = false;

var openedTable = null;

// Called when the checkbox state changes
function onValueChange(id) {
  let id_arr = id.id.split("_");
  let isOdd = id_arr[id_arr.length - 1] % 2 != 0;

  let colorGreen = isOdd ? colorGreenLight : colorGreenDark;
  let colorRed = isOdd ? colorRedLight : colorRedDark;

  if (id.checked) {
    id.parentNode.style.backgroundColor = colorGreen;
  } else if (!id.checked && id.textContent != "-" && id.textContent != "") {
    id.parentNode.style.backgroundColor = colorGreen;
  } else {
    id.parentNode.style.backgroundColor = colorRed;
  }
}

// Checks for the type and depending on the type returns another subsection of the content
function getContentEdit(type, content) {
  switch(type) {
    case "descriptor": return content.childNodes[0].nodeValue;
    case "textfield": return content.childNodes[0].nodeValue;
    case "checkbox": return content.checked ? "true" : "false";
    default:
      console.log("Cannot get content of wrong type: '" + type + "'");
      break;
  }
}

// Extracts the data from the table and returns it as a json string
function getDataFromTableEdit(tableName) {
  var tableBody = document.getElementById(tableName);
  var tableHeader = document.getElementById("header_" + tableName).childNodes[0].nodeValue;
  var data = {
    "header": tableHeader,
    "tag": tableName,
    "columns": []
  };

  // Initialize the columns data and set the names of the columns respectively
  var tableHead = document.getElementById(tableName + "_head");
  var tableHeadColumns = tableHead.childNodes[1];
  for(let col = 0; col < tableHeadColumns.childNodes.length; col++) {
    let cell = tableHeadColumns.childNodes[col];
    if(cell.nodeName == "TH") {
      data.columns.push({
        name: cell.innerText,
        type: "",
        data: []
      })
    }
  }

  var isEmptyRow = false;
  for(var row = 0; row < tableBody.childNodes.length; row++) {
    let rowNode = tableBody.childNodes[row];
    var col = 0;
    rowNode.childNodes.forEach(colNode => {
      var type = colNode.className;
      if(type != undefined && !isEmptyRow) {
        type = type.toString().replace("cell_", "");
        let content = colNode.childNodes[1];
        //console.log(type);
        //if(type == "descriptor") {
        //  console.log(content);
        //}
        if(content.childNodes.length == 0 && type == "descriptor") {
          isEmptyRow = true;
        } else {
          if(data.columns[col].type == "") {
            data.columns[col].type = type;
          }

          var contentString = getContentEdit(type, content);
          data.columns[col].data.push(contentString);
          col++;
        }
      }
    });
    isEmptyRow = false;
  }

  return JSON.stringify(data);
}

// Creates an html element from the given string
function elementFromHtml(html) {
  const template = document.createElement("template");

  template.innerHTML = html.trim();
  return template.content.firstElementChild;
}

function getEmptyCellString(type) {
  switch(type) {
    case "descriptor": return "";
    case "textfield": return "-";
    case "checkbox": return "false";
  }
}

function getEmptyRow(tableName) {
  var columns = [];
  var table = document.getElementById(tableName);
  //console.log(table);
  var firstRow = table.childNodes[0].childNodes;
  firstRow.forEach(cell => {
    let type = cell.className;
      if(type != undefined) {
        type = type.toString().replace("cell_", "");
        columns.push({
          name: "Name",
          type: type,
          data: [getEmptyCellString(type)]
        })
      }
  });
  
  return JSON.stringify(columns);
}

// Called whenever the "add row" button was pushed
function addRow(id) {
  var tableName = id.id;
  var index = document.getElementById(tableName).childNodes.length;
  $.ajax({
    method: "POST",
    url: "events.php",
    data: { 
      tableName: tableName, 
      data: getEmptyRow(tableName),
      id: index
    }
  }).done(function(response) {
    document.getElementById(tableName).appendChild(elementFromHtml(response));
    // change the table containers max height to take the new added row into account
    var container = document.getElementById(tableName + "_container");
    if(container.style.maxHeight != null) {
      container.style.maxHeight = container.scrollHeight + "px";
    }
  });
}

// Called whenever the radio button switch is pressed
async function changeEditMode(id) {
  if(requesting)
    return;
  requesting = true;
  var tableName = id.id;
  document.getElementById(tableName + "_switch").disabled = true;
  document.getElementById(tableName + "_slider").className = "slider slider-deactive round";
  editable[tableName] = !editable[tableName];

  if(!editable[tableName]) {
    // Save the table data into the respective json file
    var data = getDataFromTableEdit(tableName);
    await $.ajax({
      method: "POST",
      url: "events.php",
      data: { 
        tableName: tableName, 
        data: data 
      }
    }).done(function(response) {
      if(response != undefined && response != "") {
        console.log(response);
      }
    });
  }

  // Change the visibility of all editor function buttons
  document.getElementById(tableName + "_add_row").style.display = editable[tableName] ? "inline-block" : "none";

  // Make a function call request of the php script to create a new table content
  await $.ajax({
    method: "POST",
    url: "events.php",
    data: { 
      tableName: tableName, 
      isEditable: editable[tableName] 
    }
  }).done(function(response) {
    document.getElementById(tableName).innerHTML = response;
    document.getElementById(tableName + "_switch").disabled = false;
    document.getElementById(tableName + "_slider").className = "slider round";
    requesting = false;
    
    var tableLength = document.getElementById(tableName).rows.length;
    console.log(tableLength);
    document.getElementById("header_" + tableName + "_count").innerHTML = "[" + tableLength + "]";
  });
}

function onButtonHover(inside) {
  hovering = inside;
}

function toggleTable(content, inactive) {
  if (inactive) {
    content.style.maxHeight = null;
  } else {
    content.style.maxHeight = content.scrollHeight + "px";
  }
}

// Makes the buttons collaps the content right "below" them
var coll = document.getElementsByClassName("collapsible");
for (var i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    if(!hovering) {
      this.classList.toggle("active");
      var content = this.nextElementSibling;
      if(openedTable != null && openedTable != content) {
        toggleTable(openedTable, true);
        openedTable = null;
      }
      const isOpen = content.style.maxHeight;
      toggleTable(content, isOpen);
      openedTable = isOpen ? null : content;
    }
  });
}

function getContent(type, content) {
  switch(type) {
    case "descriptor": return content;
    case "textfield": return content;
    case "checkbox": return content == "true" ? "true" : "false";
    default:
      console.log("Cannot get content of wrong type: '" + type + "'");
      break;
  }
}

// Get the data from the table when in view mode
function getDataFromTable(tableName) {
  var tableBody = document.getElementById(tableName);
  var tableHeader = document.getElementById("header_" + tableName).childNodes[0].nodeValue;
  var data = {
    "header": tableHeader,
    "tag": tableName,
    "columns": []
  };

  // Initialize the columns data and set the names of the columns respectively
  var tableHead = document.getElementById(tableName + "_head");
  var tableHeadColumns = tableHead.childNodes[1];
  for(let col = 0; col < tableHeadColumns.childNodes.length; col++) {
    let cell = tableHeadColumns.childNodes[col];
    if(cell.nodeName == "TH") {
      data.columns.push({
        name: cell.outerText,
        type: "",
        data: []
      })
    }
  }

  for(var row = 0; row < tableBody.childNodes.length; row++) {
    let rowNode = tableBody.childNodes[row];
    var col = 0;
    rowNode.childNodes.forEach(colNode => {
      var type = colNode.className;
      if(type != undefined) {
        type = type.toString().replace("cell_", "");
        let content = colNode.innerHTML;

        if(data.columns[col].type == "") {
          data.columns[col].type = type;
        }

        var contentString = getContent(type, content);
        data.columns[col].data.push(contentString);
        col++;
      }
    });
  }

  return data;
}


// SORTING STUFF
async function sortForColumn(columnID, tableName) {
  var tableData = getDataFromTable(tableName.id);
  var ascending = true;
  var newOrder = getOrderArray(tableData.columns[columnID], true);
  if(isOrderUnchanged(newOrder)) {
    newOrder = getOrderArray(tableData.columns[columnID], false);
    ascending = false;
  }
  
  console.log("Sorting for column '" + tableData.columns[columnID].name + 
    "' in table '" + tableName.id + "' " + (ascending ? "ascending" : "descending"));
  tableData.columns = moveColumns(tableData.columns, newOrder);
  var newTable = await createTableFromData(tableData);
  document.getElementById(tableName.id).innerHTML = newTable;
}

// Checks whether the order has not changed. This is the case whenn the table is sorted ascending, for example
// This function is used to detect if the table is sorted ascending, if it is, it is sorted descending
function isOrderUnchanged(order) {
  var orderUnchanged = true;
  order.forEach(element => {
    if(element != order.indexOf(element)) 
      orderUnchanged = false;
  });
  return orderUnchanged;
}

// Returns an object array containing the old and the new position of the sorted list
function getOrderArray(column, isAscending) {
  // create a copy of the original array
  const sortedArr = column.data.slice();
  sortedArr.sort((a, b) => isAscending ? 
    ("" + a).localeCompare("" + b, undefined, { numeric: true })
    : ("" + b).localeCompare("" + a, undefined, { numeric: true }));
  
  const newIndicesAccessed = [];
  const oldToNewIndices = [];
  for (let i = 0; i < column.data.length; i++) {
    var newIndex = sortedArr.indexOf(column.data[i]);
    while(newIndicesAccessed[newIndex] != undefined) {
      newIndex++;
    }
    newIndicesAccessed[newIndex]++;
    oldToNewIndices[i] = newIndex;
  }

  return oldToNewIndices;
}

function columnsDeepCopy(columns) {
  const newColumns = [];
  columns.forEach(column => {
    const newColumn = {};
    newColumn.name = column.name;
    newColumn.type = column.type;
    newColumn.data = [];
    for(let i = 0; i < column.data.length; i++) {
      newColumn.data[i] = column.data[i];
    }
    newColumns.push(newColumn);
  });
  return newColumns;
}

// Moves all rows of the columns to their new positions
function moveColumns(columns, fromToArray) {

  // Make a deep copy of the columns object
  var newColumns = columnsDeepCopy(columns);

  for(let i = 0; i < fromToArray.length; i++) {
    for(let j = 0; j < columns.length; j++) {
      newColumns[j].data[fromToArray[i]] = columns[j].data[i];
    }
  }
  return newColumns;
}

// Creates a table from the given data
async function createTableFromData(data) {
  var table = "";
  await $.ajax({
    method: "POST",
    url: "events.php",
    data: { 
      tableData: JSON.stringify(data)
    }
  }).done(function(response) {
    table = response;
  });
  return table;
}

// Update the counter in all tables
var searchedTables = document.querySelectorAll('[id$="_count"]');
searchedTables.forEach(element => {
  var id = element.id.replace("header_", "").replace("_count", "");
  var tableLength = document.getElementById(id).rows.length;
  element.innerHTML = "[" + tableLength + "]";
});