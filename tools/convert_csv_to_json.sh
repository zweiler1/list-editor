#!/usr/bin/env bash

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

getType() {
    if [ "$(echo "$1" | grep -i 'true')" == "true" ] || 
        [ "$(echo "$1" | grep -i 'false')" == "false" ]; then
        echo "checkbox"
    elif [ "$1" != "" ]; then
        echo "textfield"
    # How to differentiate beween textfield and descriptor? I think it is not possible easily...
    # elif [ "$(echo "$1" | grep -E '')" != "" ]; then
    #     echo "descriptor"
    else
        echo "undefined"
    fi
}

lineBreak() {
    case $1 in
        descriptor) echo 3 ;;
        textfield) echo 6 ;;
        checkbox) echo 12 ;;
        *) echo 1 ;;
    esac
}

fileName=$(basename "$1" | rev | cut -d '.' -f 2- | rev)
echo "Current File: $fileName"
./clean_csv.sh "$1" || printError "Failed to clean CSV file"

# Check if the output directory is specified, if not the current directory is used
if [ -z "$2" ]; then
    OUTPUT_DIR="$SCRIPTPATH/json-out"
fi

# Check if the directory the output is specified and exists
if [ ! -d "$(dirname "$OUTPUT_DIR")" ]; then
    printError "Output directory does not exist"
fi

echo "    Converting CSV to JSON..."

firstLine="$(head "$1" -n1)"
secondLine="$(head "$1" -n2 | tail -n1)"
entryCount="$(echo "$firstLine" | sed 's/[^,]*//g' | wc -c)"
IFS=" " read -r -a headers <<< "$(echo -n "$firstLine" | sed 's/[^a-zA-Z0-9]*//' | sed 's/,/ /g')"
declare -a types
declare -i index=0
while [ $index -lt "$entryCount" ]; do
    type="$(getType "$(echo -n "$secondLine" | cut -d ',' -f$((index + 1)))")"
    types+=("$type")
    if [ $index -eq 0 ]; then
        types[index]="descriptor"
    fi
    index+=1
done

index=0
declare -i lineNum=0
{ echo -n "{
    \"header\": \"$fileName\",
    \"tag\": \"$fileName\",
    \"columns\": ["
while [ $index -lt "$entryCount" ]; do
    [ $index -gt 0 ] && echo -n ", "
    echo -n "{
        \"name\": \"${headers[$index]}\",
        \"type\": \"${types[$index]}\",
        \"data\": ["
    lineNum=0
    declare -i lineBreaks
    lineBreaks="$(lineBreak "${types[$index]}")"
    while IFS="" read -r line || [ -n "$line" ]; do
        if ! [ $lineNum -eq 0 ]; then
            [ $lineNum -gt 1 ] && echo -n ', '
            if [ $(((lineNum - 1) % lineBreaks)) -eq 0 ]; then
                echo
                echo -n "            "
            fi
            echo -n "\"$(echo -n "$line" | cut -d ',' -f"$((index + 1))")\""
        fi
        lineNum+=1
    done < "$1"
    echo -n " ]
    }"
    index+=1
done 
echo " ]
}"
} > "$OUTPUT_DIR/$fileName.json"
# Remove the last comma in the whole file. Why does this work? I have no idea.
#sed -i '1h;1!H;$!d;g;s/\(.*\),/\1/' "$OUTPUT_DIR/$fileName.json"
