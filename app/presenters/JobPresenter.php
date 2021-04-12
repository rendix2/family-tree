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
use Rendix2\FamilyTree\App\Controls\Forms\Helpers\FormJsonDataParser;
use Rendix2\FamilyTree\App\Controls\Forms\JobForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\JobSettings;
use Rendix2\FamilyTree\App\Controls\Modals\Job\Container\JobModalContainer;
use Rendix2\FamilyTree\App\Controls\Modals\Job\JobAddAddressModal;
use Rendix2\FamilyTree\App\Controls\Modals\Job\JobAddPersonJobModal;
use Rendix2\FamilyTree\App\Controls\Modals\Job\JobAddTownModal;
use Rendix2\FamilyTree\App\Controls\Modals\Job\JobDeleteJobFromEditModal;
use Rendix2\FamilyTree\App\Controls\Modals\Job\JobDeleteJobFromListModal;
use Rendix2\FamilyTree\App\Controls\Modals\Job\JobDeletePersonJobModal;
use Rendix2\FamilyTree\App\Model\Facades\Person2JobFacade;
use Rendix2\FamilyTree\App\Model\Managers\JobManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;

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
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * JobPresenter constructor.
     *
     * @param AddressFacade     $addressFacade
     * @param JobFacade         $jobFacade
     * @param JobForm           $jobForm
     * @param JobManager        $jobManager
     * @param JobModalContainer $jobModalContainer
     * @param Person2JobFacade  $person2JobFacade
     * @param TownManager       $townManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        JobFacade $jobFacade,
        JobForm $jobForm,
        JobManager $jobManager,
        JobModalContainer $jobModalContainer,
        Person2JobFacade $person2JobFacade,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->jobForm = $jobForm;

        $this->jobModalContainer = $jobModalContainer;

        $this->addressFacade = $addressFacade;
        $this->jobFacade = $jobFacade;
        $this->person2JobFacade = $person2JobFacade;

        $this->jobManager = $jobManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $jobs = $this->jobFacade->select()->getSettingsCachedManager()->getAll();

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

        $towns = $this->townManager->select()->getCachedManager()->getPairs('name');

        if ($townId) {
            $this['jobForm-townId']->setItems($towns)
                ->setDefaultValue($townId);

            $addresses = $this->addressFacade->select()->getManager()->getByTownPairs($townId);

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
        $towns = $this->townManager->select()->getCachedManager()->getAllPairs();
        $addresses = $this->addressFacade->select()->getCachedManager()->getALlPairs();

        $this['jobForm-townId']->setItems($towns);
        $this['jobForm-addressId']->setItems($addresses);

        if ($id !== null) {
            $job = $this->jobFacade->select()->getCachedManager()->getByPrimaryKey($id);

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
            $persons = $this->person2JobFacade->select()->getCachedManager()->getByRightKey($id);
            $job = $this->jobFacade->select()->getCachedManager()->getByPrimaryKey($id);
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
            $this->jobManager->update()->updateByPrimaryKey($id, $values);

            $this->flashMessage('job_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->jobManager->insert()->insert((array) $values);

            $this->flashMessage('job_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Job:edit', $id);
    }

    /**
     * @return JobAddAddressModal
     */
    public function createComponentJobAddAddressModal()
    {
        return $this->jobModalContainer->getJobAddAddressModalFactory()->create();
    }

    /**
     * @return JobAddTownModal
     */
    public function createComponentJobAddTownModal()
    {
        return $this->jobModalContainer->getJobAddTownModalFactory()->create();
    }

    /**
     * @return JobDeleteJobFromListModal
     */
    public function createComponentJobDeleteJobFromListModal()
    {
        return $this->jobModalContainer->getJobDeleteJobFromListModalFactory()->create();
    }

    /**
     * @return JobDeleteJobFromEditModal
     */
    public function createComponentJobDeleteJobFromEditModal()
    {
        return $this->jobModalContainer->getJobDeleteJobFromEditModalFactory()->create();
    }

    /**
     * @return JobAddPersonJobModal
     */
    public function createComponentJobAddPersonJobModal()
    {
        return $this->jobModalContainer->getJobAddPersonJobModalFactory()->create();
    }

    /**
     * @return JobDeletePersonJobModal
     */
    public function createComponentJobDeletePersonJobModal()
    {
        return $this->jobModalContainer->getJobDeletePersonJobModalFactory()->create();
    }
}
