<?php

namespace StorageTests\File;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_pedigree\GetEntries;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage;


/**
 * Class EntriesTest
 * @package StorageTests\File
 * @requires extension PDO
 * @requires extension pdo_mysql
 */
class EntriesTest extends AFileTest
{
    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testInitial(): void
    {
        $lib = new GetEntries(new FileTestRecord());
        $this->assertInstanceOf(FileTestRecord::class, $lib->getRecord());
        $this->assertInstanceOf(Storage\File\EntryAdapter::class, $lib->getStorage());
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testGetById(): void
    {
        $lib = new GetEntries(new FileTestRecord());
        $data = $lib->getById(4);
        $this->assertEquals(4, $data->getId());
        $this->assertEquals('Margaret', $data->getName());
        $this->assertEquals('dau of Edward Aetherling', $data->getFamily());
        $this->assertEquals('margaret', $data->getShort());
        $this->assertEquals('', $data->getBirth());
        $this->assertEquals('1069', $data->getDeath());
        $this->assertEquals('', $data->getSuccesses());
        $this->assertEquals('female', $data->getSex());
        $this->assertEquals('', $data->getText());
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testNoId(): void
    {
        $lib = new GetEntries(new FileTestRecord());
        $this->assertNull($lib->getById(444));
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testGetByKey(): void
    {
        $lib = new GetEntries(new FileTestRecord());
        $data = $lib->getByKey('alexander_i');
        $this->assertEquals(9, $data->getId());
        $this->assertEquals('Alexander I.', $data->getName());
        $this->assertEquals('Dunkeld', $data->getFamily());
        $this->assertEquals('alexander_i', $data->getShort());
        $this->assertEquals('1077', $data->getBirth());
        $this->assertEquals('1124', $data->getDeath());
        $this->assertEquals('', $data->getSuccesses());
        $this->assertEquals('male', $data->getSex());
        $this->assertEquals('', $data->getText());
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testNoKey(): void
    {
        $lib = new GetEntries(new FileTestRecord());
        $this->assertNull($lib->getByKey('boo-bar-baz'));
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testGetBySex(): void
    {
        $lib = new GetEntries(new FileTestRecord());
        $data = $lib->getBySex('female');

        $entry = reset($data);
        $this->assertEquals(2, $entry->getId());
        $this->assertEquals('sybilla', $entry->getShort());
        $this->assertEquals('female', $entry->getSex());

        $entry = next($data);
        $this->assertEquals(4, $entry->getId());
        $this->assertEquals('margaret', $entry->getShort());
        $this->assertEquals('female', $entry->getSex());

        $entry = next($data);
        $this->assertEquals(11, $entry->getId());
        $this->assertEquals('matilda', $entry->getShort());
        $this->assertEquals('female', $entry->getSex());

        $entry = next($data);
        $this->assertEquals(13, $entry->getId());
        $this->assertEquals('ada', $entry->getShort());
        $this->assertEquals('female', $entry->getSex());

        $this->assertFalse(next($data));
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testGetBySexAndMore(): void
    {
        $lib = new GetEntries(new FileTestRecord());
        $this->assertEmpty($lib->getBySex('female', 'hills', 'don'));
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testEmptyEntry(): void
    {
        $lib = new GetEntries(new FileTestRecord());
        $data = $lib->getBySex('other', 'hills', 'don', 'this is empty one');

        $entry = reset($data);
        $this->assertEquals(0, $entry->getId());
        $this->assertEquals('this is empty one', $entry->getName());
        $this->assertEquals('', $entry->getFamily());
        $this->assertEquals('', $entry->getShort());
        $this->assertEquals('', $entry->getBirth());
        $this->assertEquals('', $entry->getDeath());
        $this->assertEquals('', $entry->getSuccesses());
        $this->assertEquals('none', $entry->getSex());
        $this->assertEquals('', $entry->getText());

        $this->assertFalse(next($data));
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
