<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteWeddingModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:17
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteWeddingModal;

/**
 * Interface PersonDeleteWeddingModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteWeddingModalFactory
{
    /**
     * @return PersonDeleteWeddingModal
     */
    public function create();
}
