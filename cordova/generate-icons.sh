#!/bin/bash
##
# Note: Icon must be >192px

root=`dirname $0`
logo="$root/res/icon.png"

# android
mkdir -p "$root/res/icon/android"
convert $logo -resize 36x36 "$root/res/icon/android/ldpi.png"
convert $logo -resize 48x48 "$root/res/icon/android/mdpi.png"
convert $logo -resize 72x72 "$root/res/icon/android/hdpi.png"
convert $logo -resize 96x96 "$root/res/icon/android/xhdpi.png"
convert $logo -resize 144x144 "$root/res/icon/android/xxhdpi.png"
convert $logo -resize 192x192 "$root/res/icon/android/xxxhdpi.png"

#androidPath="$root/platforms/android/res"
#mkdir -p $androidPath
#convert $logo -resize 36x36 "$androidPath/mipmap-ldpi/icon.png"
#convert $logo -resize 48x48 "$androidPath/mipmap-mdpi/icon.png"
#convert $logo -resize 72x72 "$androidPath/mipmap-hdpi/icon.png"
#convert $logo -resize 96x96 "$androidPath/mipmap-xhdpi/icon.png"

# ios
mkdir -p "$root/res/icon/ios"
convert $logo -resize 180x180 "$root/res/icon/ios/icon-60@3x.png"
convert $logo -resize 60x60 "$root/res/icon/ios/icon-60.png"
convert $logo -resize 120x120 "$root/res/icon/ios/icon-60@2x.png"
convert $logo -resize 76x76 "$root/res/icon/ios/icon-76.png"
convert $logo -resize 152x152 "$root/res/icon/ios/icon-76@2x.png"
convert $logo -resize 40x40 "$root/res/icon/ios/icon-40.png"
convert $logo -resize 80x80 "$root/res/icon/ios/icon-40@2x.png"
convert $logo -resize 57x57 "$root/res/icon/ios/icon.png"
convert $logo -resize 114x114 "$root/res/icon/ios/icon@2x.png"
convert $logo -resize 72x72 "$root/res/icon/ios/icon-72.png"
convert $logo -resize 144x144 "$root/res/icon/ios/icon-72@2x.png"
convert $logo -resize 29x29 "$root/res/icon/ios/icon-small.png"
convert $logo -resize 58x58 "$root/res/icon/ios/icon-small@2x.png"
convert $logo -resize 50x50 "$root/res/icon/ios/icon-50.png"
convert $logo -resize 100x100 "$root/res/icon/ios/icon-50@2x.png"
convert $logo -resize 167x167 "$root/res/icon/ios/icon-83.5@2x.png"

iosPath="$root/platforms/ios/Awayn/Images.xcassets/AppIcon.appiconset"
mkdir -p $iosPath
convert $logo -resize 40x40   "$iosPath/icon-40.png"
convert $logo -resize 80x80   "$iosPath/icon-40@2x.png"
convert $logo -resize 50x50   "$iosPath/icon-50.png"
convert $logo -resize 100x100 "$iosPath/icon-50@2x.png"
convert $logo -resize 60x60   "$iosPath/icon-60.png"
convert $logo -resize 120x120 "$iosPath/icon-60@2x.png"
convert $logo -resize 180x180 "$iosPath/icon-60@3x.png"
convert $logo -resize 72x72   "$iosPath/icon-72.png"
convert $logo -resize 144x144 "$iosPath/icon-72@2x.png"
convert $logo -resize 76x76   "$iosPath/icon-76.png"
convert $logo -resize 152x152 "$iosPath/icon-76@2x.png"
convert $logo -resize 167x167 "$iosPath/icon-83.5@2x.png"
convert $logo -resize 29x29   "$iosPath/icon-small.png"
convert $logo -resize 58x58   "$iosPath/icon-small@2x.png"
convert $logo -resize 87x87   "$iosPath/icon-small@3x.png"
convert $logo -resize 57x57   "$iosPath/icon.png"
convert $logo -resize 114x114 "$iosPath/icon@2x.png"