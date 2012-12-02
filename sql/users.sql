create user 'www'@'localhost' identified by 'password'; -- TODO: change password
grant select on secu.admin to 'www';
grant select, insert, update, delete on secu.user to 'www';
grant select, insert, update, delete on secu.file to 'www';

create user 'admin'@'localhost' identified by 'password'; -- TODO: change password
grant select, insert, update, delete on secu.admin to 'admin';

