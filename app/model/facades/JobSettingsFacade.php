<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobSettingsFacade.php
 * User: Tomáš Babický
 * Date: 11.02.2021
 * Time: 18:21
 */

namespace Rendix2\FamilyTree\App\Model\Facades;

use Nette\Caching\IStorage;
use Nette\Http\IRequest;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\JobSettingsManager;
use Rendix2\FamilyTree\App\Model\Entities\JobEntity;

/**
 * Class JobSettingsFacade
 *
 * @package Rendix2\FamilyTree\App\Model\Facades
 */
class JobSettingsFacade extends JobFacade
{
    /**
     * JobSettingsFacade constructor.
     *
     * @param AddressFacade $addressFacade
     * @param IStorage $storage
     * @param IRequest $request
     * @param JobSettingsManager $jobSettingsManager
     * @param JobFilter $jobFilter
     * @param TownFacade $townFacade
     */
    public function __construct(
        AddressFacade $addressFacade,
        IStorage $storage,
        IRequest $request,
        JobSettingsManager $jobSettingsManager,
        JobFilter $jobFilter,
        TownFacade $townFacade
    ){
        parent::__construct($addressFacade, $storage, $request, $jobSettingsManager, $jobFilter, $townFacade);
    }
}
