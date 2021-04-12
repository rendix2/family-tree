<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ISourceSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 2:51
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Source\Interfaces;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;

/**
 * Interface ISourceSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Source\Interfaces
 */
interface ISourceSelector extends ISelector
{
    public function getByPersonId($personId);

    public function getBySourceTypeId($sourceTypeId);
}
