use blog;
create table tags(id int not null auto_increment, name varchar(256), primary key (id));
create table articles_tags(article_id int, tag_id int);