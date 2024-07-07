#!/bin/bash

# $1 = path to csv file
# $2 = path to the output directory

SCRIPT=$(realpath "$0")
SCRIPTPATH=$(dirname "$SCRIPT")
OUTPUT_DIR="$2"

printUsage() {
    echo "Usage:
    convert_csv_to_json.sh CSV_PATH [OUTPUT_DIR]"
}

# printError: Print error message and exit with error code 1
printError() {
    echo "Error: $1" >&2
    exit 1
}

./clean_csv.sh "$1" || printError "Failed to clean CSV file"

# Check if the output directory is specified, if not the current directory is used
if [ -z "$2" ]; then
    OUTPUT_DIR="$SCRIPTPATH/editor/data"
fi

# Check if the directory the output is specified and exists
if [ ! -d "$(dirname "$OUTPUT_DIR")" ]; then
    printError "Output directory does not exist"
fi

echo "Converting CSV to JSON..."
fileName=$(basename "$1" | rev | cut -d '.' -f 2- | rev)

echo -n '{
    "data":
    [' > "$OUTPUT_DIR/$fileName.json"

lineNum=0
while IFS="" read -r line || [ -n "$line" ]; do
    if [ $lineNum -eq 0 ]; then
        lineNum=$((lineNum + 1))
        continue
    fi

    name=$(echo -n "$line" | cut -d ',' -f 1)
    year=$(echo -n "$line" | cut -d ',' -f 2)
    type=$(echo -n "$line" | cut -d ',' -f 3)
    name1=$(echo -n "$line" | cut -d ',' -f 4)
    name2=$(echo -n "$line" | cut -d ',' -f 5)
    name3=$(echo -n "$line" | cut -d ',' -f 6)
    name4=$(echo -n "$line" | cut -d ',' -f 7)

    case "$name1" in
        *true*) name1='"true"';;
        *) name1='"-"';;
    esac
    case "$name2" in
        *true*) name2='"true"';;
        *) name2='"-"';;
    esac
    case "$name3" in
        *true*) name3='"true"';;
        *) name3='"-"';;
    esac
    case "$name4" in
        *true*) name4='"true"';;
        *) name4='"-"';;
    esac

    echo -n "{
        \"name\": \"$name\",
        \"year\": \"$year\",
        \"type\": \"$type\",
        \"name1\": $name1,
        \"name2\": $name2,
        \"name3\":$name3,
        \"name4\": $name4
    }," >> "$OUTPUT_DIR/$fileName.json"
    lineNum=$((lineNum + 1))
done < "$1"
echo "]
}" >> "$OUTPUT_DIR/$fileName.json"

# Remove the last comma in the whole file. Why does this work? I have no idea.
sed -i '1h;1!H;$!d;g;s/\(.*\),/\1/' "$OUTPUT_DIR/$fileName.json"
