<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CrudManager.php
 * User: Tomáš Babický
 * Date: 23.08.2020
 * Time: 15:29
 */

namespace Rendix2\FamilyTree\App\Managers;

use dibi;
use Dibi\Connection;
use Dibi\Exception;
use Dibi\Result;
use Dibi\Row;

/**
 * Class CrudManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
abstract class CrudManager extends DibiManager
{
    /**
     * @var string $primaryKey
     */
    private $primaryKey;

    /**
     * @var BackupManager $backupManager
     */
    private $backupManager;

    /**
     * CrudManager constructor.
     * @param Connection $dibi
     *
     * @param BackupManager $backupManager
     * @throws Exception
     */
    public function __construct(Connection $dibi, BackupManager $backupManager)
    {
        parent::__construct($dibi);

        $tableName = $this->getTableName();

        $table = $this->dibi->getDatabaseInfo()->getTable($this->getTableName());

        if (count($table->getPrimaryKey()->getColumns()) !== 1) {
            $message = sprintf('Primary key of table "%s" is not on only one column.', $tableName);

            throw new Exception($message);
        }

        if ($table->getPrimaryKey()->getColumns()[0]->getNativeType() !== 'INT') {
            $message = sprintf('Primary key of table "%s" is not integer.', $tableName);

            throw new Exception($message);
        }

        $this->primaryKey = $table->getPrimaryKey()->getColumns()[0]->getName();
        $this->backupManager = $backupManager;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param string $column
     *
     * @return array
     */
    public function getPairs($column)
    {
        return $this->getAllFluent()->fetchPairs($this->primaryKey, $column);
    }

    /**
     * @param array $data
     *
     * @return Result|int
     */
    public function add($data)
    {
        $res = $this->dibi->insert($this->getTableName(), $data)
            ->execute(dibi::IDENTIFIER);

        $this->backupManager->backup();

        return $res;
    }

    /**
     * @param int $id
     *
     * @return Row|false
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->fetch();
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return Result|int
     */
    public function updateByPrimaryKey($id, $data)
    {
        $res = $this->dibi->update($this->getTableName(), $data)
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute(dibi::AFFECTED_ROWS);

        $this->backupManager->backup();

        return $res;
    }

    /**
     * @param int $id
     *
     * @return Result|int
     */
    public function deleteByPrimaryKey($id)
    {
        $res = $this->dibi->delete($this->getTableName())
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute(dibi::AFFECTED_ROWS);

        $this->backupManager->backup();

        return $res;
    }
}
