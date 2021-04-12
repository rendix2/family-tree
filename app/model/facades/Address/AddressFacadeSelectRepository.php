<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressSelectRepository.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 17:17
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Address;

use Rendix2\FamilyTree\App\Model\Managers\Address\IAddressSelectRepository;

/**
 * Class AddressFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Address
 */
class AddressFacadeSelectRepository implements IAddressSelectRepository
{
    private $addressFacadeCachedSelector;

    private $addressFacadeSelector;

    public function __construct(
        AddressFacadeCachedSelector $addressFacadeCachedSelector,
        AddressFacadeSelector $addressFacadeSelector
    ) {
        $this->addressFacadeCachedSelector = $addressFacadeCachedSelector;
        $this->addressFacadeSelector = $addressFacadeSelector;
    }

    /**
     * @return AddressFacadeSelector
     */
    public function getManager()
    {
        return $this->addressFacadeSelector;
    }

    /**
     * @return AddressFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->addressFacadeCachedSelector;
    }
}
