<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeManager.php
 * User: Tomáš Babický
 * Date: 01.10.2020
 * Time: 23:40
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Fluent;
use Rendix2\FamilyTree\App\Model\Entities\SourceTypeEntity;

/**
 * Class SourceTypeManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class SourceTypeManager extends CrudManager
{
    /**
     * @return SourceTypeEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(SourceTypeEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return SourceTypeEntity|false
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(SourceTypeEntity::class)
            ->fetch();
    }

    /**
     * @param array $ids
     *
     * @return SourceTypeEntity[]
     */
    public function getByPrimaryKeys(array $ids)
    {
        return $this->getAllFluent()
            ->where('%n in %in', $this->getPrimaryKey(), $ids)
            ->execute()
            ->setRowClass(SourceTypeEntity::class)
            ->fetchAll();
    }

    /**
     * @param Fluent $query
     *
     * @return SourceTypeEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass(SourceTypeEntity::class)
            ->fetchAll();
    }
}
