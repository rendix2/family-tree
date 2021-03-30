<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddParentPartnerFemaleModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:58
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddParentPartnerFemaleModal;

/**
 * Interface PersonAddParentPartnerFemaleModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddParentPartnerFemaleModalFactory
{
    /**
     * @return PersonAddParentPartnerFemaleModal
     */
    public function create();
}