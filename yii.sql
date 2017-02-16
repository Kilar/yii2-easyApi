/*
Navicat MySQL Data Transfer

Source Server         : haproxy
Source Server Version : 50716
Source Host           : localhost:33060
Source Database       : yii

Target Server Type    : MYSQL
Target Server Version : 50716
File Encoding         : 65001

Date: 2017-02-17 00:37:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `app_version_record`
-- ----------------------------
DROP TABLE IF EXISTS `app_version_record`;
CREATE TABLE `app_version_record` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `api_version` varchar(30) NOT NULL DEFAULT '' COMMENT '接口版本',
  `app_version` varchar(30) NOT NULL DEFAULT '' COMMENT '应用版本号',
  `platform` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '1、安卓：2、ios：3、开放接口',
  `local_key` char(16) NOT NULL DEFAULT '' COMMENT '客户端本地验签密钥',
  `fix_question` text COMMENT '修复问题',
  `update_content` text COMMENT '修改类容',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `version` (`app_version`,`platform`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_version_record
-- ----------------------------
INSERT INTO `app_version_record` VALUES ('4', 'v1', '1.0.0.0', '1', 'KbOlJHdNq6UF07mP', '钱钱钱钱钱钱钱钱钱\r\n是是是是是是是是是\r\n烦烦烦烦烦烦烦烦烦\r\n光顾光顾光顾光顾谷\r\n哈哈哈哈哈哈哈哈哈', '钱钱钱钱钱钱钱钱钱\r\n是是是是是是是是是\r\n烦烦烦烦烦烦烦烦烦\r\n光顾光顾光顾光顾谷\r\n哈哈哈哈哈哈哈哈哈', '0', '0');
INSERT INTO `app_version_record` VALUES ('5', 'v2', '1.0.0.1', '1', 'RZzXM3SPdpZDyxQX', '111\r\n222\r\n333\r\n555', '666\r\n777\r\n888\r\n999', '1486560584', '1486560584');
INSERT INTO `app_version_record` VALUES ('6', 'v1', '1.0.0', '2', 'JY0tUWeshuLR6PeR', '', '', '1486969534', '1486969534');

-- ----------------------------
-- Table structure for `auth_assignment`
-- ----------------------------
DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `auth_user_ibfk_1` (`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_assignment
-- ----------------------------
INSERT INTO `auth_assignment` VALUES ('admin', '1', '1479642689');
INSERT INTO `auth_assignment` VALUES ('bbbbbbbbb', '7', '1481122126');
INSERT INTO `auth_assignment` VALUES ('bbbbbbbbb', '9', '1478441288');
INSERT INTO `auth_assignment` VALUES ('bbbbbbbbb', '12', '1478021892');
INSERT INTO `auth_assignment` VALUES ('bbbbbbbbb', '15', '1478021566');
INSERT INTO `auth_assignment` VALUES ('bbbbbbbbb', '16', '1478021900');
INSERT INTO `auth_assignment` VALUES ('bbbbbbbbb', '17', '1478345142');
INSERT INTO `auth_assignment` VALUES ('bbbbbbbbb', '18', '1479553569');
INSERT INTO `auth_assignment` VALUES ('ddd', '9', '1478441288');
INSERT INTO `auth_assignment` VALUES ('ddd', '11', '1478440813');
INSERT INTO `auth_assignment` VALUES ('ddd', '17', '1478345142');
INSERT INTO `auth_assignment` VALUES ('ddd', '18', '1479553569');
INSERT INTO `auth_assignment` VALUES ('dfdfgdfg', '10', '1478021919');
INSERT INTO `auth_assignment` VALUES ('dfgfg', '7', '1481122126');
INSERT INTO `auth_assignment` VALUES ('dfgfg', '10', '1478021919');
INSERT INTO `auth_assignment` VALUES ('test', '8', '1478345152');

-- ----------------------------
-- Table structure for `auth_item`
-- ----------------------------
DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` text,
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_item
-- ----------------------------
INSERT INTO `auth_item` VALUES ('admin', '1', '超级管理员', null, null, '1452785759', '1455099960');
INSERT INTO `auth_item` VALUES ('app/version/index', '2', '版本列表', null, null, '1486532317', '1486549338');
INSERT INTO `auth_item` VALUES ('bbbbbbbbb', '1', '对方的风格', null, '', '1477415342', '1478874497');
INSERT INTO `auth_item` VALUES ('ddd', '1', '法国和法国后', null, '', '1478182950', '1479909582');
INSERT INTO `auth_item` VALUES ('debug/default/toolbar', '2', 'debug调试栏', null, null, '1479613859', '1479613859');
INSERT INTO `auth_item` VALUES ('debug/default/view', '2', 'debug调试页面', null, null, '1479613818', '1479613818');
INSERT INTO `auth_item` VALUES ('dfdfgdfg', '1', '非官方的高', null, '', '1477415334', '1478441678');
INSERT INTO `auth_item` VALUES ('dfgfg', '1', '是对方的事', null, '', '1477593109', '1479610000');
INSERT INTO `auth_item` VALUES ('dsfsdf', '2', 'sdfsdf', null, null, '1487091782', '1487091782');
INSERT INTO `auth_item` VALUES ('gridview/export/download', '2', '表格excel导出', null, null, '1479631355', '1479631355');
INSERT INTO `auth_item` VALUES ('log/login-log', '2', '用户登录日志', null, null, '1478329947', '1478329947');
INSERT INTO `auth_item` VALUES ('log/operation-log', '2', '系统操作日志', null, null, '1478023168', '1478359609');
INSERT INTO `auth_item` VALUES ('null', '2', '无操作菜单', null, null, '1453896093', '1455423515');
INSERT INTO `auth_item` VALUES ('rbac/menu/bulk-delete', '2', '删除多个菜单', null, null, '1453890657', '1453890657');
INSERT INTO `auth_item` VALUES ('rbac/menu/create', '2', '菜单添加', null, null, '1455785051', '1455785051');
INSERT INTO `auth_item` VALUES ('rbac/menu/delete', '2', '删除菜单', null, null, '1453878273', '1453878273');
INSERT INTO `auth_item` VALUES ('rbac/menu/get-menu', '2', '级别菜单获取', null, null, '1458535033', '1478702258');
INSERT INTO `auth_item` VALUES ('rbac/menu/index', '2', '菜单管理', null, null, '1451219013', '1451219013');
INSERT INTO `auth_item` VALUES ('rbac/menu/update', '2', '修改菜单', null, null, '1453878194', '1453878194');
INSERT INTO `auth_item` VALUES ('rbac/menu/view', '2', '查看菜单详情', null, null, '1453878241', '1453878241');
INSERT INTO `auth_item` VALUES ('rbac/role/bulk-delete', '2', '删除多个角色', null, null, '1454566308', '1454566308');
INSERT INTO `auth_item` VALUES ('rbac/role/create', '2', '角色添加', null, null, '1455784816', '1455784816');
INSERT INTO `auth_item` VALUES ('rbac/role/delete', '2', '删除角色', null, null, '1454566207', '1454566207');
INSERT INTO `auth_item` VALUES ('rbac/role/index', '2', '角色管理', null, null, '1451218992', '1451218992');
INSERT INTO `auth_item` VALUES ('rbac/role/update', '2', '修改角色信息', null, null, '1454566274', '1454566274');
INSERT INTO `auth_item` VALUES ('rbac/role/view', '2', '查看角色信息', null, null, '1454566239', '1454566239');
INSERT INTO `auth_item` VALUES ('rbac/user/bulk-delete', '2', '删除多个用户', null, null, '1454565973', '1454565973');
INSERT INTO `auth_item` VALUES ('rbac/user/create', '2', '创建用户', null, null, '1454565869', '1454565869');
INSERT INTO `auth_item` VALUES ('rbac/user/delete', '2', '删除用户', null, null, '1456916369', '1456916369');
INSERT INTO `auth_item` VALUES ('rbac/user/index', '2', '用户管理', null, null, '1451219003', '1451219003');
INSERT INTO `auth_item` VALUES ('rbac/user/update', '2', '修改用户信息', null, null, '1454565802', '1454565802');
INSERT INTO `auth_item` VALUES ('rbac/user/view', '2', '查看用户信息', null, null, '1454566047', '1454566047');
INSERT INTO `auth_item` VALUES ('test', '1', '测试用户', null, '', '1478344876', '1483585561');
INSERT INTO `auth_item` VALUES ('刚回家1', '2', '捣鼓捣鼓df', null, null, '1479607025', '1483521902');
INSERT INTO `auth_item` VALUES ('大概很反感', '2', '测试菜单二', null, null, '1478102980', '1478615299');
INSERT INTO `auth_item` VALUES ('看见好看', '2', '的复古风格', null, null, '1479607044', '1479607044');

-- ----------------------------
-- Table structure for `auth_item_child`
-- ----------------------------
DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_item_child
-- ----------------------------
INSERT INTO `auth_item_child` VALUES ('test', 'log/login-log');
INSERT INTO `auth_item_child` VALUES ('test', 'log/operation-log');
INSERT INTO `auth_item_child` VALUES ('test', 'rbac/menu/index');
INSERT INTO `auth_item_child` VALUES ('test', 'rbac/menu/view');
INSERT INTO `auth_item_child` VALUES ('test', 'rbac/role/index');
INSERT INTO `auth_item_child` VALUES ('test', 'rbac/role/view');
INSERT INTO `auth_item_child` VALUES ('test', 'rbac/user/index');
INSERT INTO `auth_item_child` VALUES ('test', 'rbac/user/view');
INSERT INTO `auth_item_child` VALUES ('ddd', '刚回家1');
INSERT INTO `auth_item_child` VALUES ('test', '刚回家1');
INSERT INTO `auth_item_child` VALUES ('bbbbbbbbb', '大概很反感');
INSERT INTO `auth_item_child` VALUES ('ddd', '大概很反感');
INSERT INTO `auth_item_child` VALUES ('dfdfgdfg', '大概很反感');
INSERT INTO `auth_item_child` VALUES ('test', '大概很反感');
INSERT INTO `auth_item_child` VALUES ('ddd', '看见好看');
INSERT INTO `auth_item_child` VALUES ('test', '看见好看');

-- ----------------------------
-- Table structure for `auth_menu`
-- ----------------------------
DROP TABLE IF EXISTS `auth_menu`;
CREATE TABLE `auth_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `level` tinyint(1) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `uri` varchar(64) NOT NULL DEFAULT 'null' COMMENT '操作路由',
  `sort` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '菜单排序',
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uri` (`uri`),
  CONSTRAINT `auth_menu_ibfk_1` FOREIGN KEY (`uri`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_menu
-- ----------------------------
INSERT INTO `auth_menu` VALUES ('1', '0', '1', '系统管理', 'null', '0', '1451219278', '1478609325');
INSERT INTO `auth_menu` VALUES ('3', '1', '2', '权限分配', 'null', '0', '1451218748', '1477797735');
INSERT INTO `auth_menu` VALUES ('5', '3', '3', '角色管理', 'rbac/role/index', '2', '1451218992', '1477797759');
INSERT INTO `auth_menu` VALUES ('6', '3', '3', '用户管理', 'rbac/user/index', '1', '1451219003', '1477797750');
INSERT INTO `auth_menu` VALUES ('7', '3', '3', '菜单管理', 'rbac/menu/index', '0', '1451219013', '1477797595');
INSERT INTO `auth_menu` VALUES ('24', '7', '4', '修改菜单信息', 'rbac/menu/update', '0', '1453878194', '0');
INSERT INTO `auth_menu` VALUES ('25', '7', '4', '查看菜单详情', 'rbac/menu/view', '0', '1453878240', '0');
INSERT INTO `auth_menu` VALUES ('26', '7', '4', '删除菜单', 'rbac/menu/delete', '0', '1453878272', '0');
INSERT INTO `auth_menu` VALUES ('27', '7', '4', '删除多个菜单', 'rbac/menu/bulk-delete', '0', '1453890657', '0');
INSERT INTO `auth_menu` VALUES ('34', '6', '4', '修改用户信息', 'rbac/user/update', '0', '1454565802', '0');
INSERT INTO `auth_menu` VALUES ('35', '6', '4', '创建用户', 'rbac/user/create', '0', '1454565869', '0');
INSERT INTO `auth_menu` VALUES ('36', '6', '4', '删除多个用户', 'rbac/user/bulk-delete', '0', '1454565973', '0');
INSERT INTO `auth_menu` VALUES ('37', '6', '4', '查看用户信息', 'rbac/user/view', '0', '1454566046', '0');
INSERT INTO `auth_menu` VALUES ('38', '5', '4', '删除角色', 'rbac/role/delete', '0', '1454566207', '0');
INSERT INTO `auth_menu` VALUES ('39', '5', '4', '查看角色信息', 'rbac/role/view', '0', '1454566239', '0');
INSERT INTO `auth_menu` VALUES ('40', '5', '4', '修改角色信息', 'rbac/role/update', '0', '1454566274', '0');
INSERT INTO `auth_menu` VALUES ('41', '5', '4', '删除多个角色', 'rbac/role/bulk-delete', '0', '1454566308', '0');
INSERT INTO `auth_menu` VALUES ('51', '5', '4', '角色添加', 'rbac/role/create', '0', '1455784816', '0');
INSERT INTO `auth_menu` VALUES ('52', '7', '4', '菜单添加', 'rbac/menu/create', '0', '1455785051', '0');
INSERT INTO `auth_menu` VALUES ('55', '6', '4', '删除用户', 'rbac/user/delete', '0', '1456916369', '1477794920');
INSERT INTO `auth_menu` VALUES ('56', '7', '4', '级别菜单获取', 'rbac/menu/get-menu', '0', '1458535033', '1478702258');
INSERT INTO `auth_menu` VALUES ('57', '0', '1', '测试菜单一', 'null', '2', '1477593251', '1486532496');
INSERT INTO `auth_menu` VALUES ('64', '1', '2', '日志列表', 'null', '1', '1478023091', '1478023091');
INSERT INTO `auth_menu` VALUES ('65', '64', '3', '系统操作日志', 'log/operation-log', '0', '1478023168', '1478359609');
INSERT INTO `auth_menu` VALUES ('68', '57', '2', '过会就回家', 'null', '1', '1478102172', '1478615322');
INSERT INTO `auth_menu` VALUES ('73', '68', '3', '测试菜单二', '大概很反感', '1', '1478102980', '1478615299');
INSERT INTO `auth_menu` VALUES ('74', '64', '3', '用户登录日志', 'log/login-log', '0', '1478329947', '1478329947');
INSERT INTO `auth_menu` VALUES ('89', '0', '1', '商城管理', 'null', '3', '1478704135', '1486532509');
INSERT INTO `auth_menu` VALUES ('100', '57', '2', '所发生的', 'null', '45', '1479577752', '1479578716');
INSERT INTO `auth_menu` VALUES ('107', '73', '4', '捣鼓捣鼓df', '刚回家1', '0', '1479607025', '1483521902');
INSERT INTO `auth_menu` VALUES ('108', '73', '4', '的复古风格', '看见好看', '0', '1479607044', '1479607044');
INSERT INTO `auth_menu` VALUES ('121', '1', '2', '系统操作', 'null', '3', '1479613756', '1479613756');
INSERT INTO `auth_menu` VALUES ('122', '121', '3', 'debug调试页面', 'debug/default/view', '0', '1479613818', '1479613818');
INSERT INTO `auth_menu` VALUES ('123', '122', '4', 'debug调试栏', 'debug/default/toolbar', '0', '1479613859', '1479613859');
INSERT INTO `auth_menu` VALUES ('125', '122', '4', '表格excel导出', 'gridview/export/download', '0', '1479631355', '1479631355');
INSERT INTO `auth_menu` VALUES ('126', '0', '1', 'app管理', 'null', '1', '1486532049', '1486545022');
INSERT INTO `auth_menu` VALUES ('127', '126', '2', 'app信息', 'null', '0', '1486532223', '1486532223');
INSERT INTO `auth_menu` VALUES ('128', '127', '3', '版本列表', 'app/version/index', '0', '1486532317', '1486549338');
INSERT INTO `auth_menu` VALUES ('129', '68', '3', 'sdfsdf', 'dsfsdf', '13', '1487091781', '1487091782');

-- ----------------------------
-- Table structure for `auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` text NOT NULL,
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------

-- ----------------------------
-- Table structure for `member`
-- ----------------------------
DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password_hash` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '用户密码',
  `real_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '用户手机号',
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `access_token` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '用户授权码',
  `auth_key` char(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `password_reset_token` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` tinyint(2) unsigned NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `access_token` (`access_token`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of member
-- ----------------------------
INSERT INTO `member` VALUES ('1', 'username1', '$2y$13$anRX71UuibTnQ83YOjLvaOwb7ZgUuSOVG2SUAQJavtPUmxYCPa0aS', '1231dsf3许昌', '1133333', '33334444@111.com', '1JyZYaWpt0YoN3Y9W98dVfRxnwZXoowC', 'ZqNrJZ8C2cjQCR2sdrxO-QZ71fRl25-d3I0EGVne9qo1q27-ZifYUjQlyuwF2fXn', '', '1', '1486722263', '1487071209');
INSERT INTO `member` VALUES ('2', 'username2', '$2y$13$mCtBpDTIHNHoJzp/BDyWO.nu/vX4VJpYFIUib6kZLqLtZXbZDmqvy', '', '', '', 'eeI6B7yXDVqsW5Z_-TrthDhKEetW3sgO', 'LpVnbSfWDsSuX2HWq3ScrI2himsXyiAwmy9jd8rnyp53yhSOg2FXEh7H9HJCc_OW', '', '1', '1486724435', '1487159086');
INSERT INTO `member` VALUES ('3', 'username3', '$2y$13$gJzXi2I/lmWUhdz4xJuRk.YnIbcaa.A9r.yhcyxU1vSwjIofRhRzi', '', '', '', 'Yg2L0Yo2vY9KFlN6fmvpu-8QXPzV8ngc', 'mA1kaJTTWS1waULQIc_UnccaTr-MyeF32F9PiBquokTcn01WQf8AHQZSVttpS9U4', '', '1', '1486995251', '1486995251');
INSERT INTO `member` VALUES ('4', 'username4', '$2y$13$9Mtswsc3TsQEb9X0tsXQ7OwbJjd3oSTvVbKuoMTihy4wBL3bLhmLi', '', '', '', 'p49jRZxJtQdmDxeIzWx4fn05vj1cvL2x', 'Te8SdoYgbPami7mkVwlW9NPuiEShR4p1rO_6hzD3apRWWY6XsPfuW3uvXApBmdPp', '', '1', '1486995263', '1486995263');
INSERT INTO `member` VALUES ('5', 'username5', '$2y$13$6T9YL4oJUGeIJtLDh1y6kuM65Li9WrSUoz0Ct1OKBysCxbTa0YNK6', '', '', '', 'Q8DLElffQoau5LfgRcnKkxJMH-aMZD9i', 'TFiyrStW3QHw0ipmClOKsvTdYgxo8J-Y1ZlnE-RRT9iKUd4ZpfAnAftqRSNkIwy7', '', '1', '1486995271', '1486995271');
INSERT INTO `member` VALUES ('6', 'username7', '$2y$13$CPKn9cgLvz.Z45LMBoOShugAYT.GHRao4afgPIccKMn6kPJN7U4DS', '', '', '', 'Dr_KGv3c9kDHtTwvSJG1potxA8QaFP9K', 'qUbKwb5TE9KWZ0nNwZs5bCue2Ty2gf-IZ7Rs_CTkXa4Pe-ggW9T1IkHUVA3zmvbT', '', '1', '1486995279', '1486995279');
INSERT INTO `member` VALUES ('7', 'qazwsx', '$2y$13$xjQuWy85lrm3.cu/BDDyIuT/v5Ttl2jbtCb4DKZchQd2FV4q/wEQ6', '发的风格的风格', '34534534', '343sdf@sfd.com', 'yaElieAEkMsoztv35ATj1cIFIUmQnYlb', 's1ooLjOn2F1QRspc3ORmSJ1Ng3rqWNhE00Vu7jfWDoAjiAh3e5jv2kIk1FSpWM_A', '', '1', '1487159247', '1487254901');
INSERT INTO `member` VALUES ('8', 'qazwsxfff', '$2y$13$20NAlfkQuezZlE72PR2KVu0U51ysWoGjhmHe7Zq/nJxS9pRNVi77q', '', '', '', '6pbpip1QQlaIl4OZDDUrC4yAfnwOijap', 'xPbEpBvJIN2-U0oRV88sT0OTj1xaWov4XyjkXlN3BHJtDWBdcJPh4t8SKJNaCR1k', '', '1', '1487235537', '1487235537');
INSERT INTO `member` VALUES ('9', 'dsfsd', '$2y$13$Vo7U3w2vm5btOGAT7P/bpu.VKGMxLw2hoI7Fho4MmHkh5UVxqREYy', '', '', '', '1lFMC1-J6_3M_QruSZfsMpVyK3AMzOTa', 'bdo_VKQZdlv6eYoSmwP42ElyBSNSPv-IRl3_Zl0t9-l2OZDnAQJr437M5rOo0tRJ', '', '1', '1487235554', '1487235554');
INSERT INTO `member` VALUES ('10', 'cvxvxcvx', '$2y$13$FrhREbfimF3FIKb.vgh/HuchhbfP393HJ9i2I74AWTsC1uikcHzoO', '发的风格的风格', 'dfdfgdf', '343sdf@sfd.com', 'l-QXXI4nJSb1hdpFl5cRvUzrwYy9Rxer', 'l-L5iREHbQgaqPyry6rPKJIvUO2QMVbrTyjgIElDkzozH3vrq58-IX1ilb7HEm2S', '', '1', '1487235559', '1487235616');

-- ----------------------------
-- Table structure for `migration`
-- ----------------------------
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of migration
-- ----------------------------
INSERT INTO `migration` VALUES ('m000000_000000_base', '1450971663');
INSERT INTO `migration` VALUES ('m130524_201442_init', '1450971667');
INSERT INTO `migration` VALUES ('m140506_102106_rbac_init', '1450971915');

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8 NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `status` tinyint(2) unsigned NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `realname` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '手机号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `mobile` (`mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '1234567', 'nZah--hH16uR1_UHzO8t64d2wdw6EFHG', '$2y$13$CrAFy6MIaTtLeigqZeqW4euSotjKhVOUGXPnkNtcTZvkiKKtBprwC', '', '1234567@qq.com', '1', '1450977875', '1482040485', 'rrrrrr', '1');
INSERT INTO `user` VALUES ('7', '123123123', 'tEfgTFyJEFlzou5KMhrpgioQ4_219PpQ', '$2y$13$rbyIeicVEGz9wybMQodbR.8GSUwPeQUlEzDzgsBQrY24y6cewFaIa', '', '12t467@qq.com', '1', '1454475705', '1481122126', 'aaaaaa', '12345678912');
INSERT INTO `user` VALUES ('8', 'test123', 'AGhXlLyaMgt2l0pOPcxq0Wnppc1ofgDq', '$2y$13$AxbbxaksSQBwugbJ/ByCqeNdCmWApflp3uLSEO4uNYFZ3cByJBpzy', '', '1234test123567@qq.com', '1', '1454572050', '1478416053', 'test123', '12345612345');
INSERT INTO `user` VALUES ('9', 'sfsdfdsf', 'r4TCI6WPo_FMrvI--5--Zqf09AFbqdef', '$2y$13$ZJZotaI42cWCBj5h6WFrzOVNHXmF8AP5rRL0u/fHLCsfEMMhv.ckq', '', '1234qwe567@qq.com', '2', '1454572655', '1478441288', 'sdfsdfsdf', '12342312345');
INSERT INTO `user` VALUES ('10', 'eeeeeeeeeee', 'qmSLm41mSFelMJaEt6W3wLol7gPWIs2V', '$2y$13$koa9wDmX3hqwAB1uth0CEeYt2GZAKyv4aqTd3TWmO.darNa7hq7eu', '', 'eeeeeee@eee.ee', '0', '1455643542', '1479543989', 'eeeeeeee', '12346578945');
INSERT INTO `user` VALUES ('11', 'sdfdfdsfsdf', 'REd0JsHkz4--gnuWpWNS6n68wSXheJ6u', '$2y$13$H0lOuBWtlMrfBFelzKhiweQ0/kBjG3lWYQwWC3pMVSPZr7nbB925m', '', '1234sdfds567@qq.com', '1', '1455675389', '1479543950', 'dfdfgsdfsdf', '11111111222');
INSERT INTO `user` VALUES ('12', 'ffffhhhh', 'xWX5qQQ2aXRUK_aKDfxMySAOqfkxElgI', '$2y$13$KXxX34bc3xqPzTkkgv3jYOJ7AtwzCTwajUSmhLBmUbmt0Sihmrxam', '', '123456f@qq.com', '0', '1476899477', '1479543989', 'fffffhhhh', '11111111223');
INSERT INTO `user` VALUES ('15', 'sdfsdf', 'chYAVR4Uld_XwEnH5W2Ec8IO95Dj6-jx', '$2y$13$TO/yZH5Wa9Nv1uJlMV1eSuS/d6i/nlaCENjUlUVhlMTtflEMdf1wa', '', 'dfgdfg@sdfds.dfgd', '0', '1477591711', '1479543989', 'sdfsdfsdf', 'sdfdgf');
INSERT INTO `user` VALUES ('16', 'hhhhhhhhhh', 'wHintnD5wXxzzcV0oLIuOGgiS2p6OTra', '$2y$13$cggZpgoAPJeZmgaxr1CCGOGfkOJFkqcXbcK5HklTf0w9Obf2WpGNu', '', 'hhhhhh@hhhh.hh', '1', '1477843816', '1479543507', 'hhhhhhhhhhhh', 'hhhhhhhhhh');
INSERT INTO `user` VALUES ('17', 'rtgftghfgh', 'aURzSy-nhYN1Uu8TdAvUS7UOnuGMr3og', '$2y$13$iEfn.gcjKfHp/krnLuReF.UU15ziEAukybtLoI/1huMUAswxtapg6', '', 'fghfghfgh@dsf.fghfg', '1', '1478006908', '1478345142', 'fghfghfg', 'fghfghfgh');
INSERT INTO `user` VALUES ('18', 'ghfghhgfh', 'GEQaIaJgf9rDWdNnM3XbzVBq5ueOY5Yg', '$2y$13$yy2cJxBSnbx3fQgCKERIvOArqRt2vwVx9Acpetq71AqEMGQwj7tfC', '', 'sdfsdf@fddg.ff', '1', '1478181328', '1479553569', 'gggggg', 'ghgfhgfhfh');
INSERT INTO `user` VALUES ('19', '984844048@qq.com', 'DxJTsX3gIPyawKWZYOkJtnXrUqD0GQNC', '$2y$13$7cDEIU9gtPwqHKveTnRJ6uCgNKDkcwgpsrHNnWc9czW.wKzKfGVtm', '4FmSVrFAJ_RZdni_cc_7qk0M01bF4Drv_1486968421', '984844048@qq.com', '1', '1486968410', '1486968421', '', '');
