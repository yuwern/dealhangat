SOURCE="user@dealhangat.com:~/public_staging"

rsync -avzpP $SOURCE/app/webroot/img/big_thumb app/webroot/img/
rsync -avzpP $SOURCE/app/webroot/img/medium_big_thumb app/webroot/img/
rsync -avzpP $SOURCE/app/webroot/img/medium_thumb app/webroot/img/
rsync -avzpP $SOURCE/app/webroot/img/normal_thumb app/webroot/img/
rsync -avzpP $SOURCE/app/webroot/img/micro_thumb app/webroot/img/
rsync -avzpP $SOURCE/app/webroot/img/small_big_thumb app/webroot/img/
rsync -avzpP $SOURCE/app/webroot/img/small_thumb app/webroot/img/
rsync -avzpP $SOURCE/app/media app/media