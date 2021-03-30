<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusAddNameModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 0:45
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Genus\GenusAddNameModal;

/**
 * Interface GenusAddNameModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory
 */
interface GenusAddNameModalFactory
{
    /**
     * @return GenusAddNameModal
     */
    public function create();
}
