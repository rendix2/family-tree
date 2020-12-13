<?php
/**
 *
 * Created by PhpStorm.
 * Filename: s.php
 * User: Tomáš Babický
 * Date: 23.08.2020
 * Time: 15:11
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Fluent;
use Rendix2\FamilyTree\App\Model\Entities\GenusEntity;

/**
 * Class GenusManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class GenusManager extends CrudManager
{
    /**
     * @return GenusEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()
            ->execute()
            ->setRowClass(GenusEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return GenusEntity
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(GenusEntity::class)
            ->fetch();
    }

    /**
     * @param array $ids
     *
     * @return GenusEntity[]|false
     */
    public function getByPrimaryKeys(array $ids)
    {
        $result = $this->checkValues($ids);

        if ($result !== null) {
            return $result;
        }

        return $this->getAllFluent()
            ->where('%n in %in', $this->getPrimaryKey(), $ids)
            ->execute()
            ->setRowClass(GenusEntity::class)
            ->fetchAll();
    }

    /**
     * @param Fluent $query
     *
     * @return GenusEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass(GenusEntity::class)
            ->fetchAll();
    }
}
