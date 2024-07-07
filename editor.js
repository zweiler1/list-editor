// Colors
let backgroundLight = "#e3e3e3";
let backgroundDark = "#cdcdcd";
let colorGreenLight = "#739C40";
let colorGreenDark = "#668A39";
let colorRedLight = "#B65151";
let colorRedDark = "#A44949";

var editable = {
  'movies': false,
  'series': false,
  'ps5': false,
  'ps4': false,
  'ps3': false,
  'ps2': false,
  'ps1': false,
  'xone': false,
  'x360': false,
  'nds': false,
  'wii': false,
  'wiiu': false,
  'steam': false,
  'gog': false,
  'epic': false,
  'amazon': false,
  'ubisoft': false,
  'ea': false,
  'physical': false
};

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

// Extracts the data from the table and returns it as a json string
function getDataFromTable(tableName) {
  var tableBody = document.getElementById(tableName);
  var data = [];
  for(var i = 0; i < tableBody.childNodes.length; i++) {
    var node = tableBody.childNodes[i];
    console.log(node.cells[0]);
    if(node.nodeName == "HEADER") {
      i = tableBody.childNodes.length + 1;
    } else if(node.id != undefined && node.id != "" 
      && node.cells[0].childNodes[1].childNodes.length > 0 
      && node.cells[0].childNodes[1].childNodes[0].nodeValue != null) 
    {
      data.push({
        name: node.cells[0].childNodes[1].innerHTML,
        year: node.cells[1].childNodes[1].innerHTML,
        type: node.cells[2].childNodes[1].innerHTML,
        name1: node.cells[3].childNodes[1].checked ? "true" : "-",
        name2: node.cells[4].childNodes[1].checked ? "true" : "-",
        name3: node.cells[5].childNodes[1].checked ? "true" : "-",
        name4: node.cells[6].childNodes[1].checked ? "true" : "-",
        name5: node.cells[7].childNodes[1].checked ? "true" : "-",
        name6: node.cells[8].childNodes[1].checked ? "true" : "-"
      });
    }
  }
  
  return JSON.stringify({ data: data });
}

// Creates an html element from the given string
function elementFromHtml(html) {
  const template = document.createElement("template");

  template.innerHTML = html.trim();
  return template.content.firstElementChild;
}

// Called whenever the "add row" button was pushed
function addRow(id) {
  var tableName = id.id;
  $.ajax({
    method: "POST",
    url: "events.php",
    data: { 
      tableName: tableName, 
      data: {
        name: "",
        year: "-",
        type: "-"
      },
      id: document.getElementById(tableName).childNodes.length,
      isEditable: true
    }
  }).done(function(response) {
    //console.log(response);
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
    data: { tableName: tableName, isEditable: editable[tableName] }
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
