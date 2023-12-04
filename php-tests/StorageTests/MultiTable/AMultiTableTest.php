<?php

namespace StorageTests\MultiTable;


use kalanis\kw_mapper\Storage;
use kalanis\kw_pedigree\Storage\MultiTable;
use StorageTests\AMySqlTest;


/**
 * Class AMultiTableTest
 * @package StorageTests\MultiTable
 * @requires extension PDO
 * @requires extension pdo_mysql
 */
abstract class AMultiTableTest extends AMySqlTest
{
    protected function dataRefill(): void
    {
        $this->assertTrue($this->database->exec($this->dropTable1(), []));
        $this->assertTrue($this->database->exec($this->dropTable2(), []));
        $this->assertTrue($this->database->exec($this->basicTable1(), []));
        $this->assertTrue($this->database->exec($this->basicTable2(), []));
        $this->assertTrue($this->database->exec($this->fillTable1(), []));
        $this->assertEquals(14, $this->database->rowCount());
        $this->assertTrue($this->database->exec($this->fillTable2(), []));
        $this->assertEquals(17, $this->database->rowCount());
    }

    protected function dropTable1(): string
    {
        return 'DROP TABLE IF EXISTS `kw_pedigree_upd`';
    }

    protected function dropTable2(): string
    {
        return 'DROP TABLE IF EXISTS `kw_pedigree_relate`';
    }

    protected function basicTable1(): string
    {
        return "CREATE TABLE `kw_pedigree_upd` (
    `kwp_id` int(32) NOT NULL AUTO_INCREMENT,
    `kwp_short` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_family` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_birth` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_death` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_successes` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_sex` set('female','male') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'male',
    `kwp_text` longtext COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    PRIMARY KEY (`kwp_id`),
    -- UNIQUE KEY `identifier` (`kwp_short`),
    INDEX `birth` (`kwp_birth`),
    INDEX `death` (`kwp_death`),
    INDEX `sex` (`kwp_sex`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Pedigree table';";
    }

    protected function basicTable2(): string
    {
        return "CREATE TABLE `kw_pedigree_relate` (
    `kwpr_id` int(64) NOT NULL AUTO_INCREMENT,
    `kwp_id_child` int(64) DEFAULT NULL,
    `kwp_id_parent` int(64) DEFAULT NULL,
    PRIMARY KEY (`kwpr_id`),
    KEY `kp_id_child` (`kwp_id_child`),
    KEY `kp_id_parent` (`kwp_id_parent`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Pedigree relations';";
    }

    /**
     * Source is scottish royal tree
     * @link https://www.britroyals.com/images/canmore.jpg
     * @return string
     */
    protected function fillTable1(): string
    {
        return 'INSERT INTO `kw_pedigree_upd` (`kwp_id`, `kwp_name`, `kwp_family`, `kwp_short`, `kwp_birth`, `kwp_death`, `kwp_sex`) VALUES
( 1, "Duncan I.", "MacAlpin", "duncan_i", "1001", "1040", "male"),
( 2, "Sybilla", "of Northumbria", "sybilla", "", "", "female"),
( 3, "Malcolm III.", "Dunkeld", "malcolm_iii", "1031", "1093", "male"),
( 4, "Margaret", "dau of Edward Aetherling", "margaret", "", "1069", "female"),
( 5, "Donald III.", "Dunkeld", "donald_iii", "1033", "1099", "male"),
( 6, "Duncan II.", "Dunkeld", "duncan_ii", "1060", "1094", "male"),
( 7, "Edmund", "Dunkeld", "edmund", "1060", "1097", "male"),
( 8, "Edgar", "Dunkeld", "edgar", "1072", "1107", "male"),
( 9, "Alexander I.", "Dunkeld", "alexander_i", "1077", "1124", "male"),
(10, "David I.", "Dunkeld", "david_i", "1080", "1153", "male"),
(11, "Matilda", "of Huntingdon", "matilda", "1074", "1130", "female"),
(12, "Henry", "of Huntingdon", "henry", "1114", "1152", "male"),
(13, "Ada", "de Warenne", "ada", "", "1178", "female"),
(14, "Malcolm IV.", "Dunkeld", "malcolm_iv", "1141", "1165", "male")
';
    }

    /**
     * Source is scottish royal tree
     * @link https://www.britroyals.com/images/canmore.jpg
     * @return string
     */
    protected function fillTable2(): string
    {
        return 'INSERT INTO `kw_pedigree_relate` (`kwp_id_child`, `kwp_id_parent`) VALUES
( 3, 1),
( 3, 2),
( 5, 1),
( 5, 2),
( 6, 3),
( 7, 3),
( 7, 4),
( 8, 3),
( 8, 4),
( 9, 3),
( 9, 4),
(10, 3),
(10, 4),
(12, 10),
(12, 11),
(14, 12),
(14, 13)
';
    }
}


class PedigreeRelateTestMapper extends MultiTable\PedigreeRelateMapper
{
    protected function setMap(): void
    {
        parent::setMap();
        $this->addForeignKey('parents', PedigreeItemTestRecord::class, 'parentId', 'id');
        $this->addForeignKey('children', PedigreeItemTestRecord::class, 'childId', 'id');
        $this->setSource('test_mysql_local');
    }
}


class PedigreeRelateTestRecord extends MultiTable\PedigreeRelateRecord
{
    protected function getMapperClass(): string
    {
        return PedigreeRelateTestMapper::class;
    }
}


class PedigreeItemTestMapper extends MultiTable\PedigreeItemMapper
{
    protected function setMap(): void
    {
        parent::setMap();
        $this->setSource('test_mysql_local');
    }

    protected function getRelateRecordClass(): string
    {
        return PedigreeRelateTestRecord::class;
    }
}


class PedigreeItemTestRecord extends MultiTable\PedigreeItemRecord
{
    protected function getMapperClass(): string
    {
        return PedigreeItemTestMapper::class;
    }
}
