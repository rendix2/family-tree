<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPersonNameModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:03
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPersonNameModal;

/**
 * Interface PersonAddPersonNameModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddPersonNameModalFactory
{
    /**
     * @return PersonAddPersonNameModal
     */
    public function create();
}
