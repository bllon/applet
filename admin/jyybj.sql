/*
 Navicat MySQL Data Transfer

 Source Server         : 本地mysql
 Source Server Type    : MySQL
 Source Server Version : 80016
 Source Host           : localhost:3306
 Source Schema         : jyybj

 Target Server Type    : MySQL
 Target Server Version : 80016
 File Encoding         : 65001

 Date: 19/09/2021 21:04:35
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for jyybj_class
-- ----------------------------
DROP TABLE IF EXISTS `jyybj_class`;
CREATE TABLE `jyybj_class`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `session_id` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户唯一id',
  `class_name` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分类名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jyybj_class
-- ----------------------------
INSERT INTO `jyybj_class` VALUES (1, 'oYFKb5SPlkdpySEicMBEDzkKzyMg', '默认');

-- ----------------------------
-- Table structure for jyybj_label
-- ----------------------------
DROP TABLE IF EXISTS `jyybj_label`;
CREATE TABLE `jyybj_label`  (
  `id` int(12) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `session_id` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户唯一标识',
  `name` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标签名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for jyybj_note
-- ----------------------------
DROP TABLE IF EXISTS `jyybj_note`;
CREATE TABLE `jyybj_note`  (
  `id` int(12) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `session_id` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户唯一标识',
  `title` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '笔记标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '笔记内容',
  `type` tinyint(1) NOT NULL COMMENT '笔记类型',
  `class_name` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '笔记分类',
  `sort` int(4) NOT NULL COMMENT '笔记排序',
  `label` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '笔记标签',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jyybj_note
-- ----------------------------
INSERT INTO `jyybj_note` VALUES (1, 'oYFKb5SPlkdpySEicMBEDzkKzyMg', '简易云笔记介绍', '&lt;p class=\'ql-indent-4\'&gt;&lt;strong&gt;建议云笔记介绍&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;hr&gt;&lt;p&gt;&lt;strong&gt;1.方便使用&lt;/strong&gt;&lt;/p&gt;&lt;p class=\'ql-indent-1\'&gt;简易云笔记适用于学生，工作人士等，支持多种存&lt;/p&gt;&lt;p&gt;储格式及样式&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;hr&gt;&lt;p&gt;&lt;strong&gt;2.简洁清晰&lt;/strong&gt;&lt;/p&gt;&lt;p class=\'ql-indent-1\'&gt;建议云笔记界面清晰，无广告，操作人性化&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;hr&gt;&lt;p&gt;&lt;strong&gt;3.分类及标签&lt;/strong&gt;&lt;/p&gt;&lt;p class=\'ql-indent-1\'&gt;简易云笔记支持创建分类和标签，可以帮助用户准&lt;/p&gt;&lt;p&gt;确定位笔记位置，方便笔记搜索&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;hr&gt;&lt;p&gt;&lt;strong&gt;4.支持图片上传&lt;/strong&gt;&lt;/p&gt;&lt;p class=\'ql-indent-1\'&gt;简易云笔记支持富文本编辑，包括图片上传，例如&lt;/p&gt;&lt;p&gt;&lt;img src=\'http://localhost/ybj/img/moren.jpg\' data-custom=\'id=abcd&amp;amp;role=god\'&gt;&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;p class=\'ql-indent-1\'&gt;&lt;br&gt;&lt;/p&gt;', 0, '默认', 1, '', 1591670974, 1625893612);
INSERT INTO `jyybj_note` VALUES (2, 'oYFKb5SPlkdpySEicMBEDzkKzyMg', '213', '&lt;p&gt;231213&lt;/p&gt;', 0, '默认', 2, '', 1625893631, 1631979510);

-- ----------------------------
-- Table structure for jyybj_user
-- ----------------------------
DROP TABLE IF EXISTS `jyybj_user`;
CREATE TABLE `jyybj_user`  (
  `id` int(12) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `session_id` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户唯一标识',
  `nickName` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '微信用户昵称',
  `gender` tinyint(1) NOT NULL COMMENT '用户性别',
  `city` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'city',
  `country` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'country',
  `language` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'language',
  `province` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'province',
  `avatarUrl` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户头像',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jyybj_user
-- ----------------------------
INSERT INTO `jyybj_user` VALUES (1, 'oYFKb5SPlkdpySEicMBEDzkKzyMg', 'IM', 1, '', 'Columbia', 'zh_CN', '', 'https://wx.qlogo.cn/mmopen/vi_32/UuOV8OrMeRGuibY76OXzuB9MPSsFicibhJT6FoXP7gm6fn9t8tibibxNk5CWbW8SCAycbhvaCyk26Mw8QZ6Yql4atcQ/132');

SET FOREIGN_KEY_CHECKS = 1;
