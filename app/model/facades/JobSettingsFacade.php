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
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var JobSettingsManager $jobSettingsManager
     */
    private $jobSettingsManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * JobSettingsFacade constructor.
     *
     * @param AddressFacade $addressFacade
     * @param IStorage $storage
     * @param IRequest $request
     * @param JobSettingsManager $jobSettingsManager
     * @param TownFacade $townFacade
     */
    public function __construct(
        AddressFacade $addressFacade,
        IStorage $storage,
        IRequest $request,
        JobSettingsManager $jobSettingsManager,
        TownFacade $townFacade
    ) {
        parent::__construct($addressFacade, $storage, $request, $jobSettingsManager, $townFacade);

        $this->addressFacade = $addressFacade;
        $this->jobSettingsManager = $jobSettingsManager;
        $this->townFacade = $townFacade;
    }

    /**
     * @return JobEntity[]
     */
    public function getAll()
    {
        $jobs = $this->jobSettingsManager->getAll();

        $addressId = $this->getIds($jobs, '_addressId');
        $townId = $this->getIds($jobs, '_townId');

        $towns = $this->townFacade->getByPrimaryKeys($townId);
        $addresses = $this->addressFacade->getByPrimaryKeys($addressId);

        return $this->join($jobs, $towns, $addresses);
    }
}
