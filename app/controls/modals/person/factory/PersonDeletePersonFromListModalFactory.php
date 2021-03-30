<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeletePersonFromListModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 1:44
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeletePersonFromListModal;

/**
 * Interface PersonDeletePersonFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeletePersonFromListModalFactory
{
    /**
     * @return PersonDeletePersonFromListModal
     */
    public function create();
}
