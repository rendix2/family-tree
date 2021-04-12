<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IWeddingDeletor.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 22:22
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Wedding\Interfaces;

use Rendix2\FamilyTree\App\Model\Interfaces\IDeleter;

/**
 * Interface IWeddingDeleter
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Wedding\Interfaces
 */
interface IWeddingDeleter extends IDeleter
{
    public function deleteByHusbandId($id);

    public function deleteByWifeId($id);

    public function deleteByHusbandIdAndWifeId($husbandId, $wifeId);
}
