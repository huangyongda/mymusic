/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50740 (5.7.40)
 Source Host           : localhost:3306
 Source Schema         : hydmusic

 Target Server Type    : MySQL
 Target Server Version : 50740 (5.7.40)
 File Encoding         : 65001

 Date: 24/10/2023 17:31:51
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post` text COLLATE utf8_bin,
  `time` datetime DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1636 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for music
-- ----------------------------
DROP TABLE IF EXISTS `music`;
CREATE TABLE `music` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '歌名',
  `source` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '来源',
  `file` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '路径',
  `singer` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '歌手',
  `album` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '专辑',
  `duration` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '时长',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `hash` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '文件哈希',
  `size` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '大小',
  `status` int(11) DEFAULT '0' COMMENT '状态 0等待下载  1下载完成  2 下载中  -1 下载失败',
  `content` text COLLATE utf8_bin COMMENT '其他内容',
  `quality` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '质量',
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`singer`,`album`,`duration`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='歌曲库';

-- ----------------------------
-- Table structure for task
-- ----------------------------
DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `str` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status` int(11) DEFAULT '0' COMMENT '任务状态  0 未开始   1已完成  -1  失败',
  `content` text COLLATE utf8_bin COMMENT '详情',
  `finish_num` int(11) DEFAULT NULL COMMENT '已下载数',
  `total_num` int(11) DEFAULT NULL COMMENT '歌单歌曲总数',
  `tag` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '标签',
  `type` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '歌单类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='歌单列表';

-- ----------------------------
-- Table structure for task_detail
-- ----------------------------
DROP TABLE IF EXISTS `task_detail`;
CREATE TABLE `task_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `music_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '歌名',
  `singer` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '歌手',
  `album` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '专辑',
  `duration` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '时长',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `content` text COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=297 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

SET FOREIGN_KEY_CHECKS = 1;
