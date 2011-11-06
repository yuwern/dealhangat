mkdir app/media
find app/media -type d -exec chmod 777 {} \;

mkdir app/tmp
mkdir app/tmp/cache
mkdir app/tmp/cache/views
mkdir app/tmp/cache/persistent
mkdir app/tmp/cache/models
find app/tmp -type d -exec chmod 777 {} \;

mkdir app/webroot/js
mkdir app/webroot/img
mkdir app/webroot/css
mkdir app/webroot/files

mkdir core/cake/console/cake
find core/cake/console/cake -type d -exec chmod 777 {} \;

mkdir core/vendors/securimage
find core/vendors/securimage -type d -exec chmod 777 {} \;

find app/vendors/shells/cron.sh -type d -exec chmod 777 {} \;
find app/vendors/shells/cron.php -type d -exec chmod 777 {} \;

touch core/vendors/shells/cron.sh
chmod 777 core/vendors/shells/cron.sh

touch core/vendors/shells/cron.php
chmod 777 core/vendors/shells/cron.php