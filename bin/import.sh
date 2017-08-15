#!/bin/bash

date
runJob="php $(dirname $0)/run-job.php"

echo "Running ImportGeoJson"
$runJob ImportGeoJson
echo ""

echo "Running ImportMembers"
$runJob ImportMembers
echo ""

echo "Running ImportHeadshots"
$runJob ImportHeadshots
echo ""


exit 0