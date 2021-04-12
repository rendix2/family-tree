<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IM2NDeletor.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 21:52
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces;

/**
 * Interface IM2NDeletor
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces
 */
interface IM2NDeleter
{
    public function deleteByLeftKey($leftId);

    public function deleteByRightKey($rightId);

    public function deleteByLeftAndRightKey($leftId, $rightId);

}
