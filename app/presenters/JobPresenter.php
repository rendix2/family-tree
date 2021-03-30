<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 22:29
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\JobForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\JobSettings;
use Rendix2\FamilyTree\App\Controls\Modals\Job\Container\JobModalContainer;
use Rendix2\FamilyTree\App\Facades\Person2JobFacade;



use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobSettingsFacade;

/**
 * Class JobPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class JobPresenter extends BasePresenter
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobForm $jobForm
     */
    private $jobForm;

    /**
     * @var JobModalContainer $jobModalContainer
     */
    private $jobModalContainer;

    /**
     * @var JobSettingsFacade $jobSettingsFacade
     */
    private $jobSettingsFacade;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * JobPresenter constructor.
     *
     * @param AddressFacade $addressFacade
     * @param JobFacade $jobFacade
     * @param JobSettingsFacade $jobSettingsFacade
     * @param JobManager $jobManager
     * @param JobModalContainer $jobModalContainer
     * @param Person2JobFacade $person2JobFacade
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        JobFacade $jobFacade,
        JobForm $jobForm,
        JobSettingsFacade $jobSettingsFacade,
        JobManager $jobManager,
        JobModalContainer $jobModalContainer,
        Person2JobFacade $person2JobFacade,
        TownSettingsManager $townSettingsManager
    ) {
        parent::__construct();

        $this->jobForm = $jobForm;

        $this->jobModalContainer = $jobModalContainer;

        $this->addressFacade = $addressFacade;
        $this->jobFacade = $jobFacade;
        $this->person2JobFacade = $person2JobFacade;

        $this->jobSettingsFacade = $jobSettingsFacade;

        $this->jobManager = $jobManager;

        $this->townSettingsManager = $townSettingsManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $jobs = $this->jobSettingsFacade->getAllCached();

        $this->template->jobs = $jobs;
    }

    /**
     * @param int $townId
     * @param string $formData
     *
     * @return void
     */
    public function handleJobFormSelectAddress($townId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('Job:edit', $this->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['townId'], $formDataParsed['addressId']);

        $towns = $this->townSettingsManager->getPairsCached('name');

        if ($townId) {
            $this['jobForm-townId']->setItems($towns)
                ->setDefaultValue($townId);

            $addresses = $this->addressFacade->getByTownPairs($townId);

            $this['jobForm-addressId']->setItems($addresses);
        } else {
            $this['jobForm-townId']->setItems($towns)
                ->setDefaultValue(null);

            $this['jobForm-addressId']->setItems([]);
        }

        $this['jobForm']->setDefaults($formDataParsed);

        $this->payload->snippets = [
            $this['jobForm-addressId']->getHtmlId() => (string) $this['jobForm-addressId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }


    /**
     * @param int|null $id jobId
     */
    public function actionEdit($id = null)
    {
        $towns = $this->townSettingsManager->getAllPairsCached();
        $addresses = $this->addressFacade->getPairsCached();

        $this['jobForm-townId']->setItems($towns);
        $this['jobForm-addressId']->setItems($addresses);

        if ($id !== null) {
            $job = $this->jobFacade->getByPrimaryKeyCached($id);

            if (!$job) {
                $this->error('Item not found.');
            }

            if ($job->town) {
                $this['jobForm-townId']->setDefaultValue($job->town->id);
            }

            if ($job->address) {
                $this['jobForm-addressId']->setDefaultValue($job->address->id);
            }

            $this['jobForm']->setDefaults((array) $job);
        }
    }

    /**
     * @param int|null $id jobId
     */
    public function renderEdit($id)
    {
        if ($id === null) {
            $persons = [];
            $job = null;
        } else {
            $persons = $this->person2JobFacade->getByRightCached($id);
            $job = $this->jobFacade->getByPrimaryKeyCached($id);
        }

        $this->template->persons = $persons;
        $this->template->job = $job;
    }

    /**
     * @return Form
     */
    public function createComponentJobForm()
    {
        $jobSettings = new JobSettings();
        $jobSettings->selectTownHandle = $this->link('jobFormSelectAddress!');

        $form = $this->jobForm->create($jobSettings);

        $form->onSuccess[] = [$this, 'jobFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function jobFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->jobManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('job_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->jobManager->add($values);

            $this->flashMessage('job_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Job:edit', $id);
    }

    public function createComponentJobAddAddressModal()
    {
        return $this->jobModalContainer->getJobAddAddressModalFactory()->create();
    }

    public function createComponentJobAddTownModal()
    {
        return $this->jobModalContainer->getJobAddTownModalFactory()->create();
    }

    public function createComponentJobDeleteJobFromListModal()
    {
        return $this->jobModalContainer->getJobDeleteJobFromListModalFactory()->create();
    }

    public function createComponentJobDeleteJobFromEditModal()
    {
        return $this->jobModalContainer->getJobDeleteJobFromEditModalFactory()->create();
    }

    public function createComponentJobAddPersonJobModal()
    {
        return $this->jobModalContainer->getJobAddPersonJobModalFactory()->create();
    }

    public function createComponentJobDeletePersonJobModal()
    {
        return $this->jobModalContainer->getJobDeletePersonJobModalFactory()->create();
    }

}
