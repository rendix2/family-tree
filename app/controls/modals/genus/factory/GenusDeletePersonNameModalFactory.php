<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusPersonNameDeleteModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 0:25
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Genus\GenusDeletePersonNameModal;

/**
 * Interface GenusDeletePersonNameModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory
 */
interface GenusDeletePersonNameModalFactory
{
    /**
     * @return GenusDeletePersonNameModal
     */
    public function create();
}
