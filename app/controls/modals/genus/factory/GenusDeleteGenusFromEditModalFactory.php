<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Genus\GenusDeleteGenusFromEditModal;

/**
 * Interface GenusDeleteGenusFromEditModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory
 */
interface GenusDeleteGenusFromEditModalFactory
{
    /**
     * @return GenusDeleteGenusFromEditModal
     */
    public function create();
}
