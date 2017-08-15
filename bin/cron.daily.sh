#!/bin/bash

ROOT=`dirname $0`

$ROOT/import.sh
$ROOT/deploy.sh

exit 0