<?php

namespace StorageTests\SingleTable;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Storage;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage\SingleTable\EntryAdapter;


class EntryAdapterTest extends ASingleTableTest
{
    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testGetters(): void
    {
        if ($this->skipIt) {
            $this->markTestSkipped('Skipped by config');
            return;
        }

        $this->dataRefill();

        $lib = new EntryAdapter();
        $lib->setRecord(new PedigreeTestRecord());
        $lib->setId(4);
        $lib->getRecord()->load();

        $this->assertEquals(4, $lib->getId());
        $this->assertEquals('Margaret', $lib->getName());
        $this->assertEquals('dau of Edward Aetherling', $lib->getFamily());
        $this->assertEquals('margaret', $lib->getShort());
        $this->assertEquals('', $lib->getBirth());
        $this->assertEquals('1069', $lib->getDeath());
        $this->assertEquals('', $lib->getSuccesses());
        $this->assertEquals('female', $lib->getSex());
        $this->assertEquals('', $lib->getText());

        $this->assertEquals(4, count($lib->getChildren()));
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testLike(): void
    {
        if ($this->skipIt) {
            $this->markTestSkipped('Skipped by config');
            return;
        }

        $this->dataRefill();

        $lib = new EntryAdapter();
        $lib->setRecord(new PedigreeTestRecord());
        $result = $lib->getLike('Huntingdon', null);
        $this->assertEquals(2, count($result));
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testSetters(): void
    {
        if ($this->skipIt) {
            $this->markTestSkipped('Skipped by config');
            return;
        }

        $this->dataRefill();

        $lib = new EntryAdapter();
        $lib->setRecord(new PedigreeTestRecord());

        $lib->setId(777);
        $this->assertEquals(777, $lib->getId());

        $lib->setName('uhbzgv');
        $this->assertEquals('uhbzgv', $lib->getName());

        $lib->setFamily('sbtndngfmj');
        $this->assertEquals('sbtndngfmj', $lib->getFamily());

        $lib->setShort('uinjdhj');
        $this->assertEquals('uinjdhj', $lib->getShort());

        $lib->setBirth('399-11-06');
        $this->assertEquals('399-11-06', $lib->getBirth());

        $lib->setDeath('444-7-25');
        $this->assertEquals('444-7-25', $lib->getDeath());

        $lib->setSuccesses('yknlsyhgjyg');
        $this->assertEquals('yknlsyhgjyg', $lib->getSuccesses());

        $lib->setSex('male');
        $this->assertEquals('male', $lib->getSex());

        $lib->setText('ssdnghsdgklnhdfgh');
        $this->assertEquals('ssdnghsdgklnhdfgh', $lib->getText());
    }

    /**
     * @throws PedigreeException
     */
    public function testNoAdapter(): void
    {
        $lib = new EntryAdapter();
        $this->expectException(PedigreeException::class);
        $lib->setId(777);
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testOtherFile(): void
    {
        if ($this->skipIt) {
            $this->markTestSkipped('Skipped by config');
            return;
        }

        $this->dataRefill();

        $lib = new EntryAdapter();
        $lib->setRecord(new PedigreeTestRecord());
        $lib->setId(4);
        $lib->getRecord()->load();

        // no changes
        $this->assertNull($lib->saveFamily(null, null));
        // change both
        $this->assertTrue($lib->saveFamily(1, 2));

        $lib->getRecord()->load();

        $this->assertEquals(1, $lib->getFatherId());
        $this->assertEquals(2, $lib->getMotherId());
    }
}
