create database ams_db default character set utf8 collate utf8_general_ci;

create table user_t(
    id int(10) unsigned not null auto_increment comment '自增主键',
    uid varchar(40) not null default '' comment '用户标识',
    username varchar(20) not null default '' comment '登录帐号',
    password varchar(20) not null default '' comment '登陆密码',
    nick_name varchar(20) not null default '' comment '昵称',
    role_group tinyint(3) unsigned not null default 0 comment '用户角色标识',
    is_enabled tinyint(3) unsigned not null default 0 comment '帐号状态',
    last_time int(11) unsigned not null default 0 comment '最后登录时间',
    primary key(id)
)engine=innodb default charset=utf8 comment='用户信息表';


insert into user_t (username,password,nick_name) values('test','abc123','test');
