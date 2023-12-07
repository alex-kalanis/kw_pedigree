-- variant of sql for separated pedigree tables

SET NAMES utf8;
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `kw_pedigree_relate`;
DROP TABLE IF EXISTS `kw_pedigree_upd`;

CREATE TABLE `kw_pedigree_upd` (
    `kwp_id` int(32) NOT NULL AUTO_INCREMENT,
    `kwp_short` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_family` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_birth` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_death` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_successes` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_sex` set('female','male') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'male',
    `kwp_text` longtext COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (`kwp_id`),
    UNIQUE KEY `identifier` (`kwp_short`),
    INDEX `birth` (`kwp_birth`),
    INDEX `death` (`kwp_death`),
    INDEX `sex` (`kwp_sex`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Pedigree table';

CREATE TABLE `kw_pedigree_relate` (
    `kwpr_id` int(64) NOT NULL AUTO_INCREMENT,
    `kwp_id_child` int(64) DEFAULT NULL,
    `kwp_id_parent` int(64) DEFAULT NULL,
    PRIMARY KEY (`kwpr_id`),
    KEY `kp_id_child` (`kwp_id_child`),
    KEY `kp_id_parent` (`kwp_id_parent`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Pedigree relations';
