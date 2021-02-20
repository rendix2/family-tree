<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSisterModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteSisterModal;

/**
 * Interface PersonDeleteSisterModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteSisterModalFactory
{
    /**
     * @return PersonDeleteSisterModal
     */
    public function create();
}
