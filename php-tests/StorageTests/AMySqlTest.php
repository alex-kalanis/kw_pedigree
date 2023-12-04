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
        $skipIt = getenv('MYSKIP');
        $this->skipIt = false !== $skipIt && boolval(intval(strval($skipIt)));

        $location = getenv('MYSERVER');
        $location = false !== $location ? strval($location) : '127.0.0.1' ;

        $port = getenv('MYPORT');
        $port = false !== $port ? intval($port) : 3306 ;

        $user = getenv('MYUSER');
        $user = false !== $user ? strval($user) : 'testing' ;

        $pass = getenv('MYPASS');
        $pass = false !== $pass ? strval($pass) : 'testing' ;

        $db = getenv('MYDB');
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
