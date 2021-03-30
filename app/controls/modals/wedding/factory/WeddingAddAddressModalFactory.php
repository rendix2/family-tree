<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingAddAddressModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:07
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Wedding\WeddingAddAddressModal;

interface WeddingAddAddressModalFactory
{
    /**
     * @return WeddingAddAddressModal
     */
    public function create();
}