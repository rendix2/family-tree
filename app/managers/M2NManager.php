<?php
/**
 *
 * Created by PhpStorm.
 * Filename: M2NManager.php
 * User: Tomáš Babický
 * Date: 23.08.2020
 * Time: 15:42
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Connection;
use Dibi\Exception;
use Dibi\Fluent;
use Dibi\Result;
use Dibi\Row;

/**
 * Class M2NManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
abstract class M2NManager extends DibiManager
{
    /**
     * @var string
     */
    const TABLE_NAME_JOINER = '2';

    /**
     * @var string $tableName
     */
    private $tableName;

    /**
     * @var CrudManager $leftTable
     */
    private $leftTable;

    /**
     * @var CrudManager $rightTable
     */
    private $rightTable;

    /**
     * @var string $leftKey
     */
    private $leftKey;

    /**
     * @var string $rightKey
     */
    private $rightKey;

    /**
     * M2NManager constructor.
     *
     * @param Connection $dibi
     * @param CrudManager $left
     * @param CrudManager $right
     *
     * @throws Exception
     */
    public function __construct(Connection $dibi, CrudManager $left, CrudManager $right)
    {
        parent::__construct($dibi);

        $this->leftTable = $left;
        $this->rightTable = $right;

        $tableName = $left->getTableName() . self::TABLE_NAME_JOINER . $right->getTableName();

        if ($this->dibi->getDatabaseInfo()->hasTable($tableName)) {
            $this->tableName = $tableName;

            $primaryKeyColumns = $this->dibi->getDatabaseInfo()->getTable($this->tableName)->getPrimaryKey()->getColumns();

            if (count($primaryKeyColumns) !== 2) {
                $message = sprintf('Primary key of table "%s" is not two columned.', $tableName);

                throw new Exception($message);
            }

            $leftKey = $primaryKeyColumns[0]->getName();
            $rightKey = $primaryKeyColumns[1]->getName();

            if (strpos($leftKey, $left->getTableName()) === false) {
                $message = sprintf('Left column should contains left table name.');

                throw new Exception($message);
            }

            if (strpos($rightKey, $right->getTableName()) === false) {
                $message = sprintf('Right column should contains right table name.');

                throw new Exception($message);
            }

            $this->leftKey = $leftKey;
            $this->rightKey = $rightKey;
        } else {
            $message = sprintf('M2N table "%s" does not exist.', $this->tableName);

            throw new Exception($message);
        }
    }

    /// GETTERS

    /**
     * @return string
     */
    public function getLeftKey()
    {
        return $this->leftKey;
    }

    /**
     * @return string
     */
    public function getRightKey()
    {
        return $this->rightKey;
    }

    /**
     * @return CrudManager
     */
    public function getLeftTable()
    {
        return $this->leftTable;
    }

    /**
     * @return CrudManager
     */
    public function getRightTable()
    {
        return $this->rightTable;
    }

    //// LEFT

    /**
     * @param int $leftId
     *
     * @return Fluent
     */
    public function getFluentByLeft($leftId)
    {
        return $this->getAllFLuent()
            ->where('%n = %i', $this->leftKey, $leftId);
    }

    /**
     * @param int $leftId
     *
     * @return array
     */
    public function getAllByLeft($leftId)
    {
        return $this->getFluentByLeft($leftId)->fetchAll();
    }

    /**
     * @param int $leftId
     *
     * @return array
     */
    public function getPairsByLeft($leftId)
    {
        return $this->getFluentByLeft($leftId)->fetchPairs(null, $this->rightKey);
    }

    /**
     * @param int $leftId
     *
     * @return Fluent
     */
    public function getFluentByLeftJoined($leftId)
    {
        return $this->getAllFLuent()
            ->innerJoin($this->rightTable->getTableName())
            ->on('%n = %n', $this->tableName. '.' . $this->rightKey, $this->rightTable->getTableName() . '.'. $this->rightTable->getPrimaryKey())
            ->where('%n = %i', $this->leftKey, $leftId);
    }

    /**
     * @param int $leftId
     *
     * @return array
     */
    public function getAllByLeftJoined($leftId)
    {
        return $this->getFluentByLeftJoined($leftId)->fetchAll();
    }

    //// RIGHT

    /**
     * @param int $rightId
     *
     * @return Fluent
     */
    public function getFluentByRight($rightId)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->rightKey, $rightId);
    }

    /**
     * @param int $rightId
     *
     * @return array
     */
    public function getAllByRight($rightId)
    {
        return $this->getFluentByRight($rightId)->fetchAll();
    }

    /**
     * @param int $rightId
     *
     * @return array
     */
    public function getPairsByRight($rightId)
    {
        return $this->getFluentByRight($rightId)->fetchPairs(null, $this->leftKey);
    }

    /**
     * @param int $rightId
     *
     * @return Fluent
     */
    public function getFluentByRightJoined($rightId)
    {
        return $this->getAllFLuent()
            ->innerJoin($this->leftTable->getTableName())
            ->on('%n = %n', $this->tableName. '.' . $this->leftKey, $this->leftTable->getTableName() . '.'. $this->leftTable->getPrimaryKey())
            ->where('%n = %i', $this->rightKey, $rightId);
    }

    /**
     * @param int $rightId
     *
     * @return array
     */
    public function getAllByRightJoined($rightId)
    {
        return $this->getFluentByRightJoined($rightId)->fetchAll();
    }

    /**
     * @param int $leftId
     * @param int $rightId
     *
     * @return Row|false
     */
    public function getByLeftIdAndRightId($leftId, $rightId)
    {
        return $this->getAllFLuent()
            ->where('%n = %i', $this->leftKey, $leftId)
            ->where('%n = %i', $this->rightKey, $rightId)
            ->fetch();
    }

    /**
     * @param int $leftId
     * @param int $rightId
     *
     * @return Row|false
     */
    public function getFullJoined($leftId, $rightId)
    {
        return $this->getAllFLuent()
            ->innerJoin($this->leftTable->getTableName())
            ->on('%n = %n', $this->tableName. '.' . $this->leftKey, $this->leftTable->getTableName() . '.'. $this->leftTable->getPrimaryKey())
            ->innerJoin($this->rightTable->getTableName())
            ->on('%n = %n', $this->tableName. '.' . $this->rightKey, $this->rightTable->getTableName() . '.'. $this->rightTable->getPrimaryKey())
            ->where('%n = %i', $this->leftKey, $leftId)
            ->where('%n = %i', $this->rightKey, $rightId)
            ->fetch();
    }

    //// add

    /**
     * @param array $data
     */
    public function addGeneral($data)
    {
        $this->dibi->insert($this->tableName, $data)->execute();
    }

    /**
     * @param int $leftId
     * @param int $rightId
     *
     * @return Result|int
     */
    public function add($leftId, $rightId)
    {
        return $this->dibi->insert($this->tableName, [$this->leftKey => $leftId, $this->rightKey => $rightId])
            ->execute();
    }

    /**
     * @param int $leftId
     * @param array $rightIds
     */
    public function addByLeft($leftId, array $rightIds) {
        foreach ($rightIds as $rightId) {
            $this->add( $leftId, $rightId);
        }
    }

    /**
     * @param int $rightId
     * @param array $leftIds
     */
    public function addByRight($rightId, array $leftIds) {
        foreach ($leftIds as $leftId) {
            $this->add($leftId, $rightId);
        }
    }

    //// delete

    /**
     * @param int $leftId
     *
     * @return Result|int
     */
    public function deleteByLeft($leftId)
    {
        return $this->dibi->delete($this->getTableName())
            ->where('%n = %i', $this->leftKey, $leftId)
            ->execute();
    }

    /**
     * @param int $rightId
     *
     * @return Result|int
     */
    public function deleteByRight($rightId)
    {
        return $this->dibi->delete($this->getTableName())
            ->where('%n = %i', $this->rightKey, $rightId)
            ->execute();
    }

    /**
     * @param int $leftId
     * @param int $rightId
     *
     * @return Result|int
     */
    public function deleteByLeftIdAndRightId($leftId, $rightId)
    {
        return $this->dibi->delete($this->getTableName())
            ->where('%n = %i', $this->leftKey, $leftId)
            ->where('%n = %i', $this->rightKey, $rightId)
            ->execute();
    }

    /**
     * @param int $leftId
     * @param int $rightId
     *
     * @param array $data
     *
     * @return Result|int
     */
    public function updateGeneral($leftId, $rightId, array $data)
    {
        return $this->dibi->update($this->getTableName(), $data)
            ->where('%n = %i', $this->leftKey, $leftId)
            ->where('%n = %i', $this->rightKey, $rightId)
            ->execute();
    }
}
