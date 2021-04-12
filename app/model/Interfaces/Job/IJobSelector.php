<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IJobSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 15:18
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Job\Interfaces;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;

/**
 * Interface IJobSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Job\Interfaces
 */
interface IJobSelector extends ISelector
{

    public function getByTownId($townId);

    public function getByAddressId($addressId);
}