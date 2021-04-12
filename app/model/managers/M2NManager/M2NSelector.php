<?php
/**
 *
 * Created by PhpStorm.
 * Filename: M2NSelector.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 21:28
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger;

use Dibi\Connection;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NSelector;
use Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces\IM2NTable;

/**
 * Class M2NSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\M2NManger
 */
class M2NSelector implements IM2NSelector
{
    const ALIAS_NAME = 'relation';

    /**
     * @var Connection $connection
     */
    private $connection;

    /**
     * @var ITable $leftTable
     */
    private $leftTable;

    /**
     * @var ITable $rightTable
     */
    private $rightTable;

    /**
     * @var IM2NTable $table
     */
    private $table;

    /**
     * M2NSelector constructor.
     *
     * @param Connection $connection
     * @param IM2NTable  $table
     * @param ITable     $leftTable
     * @param ITable     $rightTable
     */
    public function __construct(
        Connection $connection,
        IM2NTable $table,
        ITable $leftTable,
        ITable $rightTable
    ) {
        $this->connection = $connection;
        $this->table = $table;

        $this->leftTable = $leftTable;
        $this->rightTable = $rightTable;
    }

    public function getAllFluent()
    {
        return $this->connection
            ->select('*')
            ->from($this->table->getTableName())
            ->as(static::ALIAS_NAME);
    }

    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass($this->table->getEntity())
            ->fetchAll();
    }

    public function getColumnFluent($column)
    {
        return $this->connection
            ->select($column)
            ->from($this->table->getTableName());
    }

    public function getByLeftKey($leftId)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->table->getLeftPrimaryKey(), $leftId)
            ->execute()
            ->setRowClass($this->table->getEntity())
            ->fetchAll();
    }

    public function getPairsByLeft($leftId)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->table->getLeftPrimaryKey(), $leftId)
            //->execute()
            //->setRowClass($this->table->getEntity())
            ->fetchPairs(null, $this->table->getRightPrimaryKey());
    }

    public function getByLeftKeyJoined($leftId)
    {
        return $this->getAllFluent()
            ->innerJoin($this->leftTable->getTableName())
            ->on('%n = %n',
                static::ALIAS_NAME . '.' . $this->table->getLeftPrimaryKey(),
                $this->leftTable->getTableName() . '.' . $this->leftTable->getPrimaryKey()
            )
            ->where('%n = %i', $this->table->getLeftPrimaryKey(), $leftId)
            ->execute()
            ->setRowClass($this->table->getEntity())
            ->fetchAll();
    }

    public function getByRightKey($rightId)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->table->getRightPrimaryKey(), $rightId)
            ->execute()
            ->setRowClass($this->table->getEntity())
            ->fetchAll();
    }

    public function getPairsByRight($rightId)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->table->getRightPrimaryKey(), $rightId)
            //->execute()
            //->setRowClass($this->table->getEntity())
            ->fetchPairs(null, $this->table->getLeftPrimaryKey());
    }

    public function getByRightKeyJoined($rightId)
    {
        return $this->getAllFluent()
            ->innerJoin($this->rightTable->getTableName())
            ->on('%n = %n',
                static::ALIAS_NAME . '.' . $this->table->getRightPrimaryKey(),
                $this->rightTable->getTableName() . '.' . $this->rightTable->getPrimaryKey()
            )
            ->where('%n = %i', $this->table->getRightPrimaryKey(), $rightId)
            ->execute()
            ->setRowClass($this->table->getEntity())
            ->fetchAll();
    }

    public function getByLeftAndRightKey($leftId, $rightId)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->table->getLeftPrimaryKey(), $leftId)
            ->where('%n = %i', $this->table->getRightPrimaryKey(), $rightId)
            ->execute()
            ->setRowClass($this->table->getEntity())
            ->fetch();
    }
}
