# list-editor

# Configuring the .json files

## tables.json

The `tables.json` file must be contained inside the `data` directory. In this file the groups and tables belonging to the group are determined.  
An example `tables.json` file could look like the following:  

```json
{
  "header": "List of all Movies and Games",
  "groups": [{
    "name": "Movies and Series",
    "tables": ["movies", "series"]
  }, {
    "name": "Games",
    "tables": ["ps1", "wii", "x360"]
  }]
}
```

With the configuration file above, a table editor will be created which will have the webpage header *List of all Movies and Games* with two groups: *Movies and Series* and *Games*.  
Each group will create a collapsible table for each of the given tables.  
Every here described table is the file name of the table inside the `data/tables` directory. When the table is described here, there *must* be an according table in this file.  

## table.json

The `<table>.json` file must be contained inside the `data/tables` directoy where `<table>` is the name of the saved table. With the example from above, for example, the table `ps1.json` must exist for the editor to work.  
An example `ps1.json` file could look like the following:  

```json
{
  "header": "Playstation 1",
  "tag": "ps1",
  "columns": [{
    "name": "Name",
    "type": "descriptor",
    "data": ["Metal Gear Solid", "Colin McRae Rally", "Crash Bandicoot"]
  }, {
    "name": "Release Year",
    "type": "textfield",
    "data": ["1998", "1998", "1996"]
  }, {
    "name": "Completed",
    "type": "checkbox",
    "data": ["true", "true", "false"]
  }]
}
```

The `header` field is responsible for the displayed header inside the collapsible element. It is the displayed name of the table.  
The `tag` field is the html elements tag for the table. It is ***VERY*** important that every tag from all tables does exist at most once. Data my be lost after saving tables when using the same tag on multiple tables!  
The `columns` field saves all the colums, each containing of:
- the `name` of the column, displayed at the top of the table
- the `type` of the column. Depending on the type of the column, the data is interpreted and displayed in different formats.
- the `data` of the column

<br>

As seen above, there are three column types available for now: 
- `descriptor`: is basically a textfield, but has more horizontal space reserved for it and is the "primary key" of the table.  
- `textfield`: a field where text can be entered directly. Currently no special characters such as `&, $, §, %` or any other special characters are supported.  
- `checkbox`: just a boolean value, can be true or false.
