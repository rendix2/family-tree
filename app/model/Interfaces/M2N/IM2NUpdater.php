<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IM2NUpdator.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 22:07
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces;

/**
 * Interface IM2NUpdator
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces
 */
interface IM2NUpdater
{
    public function updateByLeftAndRight($leftId, $rightId, array $data);
}
