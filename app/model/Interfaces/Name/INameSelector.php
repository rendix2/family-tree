<?php
/**
 *
 * Created by PhpStorm.
 * Filename: INameSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 22:54
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Name\Interfaces;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;

/**
 * Interface INameSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Name\Interfaces
 */
interface INameSelector extends ISelector
{

    public function getByPersonId($personId);

    public function getByGenusId($genusId);

}
