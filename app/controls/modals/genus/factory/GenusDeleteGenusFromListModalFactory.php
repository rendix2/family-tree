<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Genus\GenusDeleteGenusFromListModal;

/**
 * Interface GenusDeleteGenusFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory
 */
interface GenusDeleteGenusFromListModalFactory
{
    /**
     * @return GenusDeleteGenusFromListModal
     */
    public function create();
}