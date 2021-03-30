<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteNameModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeletePersonNameModal;

/**
 * Interface PersonDeleteNameModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeletePersonNameModalFactory
{
    /**
     * @return PersonDeletePersonNameModal
     */
    public function create();
}
