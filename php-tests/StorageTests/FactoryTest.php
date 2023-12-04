<?php

namespace StorageTests;


use CommonTestClass;
use kalanis\kw_mapper\Interfaces\IDriverSources;
use kalanis\kw_mapper\Interfaces\IEntryType;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Storage\Database;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage;
use kalanis\kw_storage\Storage\Key\StaticPrefixKey;


/**
 * Class FactoryTest
 * @package StorageTests
 * @requires extension PDO
 * @requires extension pdo_mysql
 */
class FactoryTest extends CommonTestClass
{
    /** @var bool */
    protected $skipIt = false;

    protected function setUp(): void
    {
        $skipIt = getenv('MYSKIP');
        $this->skipIt = false !== $skipIt && boolval(intval(strval($skipIt)));

        StaticPrefixKey::setPrefix(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR  . 'data') . DIRECTORY_SEPARATOR);
        $host = getenv('MYSERVER');
        $port = getenv('MYPORT');
        $user = getenv('MYUSER');
        $pass = getenv('MYPASS');
        $db = getenv('MYDB');
        Database\ConfigStorage::getInstance()->addConfig(
            Database\Config::init()->setTarget(
                IDriverSources::TYPE_PDO_MYSQL,
                'pedigree',
                (false !== $host) ? strval($host) : 'localhost',
                (false !== $port) ? intval($port) : 3306,
                (false !== $user) ? strval($user) : 'kwdeploy',
                (false !== $pass) ? strval($pass) : 'testingpass',
                (false !== $db) ? strval($db) : 'kw_deploy'
            )
        );
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        StaticPrefixKey::setPrefix('');
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testBadRecord(): void
    {
        if ($this->skipIt) {
            $this->markTestSkipped('Skipped by config');
            return;
        }

        $lib = new Storage\FactoryAdapter();
        $this->expectException(PedigreeException::class);
        $this->expectExceptionMessage('Unknown record for getting mapper');
        $lib->getAdapter(new XFailRecord());
    }

    /**
     * @param string $what
     * @param string $from
     * @throws PedigreeException
     * @dataProvider correctProvider
     */
    public function testCorrectRecord(string $what, string $from): void
    {
        if ($this->skipIt) {
            $this->markTestSkipped('Skipped by config');
            return;
        }

        $lib = new Storage\FactoryAdapter();
        $instance = $lib->getAdapter(new $from());
        $this->assertInstanceOf($what, $instance);
    }

    public function correctProvider(): array
    {
        return [
            [Storage\File\EntryAdapter::class, Storage\File\PedigreeRecord::class],
            [Storage\SingleTable\EntryAdapter::class, Storage\SingleTable\PedigreeRecord::class],
            [Storage\MultiTable\EntryAdapter::class, Storage\MultiTable\PedigreeItemRecord::class],
        ];
    }
}


class XFailRecord extends Storage\APedigreeRecord
{
    protected function addEntries(): void
    {
        $this->addEntry('id', IEntryType::TYPE_INTEGER, 65536);
        $this->setMapper(Storage\SingleTable\PedigreeMapper::class);
    }
}
