<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Job\JobDeleteJobFromEditModal;

/**
 * Interface JobDeleteJobFromEditModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job\Factory
 */
interface JobDeleteJobFromEditModalFactory
{
    /**
     * @return JobDeleteJobFromEditModal
     */
    public function create();
}
