<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ${FILE_NAME}
 * User: Tomáš Babický
 * Date: 04.10.2020
 * Time: 21:53
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Fluent;
use Rendix2\FamilyTree\App\Model\Entities\CountryEntity;

/**
 * Class CountryManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class CountryManager extends CrudManager
{
    /**
     * @return CountryEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()->execute()->setRowClass(CountryEntity::class)->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return CountryEntity
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(CountryEntity::class)
            ->fetch();
    }

    /**
     * @param array $ids
     *
     * @return CountryEntity[]|false
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
            ->setRowClass(CountryEntity::class)
            ->fetchAll();
    }

    /**
     * @param Fluent $query
     *
     * @return CountryEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass(CountryEntity::class)
            ->fetchAll();
    }
}
