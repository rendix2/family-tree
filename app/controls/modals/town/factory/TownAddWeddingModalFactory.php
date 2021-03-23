<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddWeddingModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:20
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Town\TownAddWeddingModal;

interface TownAddWeddingModalFactory
{
    /**
     * @return TownAddWeddingModal
     */
    public function create();
}