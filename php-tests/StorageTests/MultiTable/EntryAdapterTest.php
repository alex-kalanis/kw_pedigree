<?php

namespace StorageTests\MultiTable;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Storage;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage\MultiTable\EntryAdapter;


class EntryAdapterTest extends AMultiTableTest
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
        $lib->setRecord(new PedigreeItemTestRecord());
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
        $lib->setRecord(new PedigreeItemTestRecord());
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
        $lib->setRecord(new PedigreeItemTestRecord());

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
        $lib->setRecord(new PedigreeItemTestRecord());
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

    /**
     * @throws MapperException
     * @throws PedigreeException
     */
    public function testChildren(): void
    {
        if ($this->skipIt) {
            $this->markTestSkipped('Skipped by config');
            return;
        }

        $this->dataRefill();

        $record1 = new PedigreeItemTestRecord();
        $record1->name = 'William I.';
        $record1->family = 'Dunkeld';
        $record1->short = 'william_i';
        $record1->birth = '1142';
        $record1->death = '1214';
        $this->assertTrue($record1->save());
        $this->assertTrue($record1->load());

        $record2 = new PedigreeItemTestRecord();
        $record2->name = 'Alexander II.';
        $record2->family = 'Dunkeld';
        $record2->short = 'alexander_ii';
        $record2->birth = '1198';
        $record2->death = '1249';
        $this->assertTrue($record2->save());
        $this->assertTrue($record2->load());

        $lib = new EntryAdapter();
        $lib->setRecord(new PedigreeItemTestRecord());
        $lib->setId(intval($record1->id));
        // first insert - true
        $this->assertTrue($lib->saveFamily(12, 13));
        // again - already known values - null
        $this->assertNull($lib->saveFamily(12, 13));

        $lib->setRecord(new PedigreeItemTestRecord());
        $lib->setId(intval($record2->id));
        // first insert - true
        $this->assertTrue($lib->saveFamily(intval($record1->id), null));
        // again - already known values - null
        $this->assertNull($lib->saveFamily(intval($record1->id), null));

        // set bad
        $this->assertTrue($lib->setMotherId(4));
        // remove bad
        $this->assertTrue($lib->setMotherId(null));
        // set different
        $this->assertTrue($lib->setFatherId(3));
        // set different
        $this->assertTrue($lib->setFatherId(intval($record1->id)));

        // now test unlink both ancestors and successors
        $this->assertTrue($record1->delete());
    }
}
