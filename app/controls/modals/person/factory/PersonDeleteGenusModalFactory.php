<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteGenusModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:09
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteGenusModal;

/**
 * Interface PersonDeleteGenusModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteGenusModalFactory
{
    /**
     * @return PersonDeleteGenusModal
     */
    public function create();
}
