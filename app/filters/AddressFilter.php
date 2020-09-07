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
        return $address->street . ' ' . $address->streetNumber .'/'. $address->houseNumber . ' '  . $address->zip . ' ' . $address->town;
    }
}
