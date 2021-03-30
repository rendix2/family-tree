<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusPersonGenusDeleteModal.php
 * User: Tomáš Babický
 * Date: 29.11.2020
 * Time: 4:17
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Genus\GenusDeletePersonGenusModal;

/**
 * Interface GenusDeletePersonGenusModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory
 */
interface GenusDeletePersonGenusModalFactory
{
    /**
     * @return GenusDeletePersonGenusModal
     */
    public function create();
}
