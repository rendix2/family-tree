<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobFacadeSettingsCachedSelector.php
 * User: Tomáš Babický
 * Date: 09.04.2021
 * Time: 14:07
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Job;

use Nette\Caching\IStorage;

/**
 * Class JobFacadeSettingsCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Job
 */
class JobFacadeSettingsCachedSelector extends JobFacadeCachedSelector
{
    /**
     * JobFacadeSettingsCachedSelector constructor.
     *
     * @param IStorage                  $storage
     * @param JobFacadeSettingsSelector $selector
     */
    public function __construct(
        IStorage $storage,
        JobFacadeSettingsSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }
}
