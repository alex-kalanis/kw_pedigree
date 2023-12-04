<?php

namespace StorageTests\File;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_mapper\Storage;
use kalanis\kw_pedigree\PedigreeException;


class RecordTest extends AFileTest
{
    /**
     * @throws MapperException
     */
    public function testSimpleSearch(): void
    {
        $lib = new Search(new FileTestRecord());
        $lib->notExact('birth', '');
        $this->assertEquals(11, $lib->getCount());
        $lib->offset(3);
        $lib->limit(7);
        $this->assertEquals(7, count($lib->getResults()));
        $lib->offset(6);
        $lib->limit(7);
        $this->assertEquals(5, count($lib->getResults()));
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testLike(): void
    {
        $lib = new FileTestRecord();
        $records = $lib->getLike('of Hunt');
        $this->assertEquals(2, count($records));

        $records = $lib->getLike('Dunkeld', 'female');
        $this->assertEquals(0, count($records));

        $records = $lib->getLike('Dunkeld', 'male');
        $this->assertEquals(8, count($records));
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testLikeFail(): void
    {
        $lib = new FileTestRecord();
        $lib->useDummy();
        $this->expectException(PedigreeException::class);
        $lib->getLike('of Hunt');
    }

    /**
     * @throws MapperException
     */
    public function testCrud(): void
    {
        $lib = new FileTestRecord();
        $lib->getMapper()->storeToTemp(false);
        $lib->name = 'William I.';
        $lib->family = 'Dunkeld';
        $lib->short = 'william_i';
        $lib->death = '1214';
        $this->assertTrue($lib->save());
        $this->assertTrue($lib->load());
        $this->assertEquals('', $lib->birth);
        $this->assertEquals('1214', $lib->death);

        $lib->birth = '1142';
        $this->assertTrue($lib->save());
        $this->assertTrue($lib->load());
        $this->assertEquals('1142', $lib->birth);
        $this->assertEquals('1214', $lib->death);

        $lib->short = '';
        $this->assertTrue($lib->save());
        $lib->id = '';
        $this->assertFalse($lib->save());

        $lib2 = new FileTestRecord();
        $lib2->name = 'William I.';
        $lib2->family = 'Dunkeld';
        $this->assertTrue($lib2->delete());
    }
}
