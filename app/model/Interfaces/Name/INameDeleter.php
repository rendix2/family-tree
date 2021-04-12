<?php
/**
 *
 * Created by PhpStorm.
 * Filename: INameDeleter.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 23:00
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Name\Interfaces;

use Rendix2\FamilyTree\App\Model\Interfaces\IDeleter;

/**
 * Interface INameDeleter
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Name\Interfaces
 */
interface INameDeleter extends IDeleter
{
    public function deleteByPersonId($personId);
}
