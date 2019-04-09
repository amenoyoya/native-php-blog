use blog;
create table users(id int not null auto_increment, name varchar(32), password varchar(32), primary key (id));
alter table articles add user_id int after id;