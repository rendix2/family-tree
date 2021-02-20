<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPersonSourceModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:04
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPersonSourceModal;

/**
 * Interface PersonAddPersonSourceModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddPersonSourceModalFactory
{
    /**
     * @return PersonAddPersonSourceModal
     */
    public function create();
}
