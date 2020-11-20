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

use Dibi\Row;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\Person2JobFacade;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\JobForm;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobPersonDeleteModal;

/**
 * Class JobPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class JobPresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    use JobPersonDeleteModal;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobManager $manager
     */
    private $manager;

    /**
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var Row|false $job
     */
    private $job;

    /**
     * JobPresenter constructor.
     * @param AddressFacade $addressFacade
     * @param JobManager $jobManager
     * @param JobFacade $jobFacade
     * @param Person2JobFacade $person2JobFacade
     * @param Person2JobManager $person2JobManager
     * @param PersonManager $personManager
     * @param TownManager $townManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        JobManager $jobManager,
        JobFacade $jobFacade,
        Person2JobFacade $person2JobFacade,
        Person2JobManager $person2JobManager,
        PersonManager $personManager,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->manager = $jobManager;
        $this->jobFacade = $jobFacade;
        $this->person2JobFacade = $person2JobFacade;
        $this->person2JobManager = $person2JobManager;
        $this->personManager = $personManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $jobs = $this->jobFacade->getAllCached();

        $this->template->jobs = $jobs;

        $this->template->addFilter('job', new JobFilter());
    }

    /**
     * @param int|null $id jobId
     */
    public function actionEdit($id = null)
    {
        $towns = $this->townManager->getAllPairsCached();
        $addresses = $this->addressFacade->getPairsCached();

        $this['form-townId']->setItems($towns);
        $this['form-addressId']->setItems($addresses);

        if ($id !== null) {
            $job = $this->jobFacade->getByPrimaryKeyCached($id);

            if (!$job) {
                $this->error('Item not found.');
            }

            $this['form-townId']->setDefaultValue($job->town->id);
            $this['form-addressId']->setDefaultValue($job->address->id);

            $this['form']->setDefaults((array)$job);
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

        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
    }

    /**
     * @param int $id jobId
     */
    public function actionPerson($id)
    {
        $job = $this->manager->getByPrimaryKey($id);

        if (!$job) {
            $this->error('Item not found.');
        }

        $this->job = $job;

        $jobFilter = new JobFilter();

        $persons = $this->personManager->getAllPairs($this->getTranslator());

        $this['personForm-jobId']->setItems([$id => $jobFilter($job)])
            ->setDisabled()
            ->setDefaultValue($id);

        $this['personForm-personId']->setItems($persons);
    }

    /**
     * @param int $id jobId
     */
    public function renderPerson($id)
    {
        $this->template->job = $this->job;

        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }
    
    /**
     * @param int $id jobId
     */
    public function actionPersons($id)
    {
        $job = $this->manager->getByPrimaryKey($id);

        if (!$job) {
            $this->error('Item not found.');
        }
    }

    /**
     * @param int $id jobId
     */
    public function renderPersons($id)
    {
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $formFactory = new JobForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    /**
     * @return Form
     */
    public function createComponentPersonForm()
    {
        $formFactory = new Person2JobForm($this->getTranslator());

        $form = $formFactory->create();

        $form->onSuccess[] = [$this, 'savePersonForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function savePersonForm(Form $form, ArrayHash $values)
    {
        $jobID = $this->getParameter('id');

        $values->jobId = $jobID;
        $id = $this->person2JobManager->addGeneral((array)$values);
        $this->flashMessage('item_added', self::FLASH_SUCCESS);
        $this->redirect(':edit', $jobID);
    }
}
