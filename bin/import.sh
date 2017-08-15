#!/bin/bash

date
runJob="time php $(dirname $0)/run-job.php"

echo "Running ImportGeoJson"
$runJob ImportGeoJson

echo "Running ImportMembers"
$runJob ImportMembers

echo "Running ImportHeadshots"
$runJob ImportHeadshots


exit 0