<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IUpdate.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 20:51
 */

namespace Rendix2\FamilyTree\App\Model\Interfaces;

/**
 * Interface IUpdater
 *
 * @package Rendix2\FamilyTree\App\Model\Interfaces
 */
interface IUpdater
{
    public function updateByPrimaryKey($id, array $data);
}
