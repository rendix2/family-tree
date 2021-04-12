<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IM2NSelector.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 21:26
 */

namespace Rendix2\FamilyTree\App\Model\Managers\M2NManger\Interfaces;


interface IM2NSelector
{
    public function getAll();

    public function getColumnFluent($column);

    public function getByLeftKey($leftId);

    public function getPairsByLeft($leftId);

    public function getByLeftKeyJoined($leftId);



    public function getByRightKey($rightId);

    public function getPairsByRight($rightId);

    public function getByRightKeyJoined($rightId);

    public function getByLeftAndRightKey($leftId, $rightId);
}