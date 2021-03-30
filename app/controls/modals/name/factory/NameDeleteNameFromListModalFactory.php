<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameDeleteNameFromListModalFactory.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 21:32
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Name\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Name\NameDeleteNameFromListModal;

/**
 * Interface NameDeleteNameFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name\Factory
 */
interface NameDeleteNameFromListModalFactory
{
    /**
     * @return NameDeleteNameFromListModal
     */
    public function create();
}
