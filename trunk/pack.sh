#!/bin/sh
# $Id$

cp -af package.xml.tmpl package.xml
list=$(grep "md5sum" ./package.xml | sed 's/.*"@\|@".*//g')

for i in $list
do
	md5s=$(md5sum $i | awk '{print $1}')
	perl -pi -e "s!\@${i}\@!${md5s}!g" ./package.xml
done

curdate=$(date +'%Y-%m-%d')
curtime=$(date +'%H:%M:%S')

perl -pi -e "s!\@curdate\@!${curdate}!g" ./package.xml
perl -pi -e "s!\@curtime\@!${curtime}!g" ./package.xml

#phpdoc -s on -o HTML:Smarty:PHP -f edb.php,EDB/*.php -t docs

[ -z "$1" ] && pear package
