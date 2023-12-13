<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Storage\Database;
use kalanis\kw_pedigree\Config;


/**
 * Class ConfigTest
 * @package BasicTests
 */
class ConfigTest extends CommonTestClass
{
    /**
     * @throws MapperException
     */
    public function testSameConf(): void
    {
        Config::init('testOne');
        $conf1 = Database\ConfigStorage::getInstance()->getConfig('testOne');

        $this->assertEquals('mysql', $conf1->getDriver());
        $this->assertEquals('testOne', $conf1->getSourceName());

        $conf2 = Database\ConfigStorage::getInstance()->getConfig('testOne');

        $this->assertEquals($conf1, $conf2);
    }

    /**
     * @throws MapperException
     */
    public function testDifferentConf(): void
    {
        $conf1 = Database\ConfigStorage::getInstance()->getConfig('testOne');

        $user = getenv('KW_PEDIGREE_DB_USER');
        $pass = getenv('KW_PEDIGREE_DB_PASS');
        $db = getenv('KW_PEDIGREE_DB_NAME');

        putenv('KW_PEDIGREE_DB_USER=tester1');
        putenv('KW_PEDIGREE_DB_PASS=tester2');
        putenv('KW_PEDIGREE_DB_NAME=tester3');

        Config::init('testTwo');
        $conf2 = Database\ConfigStorage::getInstance()->getConfig('testTwo');

        $this->assertEquals('mysql', $conf2->getDriver());
        $this->assertEquals('testTwo', $conf2->getSourceName());
        $this->assertEquals('tester1', $conf2->getUser());
        $this->assertEquals('tester2', $conf2->getPassword());
        $this->assertEquals('tester3', $conf2->getDatabase());

        $this->assertNotEquals($conf1, $conf2);

        putenv('KW_PEDIGREE_DB_USER=' . $user);
        putenv('KW_PEDIGREE_DB_PASS=' . $pass);
        putenv('KW_PEDIGREE_DB_NAME=' . $db);
    }
}
