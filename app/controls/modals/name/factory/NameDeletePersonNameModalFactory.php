<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameDeletePersonNameModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 21:32
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Name\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Name\NameDeletePersonNameModal;

/**
 * Interface NameDeletePersonNameModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name\Factory
 */
interface NameDeletePersonNameModalFactory
{
    /**
     * @return NameDeletePersonNameModal
     */
    public function create();
}
