<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddGenusModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:57
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddGenusModal;

/**
 * Interface PersonAddGenusModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddGenusModalFactory
{
    /**
     * @return PersonAddGenusModal
     */
    public function create();
}