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
function getContent(type, content) {
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
        name: cell.textContent,
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
        console.log(type);
        if(type == "descriptor") {
          console.log(content);
        }
        if(content.childNodes.length == 0 && type == "descriptor") {
          isEmptyRow = true;
        } else {
          if(data.columns[col].type == "") {
            data.columns[col].type = type;
          }

          var contentString = getContent(type, content);
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
  console.log(table);
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
  editable[tableName] = !editable[tableName];

  if(!editable[tableName]) {
    // Save the table data into the respective json file
    var data = getDataFromTable(tableName);
    await $.ajax({
      method: "POST",
      url: "events.php",
      data: { tableName: tableName, data: data }
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
    //console.log(response);
    document.getElementById(tableName).innerHTML = response;
    requesting = false;
  });
}

function onButtonHover(inside) {
  hovering = inside;
}

// Makes the buttons collaps the content right "below" them
var coll = document.getElementsByClassName("collapsible");
for (var i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    if(!hovering) {
      this.classList.toggle("active");
      var content = this.nextElementSibling;
      if (content.style.maxHeight){
        content.style.maxHeight = null;
      } else {
        content.style.maxHeight = content.scrollHeight + "px";
      }
    }
  });
}
