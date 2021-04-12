<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IM2NInsertor.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 21:51
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces;

use Rendix2\FamilyTree\App\Model\Interfaces\IInserter;

/**
 * Interface IM2NInsertor
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces
 */
interface IM2NInserter extends IInserter
{
    public function insertLeftAndRight($leftId, $rightId);

    public function insertByLeft($leftId, array $rightIds);

    public function insertByRight(array $leftIds, $rightId);
}
