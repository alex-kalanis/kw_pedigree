<?php

namespace StorageTests;


use CommonTestClass;
use kalanis\kw_mapper\Interfaces\IDriverSources;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Storage\Database\Config;
use kalanis\kw_mapper\Storage\Database\ConfigStorage;
use kalanis\kw_mapper\Storage\Database\DatabaseSingleton;
use kalanis\kw_mapper\Storage\Database\PDO\MySQL;
use PDO;


/**
 * Class AMySqlTest
 * @package StorageTests
 * @requires extension PDO
 * @requires extension pdo_mysql
 */
abstract class AMySqlTest extends CommonTestClass
{
    /** @var null|MySQL */
    protected $database = null;
    /** @var bool */
    protected $skipIt = false;

    /**
     * @throws MapperException
     */
    protected function setUp(): void
    {
        $skipIt = getenv('SKIP_DB_TESTS');
        $this->skipIt = false !== $skipIt && boolval(intval(strval($skipIt)));

        $location = getenv('KW_PEDIGREE_DB_HOST');
        $location = false !== $location ? strval($location) : '127.0.0.1' ;

        $port = getenv('KW_PEDIGREE_DB_PORT');
        $port = false !== $port ? intval($port) : 3306 ;

        $user = getenv('KW_PEDIGREE_DB_USER');
        $user = false !== $user ? strval($user) : 'testing' ;

        $pass = getenv('KW_PEDIGREE_DB_PASS');
        $pass = false !== $pass ? strval($pass) : 'testing' ;

        $db = getenv('KW_PEDIGREE_DB_NAME');
        $db = false !== $db ? strval($db) : 'testing' ;

        $conf = Config::init()->setTarget(
                    IDriverSources::TYPE_PDO_MYSQL,
                    'test_mysql_local',
                    $location,
                    $port,
                    $user,
                    $pass,
                    $db
                );
        $conf->setParams(2400, true);
        ConfigStorage::getInstance()->addConfig($conf);
        $this->database = DatabaseSingleton::getInstance()->getDatabase($conf);
        $this->database->addAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
}
