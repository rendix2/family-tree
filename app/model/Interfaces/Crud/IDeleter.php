<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IDelete.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 20:51
 */

namespace Rendix2\FamilyTree\App\Model\Interfaces;

/**
 * Interface IDeleter
 *
 * @package Rendix2\FamilyTree\App\Model\Interfaces
 */
interface IDeleter
{
    public function deleteByPrimaryKey($id);
}
