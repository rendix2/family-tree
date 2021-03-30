<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddJobModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:19
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Factory;


use Rendix2\FamilyTree\App\Controls\Modals\Town\TownAddJobModal;

interface TownAddJobModalFactory
{
    /**
     * @return TownAddJobModal
     */
    public function create();
}