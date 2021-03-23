<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteWeddingModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:22
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Town\TownDeleteWeddingModal;

interface TownDeleteWeddingModalFactory
{
    /**
     * @return TownDeleteWeddingModal
     */
    public function create();
}