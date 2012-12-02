-- Admin, name: 'foo', password: 'bar'
insert into admin(name, password, salt) values ('foo', 'db3db8cc7d0625272eebb0b2b1ce5719c54fed38ca0dff0d0b18a26f9878a48b', 'acbd18db4cc2f85cedef654fccc4a4d8');

-- Valid user, name: 'foo', password: 'bar'
insert into user(name, password, salt, valid) values ('foo', 'b8b181d5884b744d768179fe24c4132dc03345741421314c95e07042f25ee365', '37b51d194a7513e45b56f6524f2d51f2', true);
-- Invalid user, name: 'bar', password: 'baz'
insert into user(name, password, salt, valid) values ('bar', '0de304ed0cadd7ba69f190e94102cd3b503ef71ca2571daafc2db5f27cfc7da0', '73feffa4b7f6bb68e44cf984c85f6e88', false);
