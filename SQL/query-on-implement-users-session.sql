use blog;
create table users(id int not null auto_increment, name varchar(32), password varchar(64), primary key (id));
alter table articles add user_id int after id;