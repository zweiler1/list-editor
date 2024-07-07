#!/bin/sh

# $1 path to directory containing csv files
# $2 path to output directory

echo wololo
[ ! -d "$1" ] || [ ! -d "$2" ] && echo "Specified directories do not exist!" >&2 && exit 1
echo wololo2
find "$1" -type f -name "*.csv" -exec sh -c './convert_csv_to_json.sh $0 '"$2" {} \;

# Easy readable alternative
#FILES="$(ls "$1")"
#echo "$FILES"
#for f in $FILES
#do
#    ./convert_csv_to_json.sh "$1/$f" "$2"
#done