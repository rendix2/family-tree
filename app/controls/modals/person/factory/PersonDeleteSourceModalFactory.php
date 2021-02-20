<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSourceModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteSourceModal;

/**
 * Interface PersonDeleteSourceModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteSourceModalFactory
{
    /**
     * @return PersonDeleteSourceModal
     */
    public function create();
}
