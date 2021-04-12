<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IFileSelector.php
 * User: Tomáš Babický
 * Date: 04.04.2021
 * Time: 20:07
 */

namespace Rendix2\FamilyTree\App\Model\Managers\File\Interfaces;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;

/**
 * Interface IFileSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\File\Interfaces
 */
interface IFileSelector extends ISelector
{
    public function getByPersonId($personId);
}
