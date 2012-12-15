create table admin(
       id integer not null auto_increment,
       -- the login name
       name varchar(100),
       -- SHA-256 hash
       password varchar(256),
       -- salt used for hashing the password
       salt varchar(256),
       primary key(id),
       unique key(id));

create table user(
       id integer not null auto_increment,
       -- the login name
       -- the user certificate is stored in md5(name)/certificate.crt
       -- TODO: we might need two pairs of keys, one for the
       -- certificate and one for encrypting the files (why?)
       name varchar(100),
       -- SHA-256 hash
       password varchar(256), 
       -- salt used for hashing the password
       salt varchar(256),
       -- is this user already validated?
       valid boolean,
       primary key(id));

create table file(
       id integer not null auto_increment,
       -- the owner of this file
       user_id integer,
       -- the name of the file (user point of view)
       -- on the server, the file is stored in
       -- md5(username)/md5(filename) and the signature in
       -- md5(username)/md5(filename).sign
       -- the file is encrypted with a random secret key, which is
       -- stored encrypted in md5(username)/md5(filename).key
       filename varchar(100),
       primary key(id),
       unique key(user_id, filename), -- the same user can't have two files with the same name
       foreign key (user_id) references user(id) on delete cascade);

create table share(
       -- the file
       file_id integer,
       -- the owner of this file
       owner_id integer,
       -- the user who can read this file
       user_id integer,
       foreign key (file_id) references file(id) on delete cascade,
       foreign key (owner_id) references user(id) on delete cascade,
       foreign key (user_id) references user(id) on delete cascade);
