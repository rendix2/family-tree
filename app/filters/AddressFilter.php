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

use Dibi\Row;

/**
 * Class AddressFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class AddressFilter
{
    /**
     * @param Row $address
     * @return string
     */
    public function __invoke(Row $address)
    {
        return self::address($address);
    }

    /**
     * @param Row $address
     *
     * @return string
     */
    public static function address(Row $address)
    {
        if ($address->streetNumber && $address->houseNumber) {
            return $address->street . ' ' . $address->streetNumber . '/' . $address->houseNumber . ' ' . $address->townZipCode . ' ' . $address->townName;
        } else {
            if ($address->streetNumber) {
                return $address->street . ' ' . $address->streetNumber . ' ' . $address->townZipCode . ' ' . $address->townName;
            } elseif ($address->houseNumber) {
                return $address->street . ' ' . $address->houseNumber . ' ' . $address->townZipCode . ' ' . $address->townName;
            } else {
                return $address->street . ' ' . $address->townZipCode . ' ' . $address->townName;
            }
        }
    }
}