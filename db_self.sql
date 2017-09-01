/********** RBAC *************/

DROP TABLE if EXISTS p39_privilege;
CREATE TABLE p39_privilege
(
  id mediumint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  pri_name VARCHAR(150) NOT NULL COMMENT '权限名称',
  module_name VARCHAR(30) NOT NULL DEFAULT '' COMMENT '模块名称',
  controller_name VARCHAR(30) NOT NULL DEFAULT '' COMMENT '控制器名称',
  action_name VARCHAR(30) NOT NULL DEFAULT '' COMMENT '方法名称',
  parent_id mediumint unsigned NOT NULL DEFAULT '0' COMMENT '上级权限ID',
  PRIMARY KEY (id)
)engine=InnoDB DEFAULT CHARSET=utf8 COMMENT '权限';


DROP TABLE IF EXISTS p39_role_pri;
CREATE TABLE p39_role_pri
(
  pri_id mediumint unsigned NOT NULL COMMENT '权限ID',
  role_id mediumint unsigned NOT NULL COMMENT '角色ID',
  key pri_id(pri_id),
  key role_id(role_id)
)engine=InnoDB DEFAULT CHARSET=utf8 COMMENT '权限';


DROP TABLE IF EXISTS p39_role;
CREATE TABLE p39_role
(
  id mediumint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  role_name varchar(30) NOT NULL COMMENT '角色名称',
  PRIMARY KEY (id)
)engine=InnoDB DEFAULT CHARSET=utf8 COMMENT '角色';


DROP TABLE IF EXISTS p39_admin_role;
CREATE TABLE p39_admin_role
(
  admin_id mediumint unsigned NOT NULL COMMENT '管理员ID',
  role_id varchar(30) NOT NULL COMMENT '角色ID',
  key admin_id(admin_id),
  key role_id(role_id)
)engine=InnoDB DEFAULT CHARSET=utf8 COMMENT '管理员角色';


DROP TABLE IF EXISTS p39_admin;
CREATE TABLE p39_admin
(
  id mediumint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  username varchar(30) NOT NULL COMMENT '用户名',
  password varchar(32) NOT NULL COMMENT '密码',
  PRIMARY KEY (id)
)engine=InnoDB DEFAULT CHARSET=utf8 COMMENT '管理员';
INSERT INTO p39_admin VALUES (1,'root','63a9f0ea7bb98050796b649e85481845');
