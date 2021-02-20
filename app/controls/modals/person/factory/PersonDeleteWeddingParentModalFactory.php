<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteWeddingParentModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:17
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteWeddingParentModal;

/**
 * Interface PersonDeleteWeddingParentModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteWeddingParentModalFactory
{
    /**
     * @return PersonDeleteWeddingParentModal
     */
    public function create();
}
