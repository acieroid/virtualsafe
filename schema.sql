create database secu;
use secu;

create table admin(
       id integer not null auto_increment,
       -- the login name
       name varchar(100),
       -- SHA-256 hash
       password varchar(32),
       primary key(id));

create table user(
       id integer not null auto_increment,
       -- the login name
       -- the user certificate is stored in md5(name)/certificate.crt
       name varchar(100),
       -- SHA-256 hash
       password varchar(32), 
       -- is this user already validated?
       valid boolean,
       primary key(id));

create table file(
       id integer not null auto_increment,
       -- the owner (creator) of this file
       owner_id integer,
       -- the user who can read this file
       user_id integer,
       -- the name of the file (user point of view)
       -- on the server, the file is stored in
       -- md5(username)/md5(filename) and the signature in
       -- md5(username)/md5(filename).sign
       filename varchar(100),
       primary key(id),
       foreign key (owner_id) references user(id) on delete cascade,
       foreign key (user_id) references user(id) on delete cascade);

create user 'www'@'localhost' identified by 'password'; -- TODO: change password
grant select on secu.admin to 'www';
grant select, insert, update, delete on secu.user to 'www';
grant select, insert, update, delete on secu.file to 'www';

create user 'admin'@'localhost' identified by 'password'; -- TODO: change password
grant select, insert, update, delete on secu.admin to 'admin';

