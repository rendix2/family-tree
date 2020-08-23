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

/**
 * Class AddressManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class AddressManager extends CrudManager
{
    /**
     * @return array
     */
    public function getCountByTown()
    {
        return $this->dibi
            ->select('COUNT(%n)', $this->getPrimaryKey())
            ->select('town')
            ->from($this->getTableName())
            ->groupBy('town')
            ->fetchAll();
    }
}
