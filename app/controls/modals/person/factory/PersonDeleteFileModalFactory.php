<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteFileModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:09
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteFileModal;

/**
 * Interface PersonDeleteFileModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonDeleteFileModalFactory
{
    /**
     * @return PersonDeleteFileModal
     */
    public function create();
}
