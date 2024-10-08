#!/usr/bin/env bash

# printError: Print error message and exit with error code 1
# $1: Error message
printError() {
    echo "Error: $1" >&2
    exit 1
}

# Check if the csv file exists
if [ ! -f "$1" ]; then
    printError "CSV file not found at '$1'"
fi

count=0
maxCount=10
echo "    Cleaning CSV file..."
INSERT_MINUS='s/,,/,-,/g'
ADD_MINUS_END='s/$/-/'
# delete all lines in the csv file which only contain 
# AND change empty spaces between commas with a '-'
while IFS="" read -r line || [ -n "$line" ]; do
    # Checks if the line is full of commas or not
    if ! [ "$(echo "$line" | grep "[^,][,]")" = "" ]; then
        # Check if there are multiple commas in the line
        if [ "$(echo "$line" | grep ',,')" != "" ]; then
            if [ "$(echo -n "$line" | tail -c 1)" = ',' ]; then
                echo "$line" | sed "$INSERT_MINUS" | sed "$INSERT_MINUS" | sed "$ADD_MINUS_END" >> './clean.csv'
            else
                echo "$line" | sed "$INSERT_MINUS" | sed "$INSERT_MINUS" >> './clean.csv'
            fi
        else
            echo "$line" >> './clean.csv'
        fi
    elif [ $count -ge $maxCount ]; then
        break
    else
        count=$((count + 1))
    fi
done < "$1"

# Removing all commas inside of names, surrounded by " signs
foundQuotes="$(grep -E '"[^"]*"' < './clean.csv' | sed 's/.*\("[^"]*"\).*/\1/g')"
declare -a originalQuotes
declare -a quotes

while IFS="" read -r line; do
    originalQuotes+=("$line")
    temp="$(echo -n "$line" | tr -d '"' | tr -d ',')"
    if [ "$temp" != "" ]; then
        quotes+=("$temp")
    fi
done < <(echo "$foundQuotes")

declare -i length=${#quotes[@]}
declare -i index=0
while [ $index -lt $length ]; do
    sed -i "s/${originalQuotes[$index]}/${quotes[$index]}/g" './clean.csv'
    index+=1
done

mv './clean.csv' "$1"