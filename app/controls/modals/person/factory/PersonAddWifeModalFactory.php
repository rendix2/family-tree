<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddWifeModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:07
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddWifeModal;

/**
 * Interface PersonAddWifeModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddWifeModalFactory
{
    /**
     * @return PersonAddWifeModal
     */
    public function create();
}
