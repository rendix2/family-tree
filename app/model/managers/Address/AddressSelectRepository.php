<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressSelectRepository.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 23:48
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Address;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class AddressSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Address
 */
class AddressSelectRepository implements ISelectRepository
{
    /**
     * @var AddressCachedSelector $addressCachedSelector
     */
    private $addressCachedSelector;

    /**
     * @var AddressSelector $addressSelector
     */
    private $addressSelector;

    /**
     * AddressSelectRepository constructor.
     *
     * @param AddressCachedSelector $addressCachedSelector
     * @param AddressSelector       $addressSelector
     */
    public function __construct(
        AddressCachedSelector $addressCachedSelector,
        AddressSelector $addressSelector
    ) {
        $this->addressCachedSelector = $addressCachedSelector;
        $this->addressSelector = $addressSelector;
    }

    public function __destruct()
    {
        $this->addressSelector = null;
        $this->addressCachedSelector = null;
    }

    /**
     * @return AddressSelector
     */
    public function getManager()
    {
        return $this->addressSelector;
    }

    public function getCachedManager()
    {
        return $this->addressCachedSelector;
    }
}
