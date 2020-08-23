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
     * CrudManager constructor.
     * @param Connection $dibi
     *
     * @throws Exception
     */
    public function __construct(Connection $dibi)
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
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param array $data
     *
     * @return Result|int
     *
     * @throws Exception
     */
    public function add($data)
    {
        return $this->dibi->insert($this->getTableName(), $data)
            ->execute(dibi::IDENTIFIER);
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
     * @throws Exception
     */
    public function updateByPrimaryKey($id, $data)
    {
        return $this->dibi->update($this->getTableName(), $data)
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute(dibi::AFFECTED_ROWS);
    }

    /**
     * @param int $id
     *
     * @return Result|int
     * @throws Exception
     */
    public function deleteByPrimaryKey($id)
    {
        return $this->dibi->delete($this->getTableName())
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute(dibi::AFFECTED_ROWS);
    }
}
