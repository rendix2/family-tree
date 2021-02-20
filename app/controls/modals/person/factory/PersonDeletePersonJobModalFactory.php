<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeletePersonJobModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:13
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeletePersonJobModal;

/**
 * Interface PersonDeletePersonJobModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeletePersonJobModalFactory
{
    /**
     * @return PersonDeletePersonJobModal
     */
    public function create();
}
