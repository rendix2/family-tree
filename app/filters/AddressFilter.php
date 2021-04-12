<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressFIlter.php
 * User: Tomáš Babický
 * Date: 01.09.2020
 * Time: 12:47
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\AddressEntity;

/**
 * Class AddressFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class AddressFilter implements IFilter
{
    /**
     * @param AddressEntity $address
     *
     * @return string
     */
    public function __invoke(AddressEntity $address)
    {
        if ($address->streetNumber && $address->houseNumber) {
            return $address->street . ' ' . $address->streetNumber . '/' . $address->houseNumber . ' ' . $address->town->zipCode . ' ' . $address->town->name;
        } else {
            if ($address->streetNumber) {
                return $address->street . ' ' . $address->streetNumber . ' ' . $address->town->zipCode . ' ' . $address->town->name;
            } elseif ($address->houseNumber) {
                return $address->street . ' ' . $address->houseNumber . ' ' . $address->town->zipCode . ' ' . $address->town->name;
            } else {
                return $address->street . ' ' . $address->town->zipCode . ' ' . $address->town->name;
            }
        }
    }
}
