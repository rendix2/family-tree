<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Job\JobDeleteJobFromListModal;

/**
 * Interface JobDeleteJobFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job\Factory
 */
interface JobDeleteJobFromListModalFactory
{
    /**
     * @return JobDeleteJobFromListModal
     */
    public function create();
}
