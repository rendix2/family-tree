<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingAddTownModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 2:07
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Wedding\WeddingAddTownModal;

interface WeddingAddTownModalFactory
{
    /**
     * @return WeddingAddTownModal
     */
    public function create();
}