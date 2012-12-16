-- IMPORTANT: don't forget to change the password
create user 'www'@'localhost' identified by 'password';
grant select on secu.admin to 'www';
grant select, insert, update, delete on secu.user to 'www';
grant select, insert, update, delete on secu.file to 'www';
grant select, insert, update, delete on secu.share to 'www';

-- IMPORTANT: don't forget to change the password
create user 'admin'@'localhost' identified by 'password';
grant select, insert, update, delete on secu.admin to 'admin';

