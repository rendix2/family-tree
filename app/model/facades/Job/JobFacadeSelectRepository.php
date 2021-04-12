<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobFacadeSelectRepository.php
 * User: Tomáš Babický
 * Date: 09.04.2021
 * Time: 14:08
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Job;


use Rendix2\FamilyTree\App\Model\Interfaces\ISettingsSelectRepository;

/**
 * Class JobFacadeSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Job
 */
class JobFacadeSelectRepository implements ISettingsSelectRepository
{
    /**
     * @var JobFacadeCachedSelector $jobFacadeCachedSelector
     */
    private $jobFacadeCachedSelector;

    /**
     * @var JobFacadeSelector $jobFacadeSelector
     */
    private $jobFacadeSelector;

    /**
     * @var JobFacadeSettingsCachedSelector $jobFacadeSettingsCachedSelector
     */
    private $jobFacadeSettingsCachedSelector;

    /**
     * @var JobFacadeSettingsSelector $jobFacadeSettingsSelector
     */
    private $jobFacadeSettingsSelector;

    /**
     * JobFacadeSelectRepository constructor.
     *
     * @param JobFacadeCachedSelector $jobFacadeCachedSelector
     * @param JobFacadeSelector $jobFacadeSelector
     * @param JobFacadeSettingsCachedSelector $jobFacadeSettingsCachedSelector
     * @param JobFacadeSettingsSelector $jobFacadeSettingsSelector
     */
    public function __construct(
        JobFacadeCachedSelector $jobFacadeCachedSelector,
        JobFacadeSelector $jobFacadeSelector,
        JobFacadeSettingsCachedSelector $jobFacadeSettingsCachedSelector,
        JobFacadeSettingsSelector $jobFacadeSettingsSelector
    ) {
        $this->jobFacadeCachedSelector = $jobFacadeCachedSelector;
        $this->jobFacadeSelector = $jobFacadeSelector;
        $this->jobFacadeSettingsCachedSelector = $jobFacadeSettingsCachedSelector;
        $this->jobFacadeSettingsSelector = $jobFacadeSettingsSelector;
    }

    /**
     * @return JobFacadeSelector
     */
    public function getManager()
    {
        return $this->jobFacadeSelector;
    }

    /**
     * @return JobFacadeCachedSelector
     */
    public function getCachedManager()
    {
        return $this->jobFacadeCachedSelector;
    }

    /**
     * @return JobFacadeSettingsSelector
     */
    public function getSettingsManager()
    {
        return $this->jobFacadeSettingsSelector;
    }

    /**
     * @return JobFacadeSettingsCachedSelector
     */
    public function getSettingsCachedManager()
    {
        return $this->jobFacadeSettingsCachedSelector;
    }
}