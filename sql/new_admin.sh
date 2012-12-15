#!/bin/sh
if [ "$#" != 1 ]; then
  echo "One argument expected (the name of the admin)"
  exit
fi
NAME="$1"
echo -n "Password: "
read -s PASSWORD

SALT="`head /dev/urandom| md5sum | cut -d' ' -f1`"
HASH="`echo "$NAME|$PASSWORD|$SALT" | sha256sum | cut -d' ' -f1`"

echo
echo "Enter the following line in MySQL:"
echo "insert into admin(name, password, salt) values ('$NAME', '$HASH', '$SALT');"

