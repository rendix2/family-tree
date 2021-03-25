<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddAddressModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 0:46
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Job\JobAddAddressModal;

/**
 * Interface JobAddAddressModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job\Factory
 */
interface JobAddAddressModalFactory
{
    /**
     * @return JobAddAddressModal
     */
    public function create();
}
