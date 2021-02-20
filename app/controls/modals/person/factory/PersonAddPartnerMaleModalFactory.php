<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPartnerMaleModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:01
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPartnerMaleModal;

/**
 * Interface PersonAddPartnerMaleModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factory
 */
interface PersonAddPartnerMaleModalFactory
{
    /**
     * @return PersonAddPartnerMaleModal
     */
    public function create();
}