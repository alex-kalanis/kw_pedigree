<?php

namespace StorageTests\SingleTable;


use kalanis\kw_pedigree\Storage\SingleTable;
use StorageTests\AMySqlTest;


/**
 * Class ASingleTableTest
 * @package StorageTests\SingleTable
 * @requires extension PDO
 * @requires extension pdo_mysql
 */
abstract class ASingleTableTest extends AMySqlTest
{
    protected function dataRefill(): void
    {
        $this->assertTrue($this->database->exec($this->dropTable(), []));
        $this->assertTrue($this->database->exec($this->basicTable(), []));
        $this->assertTrue($this->database->exec($this->fillTable(), []));
        $this->assertEquals(14, $this->database->rowCount());
    }

    protected function dropTable(): string
    {
        return 'DROP TABLE IF EXISTS `kw_pedigree`';
    }

    /**
     * De facto simple tree version
     * @return string
     */
    protected function basicTable(): string
    {
        return "CREATE TABLE `kw_pedigree` (
    `pedigree_id` INTEGER AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `pedigree_name` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
    `pedigree_family` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `pedigree_short` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `pedigree_birth` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `pedigree_death` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `pedigree_father_id` INTEGER NULL,
    `pedigree_mother_id` INTEGER NULL,
    `pedigree_successes` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `pedigree_sex` set('female','male') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'male',
    `pedigree_text` longtext COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    CONSTRAINT fk_father FOREIGN KEY (`pedigree_father_id`)
        REFERENCES `kw_pedigree`(`pedigree_id`),
    CONSTRAINT fk_mother FOREIGN KEY (`pedigree_mother_id`)
        REFERENCES `kw_pedigree`(`pedigree_id`),
    INDEX `name` (`pedigree_name`),
    INDEX `family` (`pedigree_family`),
    INDEX `birth` (`pedigree_birth`),
    INDEX `death` (`pedigree_death`),
    INDEX `sex` (`pedigree_sex`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Pedigree table';";
    }

    /**
     * Source is scottish royal tree
     * @link https://www.britroyals.com/images/canmore.jpg
     * @return string
     */
    protected function fillTable(): string
    {
        return 'INSERT INTO `kw_pedigree` (`pedigree_id`, `pedigree_name`, `pedigree_family`, `pedigree_short`, `pedigree_birth`, `pedigree_death`, `pedigree_father_id`, `pedigree_mother_id`, `pedigree_sex`) VALUES
( 1, "Duncan I.", "MacAlpin", "duncan_i", "1001", "1040", NULL, NULL, "male"),
( 2, "Sybilla", "of Northumbria", "sybilla", "", "", NULL, NULL, "female"),
( 3, "Malcolm III.", "Dunkeld", "malcolm_iii", "1031", "1093", 1, 2, "male"),
( 4, "Margaret", "dau of Edward Aetherling", "margaret", "", "1069", NULL, NULL, "female"),
( 5, "Donald III.", "Dunkeld", "donald_iii", "1033", "1099", 1, 2, "male"),
( 6, "Duncan II.", "Dunkeld", "duncan_ii", "1060", "1094", 3, NULL, "male"),
( 7, "Edmund", "Dunkeld", "edmund", "1060", "1097", 3, 4, "male"),
( 8, "Edgar", "Dunkeld", "edgar", "1072", "1107", 3, 4, "male"),
( 9, "Alexander I.", "Dunkeld", "alexander_i", "1077", "1124", 3, 4, "male"),
(10, "David I.", "Dunkeld", "david_i", "1080", "1153", 3, 4, "male"),
(11, "Matilda", "of Huntingdon", "matilda", "1074", "1130", NULL, NULL, "female"),
(12, "Henry", "of Huntingdon", "henry", "1114", "1152", 10, 11, "male"),
(13, "Ada", "de Warenne", "ada", "", "1178", NULL, NULL, "female"),
(14, "Malcolm IV.", "Dunkeld", "malcolm_iv", "1141", "1165", 12, 13, "male")
';
    }
}


class PedigreeTestMapper extends SingleTable\PedigreeMapper
{
    protected function setMap(): void
    {
        parent::setMap();
        $this->addForeignKey('father', PedigreeTestRecord::class, 'fatherId', 'id');
        $this->addForeignKey('mother', PedigreeTestRecord::class, 'motherId', 'id');
        $this->setSource('test_mysql_local');
    }
}


class PedigreeTestRecord extends SingleTable\PedigreeRecord
{
    protected function getMapperClass(): string
    {
        return PedigreeTestMapper::class;
    }
}
