<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobSelectRepository.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 15:13
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Job;

use Rendix2\FamilyTree\App\Model\Interfaces\ISettingsSelectRepository;

/**
 * Class JobSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Job
 */
class JobSelectRepository implements ISettingsSelectRepository
{
    /**
     * @var JobCachedSelector $jobCachedSelector
     */
    private $jobCachedSelector;

    /**
     * @var JobSelector $jobSelector
     */
    private $jobSelector;

    /**
     * @var JobSettingsCachedSelector $jobSettingsCachedSelector
     */
    private $jobSettingsCachedSelector;

    /**
     * @var JobSettingsSelector $jobSettingsSelector
     */
    private $jobSettingsSelector;

    /**
     * JobSelectRepository constructor.
     *
     * @param JobCachedSelector         $jobCachedSelector
     * @param JobSelector               $jobSelector
     * @param JobSettingsCachedSelector $jobSettingsCachedSelector
     * @param JobSettingsSelector       $jobSettingsSelector
     */
    public function __construct(
        JobCachedSelector $jobCachedSelector,
        JobSelector $jobSelector,
        JobSettingsCachedSelector $jobSettingsCachedSelector,
        JobSettingsSelector $jobSettingsSelector
    ) {
        $this->jobCachedSelector = $jobCachedSelector;
        $this->jobSelector = $jobSelector;
        $this->jobSettingsCachedSelector = $jobSettingsCachedSelector;
        $this->jobSettingsSelector = $jobSettingsSelector;
    }

    /**
     * @return JobSelector
     */
    public function getManager()
    {
        return $this->jobSelector;
    }

    /**
     * @return JobCachedSelector
     */
    public function getCachedManager()
    {
        return $this->jobCachedSelector;
    }

    /**
     * @return JobSettingsSelector
     */
    public function getSettingsManager()
    {
        return $this->jobSettingsSelector;
    }

    /**
     * @return JobSettingsCachedSelector
     */
    public function getSettingsCachedManager()
    {
        return $this->jobSettingsCachedSelector;
    }
}
