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
 * Class GenusManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class GenusManager extends CrudManager
{
    public function get()
    {
        return $this->dibi
            ->select('surname')
            ->as('id')
            ->select('surname')
            ->from($this->getTableName())
            ->fetchAll();
    }

}
