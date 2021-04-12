<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IRelationSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 21:43
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Relation;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;

interface IRelationSelector extends ISelector
{
    public function getByMaleId($maleId);

    public function getByFemaleId($femaleId);

    public function getByMaleIdAndFemaleId($maleId, $femaleId);
}
