create database ams_db default character set utf8 collate utf8_general_ci;

use ams_db;

create table user_t(
    id int(10) unsigned not null auto_increment comment '自增主键',
    uid varchar(40) not null default '' comment '用户标识',
    username varchar(20) binary not null default '' comment '登录帐号',
    userpwd varchar(100) binary not null default '' comment '登陆密码',
    nick_name varchar(20) not null default '' comment '昵称',
    role_group tinyint(3) unsigned not null default 0 comment '用户角色标识,0查询，1出库及查询，2所有',
    is_enabled tinyint(3) unsigned not null default 0 comment '帐号状态，0禁用，1启用',
    last_time int(11) unsigned not null default 0 comment '最后登录时间',
    primary key(id)
)engine=innodb default charset=utf8 comment='用户信息表';

CREATE TABLE item_t(
	id int(10) unsigned not null auto_increment comment '自增主键',
	item_id varchar(20) not null default 0 comment '分类ID，1-2-3,分类3属于分类2，分类2属于分类1',
    item_name varchar(20) not null default '' comment '类目显示名称',
    parent_id int(10) unsigned not null default 0 comment '记录父ID',
    is_ended tinyint(3) unsigned not null default 1 comment '是否最终项，1是，0否',
    warehouse_id int(10) unsigned not null default 0 comment '对应仓库信息表warehouse_id',
    item_count int(10) unsigned not null default 0 comment '库存数量',
    primary key(id)
)engine=innodb default charset=utf8 comment='库存信息表';

create table warehouse_t(
	warehouse_id int(10) unsigned not null auto_increment comment '自增主键',
    warehouse_name varchar(50) not null default '' comment '仓库名称',
    primary key (warehouse_id)
)engine = InnoDB default charset  = utf8 comment='仓库信息表';

create table record_t(
	id int(10) unsigned not null auto_increment comment '自增主键',
    item_id int(10) unsigned not null default 0 comment '对应库存信息表id字段',
    record_status varchar(20) not null default '' comment '操作状态：in入库，out出库，lend借出，change校正',
    record_time int(11) unsigned not null default 0 comment '记录本次操作时间',
    update_count int(10)  not null default 0 comment '本次变更数量',
    consumer_code varchar(15) not null default '' comment '物品使用人工号',
    computer_barcode varchar(15) not null default '' comment '电脑资产条码',
    item_sn varchar(15) not null default '' comment '物品序列号编码',
    computer_sn varchar(15) not null default '' comment '电脑序列号',
    username varchar(20) not null default '' comment '当前操作人，对应用户信息表username字段',
    primary key(id)
)engine=innodb default charset=utf8 comment='出入库记录表';

create table check_stock_t(
	id int(10) unsigned not null auto_increment comment '自增主键',
    item_id varchar(20) not null default '' comment '对应库存信息表item_id字段',
    check_count int(10) unsigned not null default 0 comment '本次盘点的库存实际数量',
    start_time int(10) unsigned not null default 0 comment '盘点开始时间',
    end_time int(10) unsigned not null default 0 comment '盘点结束时间',
    check_status tinyint(3) unsigned not null default 0 comment '盘点状态，1正在盘点，0盘点结束',
    uid varchar(40) not null default '' comment '对应用户信息表uid字段',
    primary key(id)
)engine=innodb default charset=utf8 comment='库存盘点表';
