<?php

namespace StorageTests\SingleTable;


use Builder;
use kalanis\kw_mapper\Interfaces\IQueryBuilder;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Storage\Database\Dialects;


/**
 * Class MySqlTest
 * @package StorageTests\SingleTable
 * @requires extension PDO
 * @requires extension pdo_mysql
 */
class MySqlTest extends ASingleTableTest
{
    /**
     * @throws MapperException
     */
    public function testSimpleProcess(): void
    {
        if ($this->skipIt) {
            $this->markTestSkipped('Skipped by config');
            return;
        }

        $this->database->reconnect();
        $this->assertFalse($this->database->exec('', []));
        $this->database->reconnect();
        $this->assertEmpty($this->database->query('', []));

        $this->dataRefill();

        $query = new Builder();
        $query->setBaseTable('kw_pedigree');
        $sql = new Dialects\MySQL();
        $result = $this->database->query($sql->describe($query), []);
//var_dump($result);
        $this->assertNotEmpty($result, 'There MUST be table from file!');

        $query->addColumn('kw_pedigree', 'pedigree_name');
        $query->addColumn('kw_pedigree', 'pedigree_family');
        $lines = $this->database->query($sql->select($query), $query->getParams());
//var_dump(['full dump' => $lines]);
        $this->assertEquals(14, count($lines));
        $query->addCondition('kw_pedigree', 'pedigree_birth', IQueryBuilder::OPERATION_LT, "1100");
//var_dump(['query dump' => str_split($sql->select($query), 120)]);
        $lines = $this->database->query($sql->select($query), $query->getParams());
        $this->assertEquals(12, count($lines));

        $query->addCondition('kw_pedigree', 'pedigree_birth', IQueryBuilder::OPERATION_NEQ, "");
//var_dump(['query dump' => str_split($sql->select($query), 120)]);
        $lines = $this->database->query($sql->select($query), $query->getParams());
        $this->assertEquals(9, count($lines));

        $this->assertTrue($this->database->beginTransaction());
        $this->database->exec('INSERT INTO `kw_pedigree` (`pedigree_name`, `pedigree_family`, `pedigree_birth`, `pedigree_death`, `pedigree_father_id`, `pedigree_mother_id`, `pedigree_sex`) VALUES ("William I.", "Dunkeld", "1142", "1214", 12, 13, "male");', []);
        $this->assertTrue($this->database->commit());
        $this->assertEquals(1, $this->database->rowCount());
        $this->assertTrue($this->database->beginTransaction());
        $this->database->exec('INSERT INTO `kw_pedigree` (`pedigree_name`, `pedigree_family`, `pedigree_birth`, `pedigree_death`, `pedigree_father_id`, `pedigree_mother_id`, `pedigree_sex`) VALUES ("David", "of Huntingdon", "1152", "1219", 12, 13, "male");', []);
        $this->assertTrue($this->database->rollBack());

        $lines = $this->database->query($sql->select($query), $query->getParams());
        $this->assertEquals(9, count($lines));
    }
}
