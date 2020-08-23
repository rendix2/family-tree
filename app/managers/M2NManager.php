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

    //// LEFT

    /**
     * @param $leftId
     * @return \Dibi\Fluent
     */
    public function getFluentByLeft($leftId)
    {
        return $this->getAllFLuent()
            ->where('%n = %i', $this->leftKey, $leftId);
    }

    /**
     * @param $leftId
     * @return array
     */
    public function getAllByLeft($leftId)
    {
        return $this->getFluentByLeft($leftId)->fetchAll();
    }

    /**
     * @param $leftId
     * @return \Dibi\Fluent
     */
    public function getFluentByLeftJoined($leftId)
    {
        return $this->getAllFLuent()
            ->innerJoin($this->rightTable->getTableName())
            ->on('%n = %n', $this->tableName. '.' . $this->rightKey, $this->rightTable->getTableName() . '.'. $this->rightTable->getPrimaryKey())
            ->where('%n = %i', $this->leftKey, $leftId);
    }

    /**
     * @param $leftId
     * @return array
     */
    public function getAllByLeftJoined($leftId)
    {
        return $this->getFluentByLeftJoined($leftId)->fetchAll();
    }

    //// RIGHT

    /**
     * @param $rightId
     * @return \Dibi\Fluent
     */
    public function getFluentByRight($rightId)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->rightKey, $rightId);
    }

    /**
     * @param $rightId
     * @return array
     */
    public function getAllByRight($rightId)
    {
        return $this->getFluentByRight($rightId)->fetchAll();
    }

    /**
     * @param $rightId
     * @return \Dibi\Fluent
     */
    public function getFluentByRightJoined($rightId)
    {
        return $this->getAllFLuent()
            ->innerJoin($this->leftTable->getTableName())
            ->on('%n = %n', $this->tableName. '.' . $this->leftKey, $this->leftTable->getTableName() . '.'. $this->leftTable->getPrimaryKey())
            ->where('%n = %i', $this->rightKey, $rightId);
    }

    /**
     * @param $rightId
     * @return array
     */
    public function getAllByRightJoined($rightId)
    {
        return $this->getFluentByRightJoined($rightId)->fetchAll();
    }

    /**
     * @param $leftId
     * @param $rightId
     * @return \Dibi\Fluent
     */
    public function getFluentFull($leftId, $rightId)
    {
        return $this->getAllFLuent()
            ->where('%n = %i', $this->leftKey, $leftId)
            ->where('%n = %i', $this->rightKey, $rightId);
    }

    /**
     * @param $leftId
     * @param $rightId
     * @return \Dibi\Row|false
     */
    public function getFull($leftId, $rightId)
    {
        return $this->getFluentFull($leftId, $rightId)
            ->fetch();
    }

    /**
     * @param $leftId
     * @param $rightId
     * @return \Dibi\Row|false
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

    /**
     * @param $leftId
     * @param $rightId
     * @return bool
     */
    public function getFullCheck($leftId, $rightId)
    {
        return $this->getFluentFull($leftId, $rightId)->fetchSingle() === 1;
    }

    //// add

    /**
     * @param $leftId
     * @param $rightId
     * @return \Dibi\Result|int
     * @throws Exception
     */
    public function add($leftId, $rightId)
    {
        return $this->dibi->insert($this->getTableName(), [$this->leftKey => $leftId, $this->rightKey => $rightId])
            ->execute();
    }

    /**
     * @param $leftId
     * @param array $rightIds
     * @throws Exception
     */
    public function addByLeft($leftId, array $rightIds) {
        foreach ($rightIds as $rightId) {
            $this->add( $leftId, $rightId);
        }
    }

    /**
     * @param $rightId
     * @param array $leftIds
     * @throws Exception
     */
    public function addByRight($rightId, array $leftIds) {
        foreach ($leftIds as $leftId) {
            $this->add($leftId, $rightId);
        }
    }

    //// delete

    /**
     * @param $leftId
     * @return \Dibi\Result|int
     * @throws Exception
     */
    public function deleteByLeft($leftId)
    {
        return $this->dibi->delete($this->getTableName())
            ->where('%n = %i', $this->leftKey, $leftId)
            ->execute();
    }

    /**
     * @param $rightId
     * @return \Dibi\Result|int
     * @throws Exception
     */
    public function deleteByRight($rightId)
    {
        return $this->dibi->delete($this->getTableName())
            ->where('%n = %i', $this->rightKey, $rightId)
            ->execute();
    }
}
