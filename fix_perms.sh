mkdir app/media
mkdir app/tmp
mkdir app/webroot/js
mkdir app/webroot/img
mkdir app/webroot/css
mkdir app/webroot/files
mkdir core/cake/console/cake
mkdir core/vendors/securimage

find app/media -type d -exec chmod 777 {} \;
find app/tmp -type d -exec chmod 777 {} \;
find app/vendors/shells/cron.sh -type d -exec chmod 777 {} \;
find app/vendors/shells/cron.php -type d -exec chmod 777 {} \;
find core/cake/console/cake -type d -exec chmod 777 {} \;
find core/vendors/securimage -type d -exec chmod 777 {} \;
touch core/vendors/shells/cron.sh
chmod 777 core/vendors/shells/cron.sh
touch core/vendors/shells/cron.php
chmod 777 core/vendors/shells/cron.php
chmod 777 core/cake/console/cake
