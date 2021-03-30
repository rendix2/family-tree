<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSonModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteSonModal;

/**
 * Interface PersonDeleteSonModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteSonModalFactory
{
    /**
     * @return PersonDeleteSonModal
     */
    public function create();
}
