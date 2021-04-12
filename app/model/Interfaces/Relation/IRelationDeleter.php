<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IRelationDeleter.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 21:44
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Relation;

use Rendix2\FamilyTree\App\Model\Interfaces\IDeleter;

interface IRelationDeleter extends IDeleter
{

    public function deleteByMaleId($maleId);

    public function deleteByFemaleId($femaleId);

    public function deleteByMaleIdAndFemaleId($maleId, $femaleId);

}