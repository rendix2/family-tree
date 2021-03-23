<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobModalContainer.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 16:14
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job\Container;

use Rendix2\FamilyTree\App\Controls\Modals\Job\Factory\JobAddAddressModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Job\Factory\JobAddPersonJobModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Job\Factory\JobAddTownModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Job\Factory\JobDeleteJobFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Job\Factory\JobDeleteJobFromListModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Job\Factory\JobDeletePersonJobModalFactory;

/**
 * Class JobModalContainer
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job\Container
 */
class JobModalContainer
{
    /**
     * @var JobAddAddressModalFactory $jobAddAddressModalFactory
     */
    private $jobAddAddressModalFactory;

    /**
     * @var JobAddPersonJobModalFactory $jobAddPersonJobModalFactory
     */
    private $jobAddPersonJobModalFactory;

    /**
     * @var JobAddTownModalFactory $jobAddTownModalFactory
     */
    private $jobAddTownModalFactory;

    /**
     * @var JobDeleteJobFromEditModalFactory $jobDeleteJobFromEditModalFactory
     */
    private $jobDeleteJobFromEditModalFactory;

    /**
     * @var JobDeleteJobFromListModalFactory $jobDeleteJobFromListModalFactory
     */
    private $jobDeleteJobFromListModalFactory;

    /**
     * @var JobDeletePersonJobModalFactory $jobDeletePersonJobModalFactory
     */
    private $jobDeletePersonJobModalFactory;

    /**
     * JobModalContainer constructor.
     * @param JobAddAddressModalFactory $jobAddAddressModalFactory
     * @param JobAddPersonJobModalFactory $jobAddPersonJobModalFactory
     * @param JobAddTownModalFactory $jobAddTownModalFactory
     * @param JobDeleteJobFromEditModalFactory $jobDeleteJobFromEditModalFactory
     * @param JobDeleteJobFromListModalFactory $jobDeleteJobFromListModalFactory
     * @param JobDeletePersonJobModalFactory $jobDeletePersonJobModalFactory
     */
    public function __construct(
        JobAddAddressModalFactory $jobAddAddressModalFactory,
        JobAddPersonJobModalFactory $jobAddPersonJobModalFactory,
        JobAddTownModalFactory $jobAddTownModalFactory,
        JobDeleteJobFromEditModalFactory $jobDeleteJobFromEditModalFactory,
        JobDeleteJobFromListModalFactory $jobDeleteJobFromListModalFactory,
        JobDeletePersonJobModalFactory $jobDeletePersonJobModalFactory
    ) {
        $this->jobAddAddressModalFactory = $jobAddAddressModalFactory;
        $this->jobAddPersonJobModalFactory = $jobAddPersonJobModalFactory;
        $this->jobAddTownModalFactory = $jobAddTownModalFactory;
        $this->jobDeleteJobFromEditModalFactory = $jobDeleteJobFromEditModalFactory;
        $this->jobDeleteJobFromListModalFactory = $jobDeleteJobFromListModalFactory;
        $this->jobDeletePersonJobModalFactory = $jobDeletePersonJobModalFactory;
    }

    /**
     * @return JobAddAddressModalFactory
     */
    public function getJobAddAddressModalFactory()
    {
        return $this->jobAddAddressModalFactory;
    }

    /**
     * @return JobAddPersonJobModalFactory
     */
    public function getJobAddPersonJobModalFactory()
    {
        return $this->jobAddPersonJobModalFactory;
    }

    /**
     * @return JobAddTownModalFactory
     */
    public function getJobAddTownModalFactory()
    {
        return $this->jobAddTownModalFactory;
    }

    /**
     * @return JobDeleteJobFromEditModalFactory
     */
    public function getJobDeleteJobFromEditModalFactory()
    {
        return $this->jobDeleteJobFromEditModalFactory;
    }

    /**
     * @return JobDeleteJobFromListModalFactory
     */
    public function getJobDeleteJobFromListModalFactory()
    {
        return $this->jobDeleteJobFromListModalFactory;
    }

    /**
     * @return JobDeletePersonJobModalFactory
     */
    public function getJobDeletePersonJobModalFactory()
    {
        return $this->jobDeletePersonJobModalFactory;
    }
}
