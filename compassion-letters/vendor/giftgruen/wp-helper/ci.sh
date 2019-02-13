#!/bin/bash

LD="$(pwd)/docs"
RD="/"

apigen generate -s src -d docs --template-theme bootstrap || exit

mv docs/index.html docs/docs.html
./render.rb README.md > docs/index.html

lftp -c "set ftp:list-options -a;
open ftp://$FTP_USER:$FTP_PW@$FTP_SERVER;
lcd $LD;
cd $RD;
mirror --reverse --delete --use-cache --verbose --allow-chown --allow-suid --no-umask --parallel=2" || exit
