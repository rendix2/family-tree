<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobSettingsCachedSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 15:16
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Job;


use Nette\Caching\IStorage;

class JobSettingsCachedSelector extends JobCachedSelector
{
    /**
     * JobSettingsCachedSelector constructor.
     *
     * @param IStorage            $storage
     * @param JobSettingsSelector $selector
     */
    public function __construct(
        IStorage $storage,
        JobSettingsSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }
}
