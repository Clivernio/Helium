#!/bin/bash

#
#
#           __   _,--="=--,_   __
#          /  \."    .-.    "./  \
#         /  ,/  _   : :   _  \/` \
#         \  `| /o\  :_:  /o\ |\__/
#          `-'| :="~` _ `~"=: |
#             \`     (_)     `/
#      .-"-.   \      |      /   .-"-.
# .---{     }--|  /,.-'-.,\  |--{     }---.
#  )  (_)_)_)  \_/`~-===-~`\_/  (_(_(_)  (
#
# sshpass is required so run `sudo apt-get install sshpass` to install it
#
HOST=""
USER=""
PASSWORD=""

if [[ $1 == "deploy1" ]]; then
echo "You are about to run DEPLOY1 task"
sshpass -p $PASSWORD ssh $USER@$HOST << EOF
    ls
EOF
elif [[ $1 == "deploy2" ]]; then
echo "You are about to run DEPLOY2 task"
sshpass -p $PASSWORD ssh $USER@$HOST << EOF
    cd html
    ls
EOF
elif [[ $1 == "deploy3" ]]; then
echo "You are about to run DEPLOY3 task"
sshpass -p $PASSWORD ssh $USER@$HOST << EOF
    cd html/wp-content
    ls
EOF
elif [[ $1 == "deploy4" && -n $2 ]]; then
echo "You are about to run DEPLOY4 task"
sshpass -p $PASSWORD ssh $USER@$HOST << EOF
    $2
EOF
else
  echo "Oops! seems you are drunk"
fi
