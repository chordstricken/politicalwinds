#!/bin/bash
ROOT=`dirname $0`

aws s3 sync $ROOT/../api s3://politics.blackmast.org/api
aws s3 sync $ROOT/../cordova/www/ s3://politics.blackmast.org/
