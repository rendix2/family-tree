<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddFileModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:55
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddFileModal;

/**
 * Interface PersonAddFileModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddFileModalFactory
{
    /**
     * @return PersonAddFileModal
     */
    public function create();
}