/*
 Navicat MySQL Data Transfer

 Source Server         : 本地mysql
 Source Server Type    : MySQL
 Source Server Version : 80016
 Source Host           : localhost:3306
 Source Schema         : weixin

 Target Server Type    : MySQL
 Target Server Version : 80016
 File Encoding         : 65001

 Date: 19/09/2021 21:05:14
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin`  (
  `username` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理员名称',
  `password` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理员密码'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('admin', 'admin');

-- ----------------------------
-- Table structure for topic
-- ----------------------------
DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `options` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `answer` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of topic
-- ----------------------------
INSERT INTO `topic` VALUES (1, '我国于(  )加入《濒危野生动植物种国际贸易公约》。', '[\"1980\\u5e7412\\u670825\\u65e5\",\"1980\\u5e749\\u670826\\u65e5\",\"1980\\u5e745\\u670822\\u65e5\"]', 1);
INSERT INTO `topic` VALUES (2, '在我国已灭绝的10种野生动物中，新疆占了3种，它们是野马、高鼻羚羊和( )', '[\"\\u65b0\\u7586\\u864e\",\"\\u65b0\\u7586\\u5317\\u9cb5\",\"\\u6591\\u6797\\u72f8\"]', 1);
INSERT INTO `topic` VALUES (3, '在新疆地区分布的国家一级保护野生动物有26种，以下属于一级保护野生动物的是：( )', '[\"\\u91ce\\u9a6c\\u3001\\u96ea\\u8c79\\u3001\\u5140\\u9e6b\",\"\\u91ce\\u9a6c\\u3001\\u91ce\\u9a74\\u3001\\u68d5\\u718a\",\"\\u91ce\\u9a6c\\u3001\\u7266\\u725b\\u3001\\u6c34\\u736d\"]', 1);
INSERT INTO `topic` VALUES (4, '我国第一座森林公园是张家界国家森林公园。新疆国家级森林公园有2个，位于(  )和天池森林公园。', '[\"\\u4e4c\\u9c81\\u6728\\u9f50\\u5357\\u90ca\\u7684\\u7167\\u58c1\\u5c71\\u68ee\\u6797\\u516c\\u56ed\",\"\\u6c34\\u78e8\\u6c9f\\u68ee\\u6797\\u516c\\u56ed\",\"\\u5357\\u5c71\\u68ee\\u6797\\u516c\\u56ed\"]', 1);
INSERT INTO `topic` VALUES (5, '世界上最小的花是(  )的花，连肉眼都看不清楚。', '[\"\\u6a31\\u6843\",\"\\u65e0\\u82b1\\u679c\",\"\\u6d77\\u68e0\"]', 2);
INSERT INTO `topic` VALUES (6, '世界最远的地方', '[\"1\",\"2\",\"3\",\"4\"]', 3);

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` char(35) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `nickName` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `avatarUrl` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `city` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `score` int(12) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('5ead092b8eccd', 'IM', 'http://wx.qlogo.cn/mmopen/vi_32/t80hOPsqvAzEYvLcw5sQpYTicUQTIWwpVqcfDq7PmOZ3JxxI920vzJTd2BEdc0u1Xlss8xQ1RTGc5qLRibj6zHhw/132', 1, 'null', 120);

SET FOREIGN_KEY_CHECKS = 1;
