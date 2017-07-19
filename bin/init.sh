#!/bin/bash

curdir=`dirname $0`

case $1 in
    start)
    if ! pgrep -f "php $curdir/worker.php Scrape" > /dev/null
    then
        echo "Starting Scrape worker";
        php $curdir/worker.php Scrape & 2>&1 >/dev/null
    fi

    if ! pgrep -f "php $curdir/worker.php SEOMoz" > /dev/null
    then
        echo "Starting SEOMoz worker";
        php $curdir/worker.php SEOMoz & 2>&1 >/dev/null
    fi

    if ! pgrep -f "php $curdir/worker.php Sitemap" > /dev/null
    then
        echo "Starting Sitemap worker";
        php $curdir/worker.php Sitemap & 2>&1 >/dev/null
    fi
    ;;

    stop)
    pkill -f "$curdir/worker.php"
    ;;

    restart)
    $0 stop
    $0 start
    ;;

    *)
    echo "Usage: $0 <start|stop|restart>"
    exit 0
    ;;
esac