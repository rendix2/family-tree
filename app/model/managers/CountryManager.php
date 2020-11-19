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
}
