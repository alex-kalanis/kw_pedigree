<?php

namespace StorageTests\File;


use CommonTestClass;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Mappers\Storage\ATable;
use kalanis\kw_mapper\Storage;
use kalanis\kw_pedigree\Storage\File;
use kalanis\kw_storage\Storage\Key\StaticPrefixKey;


abstract class AFileTest extends CommonTestClass
{
    protected function setUp(): void
    {
        StaticPrefixKey::setPrefix(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data') . DIRECTORY_SEPARATOR);
        $pt = (new StaticPrefixKey())->fromSharedKey($this->mockFile());
        if (is_file($pt)) {
            chmod($pt, 0555);
            unlink($pt);
        }

        parent::setUp();
    }

    public function tearDown(): void
    {
        $pt = (new StaticPrefixKey())->fromSharedKey($this->mockFile());
        if (is_file($pt)) {
            chmod($pt, 0555);
            unlink($pt);
        }

        parent::tearDown();
        StaticPrefixKey::setPrefix('');
    }

    protected function mockFile(): string
    {
        return 'pedigree.temp';
    }
}


class FileTestMapper extends File\PedigreeMapper
{
    protected $normalStore = 'pedigree.testing';
    protected $tempStore = 'pedigree.temp';
    protected $readFile = 'pedigree.testing';
    protected $saveFile = 'pedigree.temp';

    protected function setMap(): void
    {
        parent::setMap();
        $this->setSource('pedigree.testing');
        $this->setStorage();
    }

    public function loadFromTemp(bool $useTemp): void
    {
        $this->readFile = $useTemp ? $this->tempStore : $this->normalStore;
    }

    public function storeToTemp(bool $useTemp): void
    {
        $this->saveFile = $useTemp ? $this->tempStore : $this->normalStore;
    }

    protected function getReadSource(): string
    {
        return $this->readFile;
    }

    protected function getWriteSource(): string
    {
        return $this->saveFile;
    }
}


class DummyFileTestMapper extends ATable
{
    protected function setMap(): void
    {
        $this->setSource('pedigree.txt');
        $this->setStorage();
        $this->setFormat(Storage\Shared\FormatFiles\SeparatedElements::class);
        $this->setRelation('id', 0);
        $this->setRelation('short', 1);
        $this->setRelation('name', 2);
        $this->setRelation('family', 3);
        $this->setRelation('birth', 4);
        $this->setRelation('death', 5);
        $this->setRelation('fatherId', 6);
        $this->setRelation('motherId', 7);
        $this->setRelation('successes', 8);
        $this->setRelation('sex', 9);
        $this->setRelation('text', 10);
        $this->addPrimaryKey('id');
    }
}


class FileTestRecord extends File\PedigreeRecord
{
    protected function addEntries(): void
    {
        parent::addEntries();
        $this->setMapper(FileTestMapper::class);
    }

    /**
     * @throws MapperException
     */
    public function useDummy(): void
    {
        $this->setMapper(DummyFileTestMapper::class);
    }
}
