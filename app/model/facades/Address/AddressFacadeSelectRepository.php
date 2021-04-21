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

use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class AddressFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Address
 */
class AddressFacadeSelectRepository implements ISelectRepository
{
    /**
     * @var AddressFacadeCachedSelector $addressFacadeCachedSelector
     */
    private $addressFacadeCachedSelector;

    /**
     * @var AddressFacadeSelector $addressFacadeSelector
     */
    private $addressFacadeSelector;

    /**
     * AddressFacadeSelectRepository constructor.
     *
     * @param AddressFacadeCachedSelector $addressFacadeCachedSelector
     * @param AddressFacadeSelector       $addressFacadeSelector
     */
    public function __construct(
        AddressFacadeCachedSelector $addressFacadeCachedSelector,
        AddressFacadeSelector $addressFacadeSelector
    ) {
        $this->addressFacadeCachedSelector = $addressFacadeCachedSelector;
        $this->addressFacadeSelector = $addressFacadeSelector;
    }

    public function __destruct()
    {
        $this->addressFacadeSelector = null;
        $this->addressFacadeCachedSelector = null;
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
