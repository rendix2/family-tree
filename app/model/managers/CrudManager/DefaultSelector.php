<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DefaultSelecter.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 21:33
 */

namespace Rendix2\FamilyTree\App\Model\CrudManager;

use Dibi\Connection;
use Dibi\Fluent;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\IFilter;
use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;
use Rendix2\FamilyTree\App\Model\Interfaces\ITable;

/**
 * Class DefaultSelector
 *
 * @package Rendix2\FamilyTree\App\Model\CrudManager
 *
 */
class DefaultSelector implements ISelector
{
    /**
     * @var Connection $connection
     */
    private $connection;

    /**
     * @var IFilter $filter
     */
    private $filter;

    /**
     * @var ITable $table
     */
    private $table;

    /**
     * DefaultSelector constructor.
     *
     * @param Connection $connection
     * @param ITable     $table
     * @param IFilter    $filter
     */
    public function __construct(
        Connection $connection,
        ITable $table,
        IFilter $filter
    ) {
        $this->connection = $connection;
        $this->filter = $filter;
        $this->table = $table;
    }

    public function __destruct()
    {
        $this->connection = null;
        $this->filter = null;
        $this->table = null;
    }

    /**
     * @return ITable
     */
    public function getTable()
    {
        return $this->table;
    }

    public function getAllFluent()
    {
        return $this->connection
            ->select('*')
            ->from($this->table->getTableName());
    }

    public function getColumnFluent($column)
    {
        return $this->connection
            ->select($column)
            ->from($this->table->getTableName());
    }

    /**
     * @param string $column
     *
     * @return array
     */
    public function getPairs($column)
    {
        return $this->getAllFluent()
            ->fetchPairs($this->table->getPrimaryKey(), $column);
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    protected function applyFilter(array $rows)
    {
        $filter = $this->filter;

        $resultRows = [];

        foreach ($rows as $row) {
            /** @var IFilter|AddressFilter $filter BIG HACK!! */
            $resultRows[$row->id] = $filter($row);
        }

        return $resultRows;
    }

    public function getAllPairs()
    {
        $rows = $this->getAll();

        return $this->applyFilter($rows);
    }

    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->table->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass($this->table->getEntity())
            ->fetch();
    }

    public function getByPrimaryKeys(array $ids)
    {
        $ids = array_unique($ids);
        $count = count($ids);
        $values = array_values($ids);

        if ($count === 0) {
            return [];
        }

        if ($count === 1 && $values[0] === null) {
            return [];
        }

        return $this->getAllFluent()
            ->where('%n in %in', $this->table->getPrimaryKey(), $ids)
            ->execute()
            ->setRowClass($this->table->getEntity())
            ->fetchAll();
    }

    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->table->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass($this->table->getEntity())
            ->fetchAll();
    }

    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass($this->table->getEntity())
            ->fetchAll();
    }
}
