<?php
/**
 *
 * Created by PhpStorm.
 * Filename: IWeddingSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 22:18
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Wedding\Interfaces;

use Dibi\Row;
use Rendix2\FamilyTree\App\Model\Entities\WeddingEntity;
use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;

interface IWeddingSelector extends ISelector
{
    /**
     * @param int|null $husbandId
     *
     * @return WeddingEntity[]
     */
    public function getAllByHusbandId($husbandId);

    /**
     * @param int $wifeId
     *
     * @return WeddingEntity[]
     */
    public function getAllByWifeId($wifeId);

    /**
     * @param int $wifeId
     * @param int $husbandId
     *
     * @return Row|false
     */
    public function getByWifeIdAndHusbandId($wifeId, $husbandId);

    /**
     * @param int $townId
     *
     * @return WeddingEntity[]
     */
    public function getByTownId($townId);


    /**
     * @param int $addressId
     *
     * @return WeddingEntity[]
     */
    public function getByAddressId($addressId);
}
