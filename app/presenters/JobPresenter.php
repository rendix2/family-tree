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
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\DateFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
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
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var JobManager $manager
     */
    private $manager;

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
     * @param AddressManager $addressManager
     * @param JobManager $jobManager
     * @param Person2JobManager $person2JobManager
     * @param PersonManager $personManager
     * @param TownManager $townManager
     */
    public function __construct(
        AddressManager $addressManager,
        JobManager $jobManager,
        Person2JobManager $person2JobManager,
        PersonManager $personManager,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->addressManager = $addressManager;
        $this->manager = $jobManager;
        $this->person2JobManager = $person2JobManager;
        $this->personManager = $personManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $jobs = $this->manager->getAll();

        $this->template->jobs = $jobs;

        $this->template->addFilter('job', new JobFilter());
    }

    /**
     * @param int|null $id $jobId
     */
    public function actionEdit($id = null)
    {
        $towns = $this->townManager->getAllPairs();
        $addresses = $this->addressManager->getAllPairs();

        $this['form-townId']->setItems($towns);
        $this['form-addressId']->setItems($addresses);

        $this->traitActionEdit($id);
    }

    /**
     * @param int|null $id $jobId
     */
    public function renderEdit($id)
    {
        if ($id === null) {
            $persons = [];
        } else {
            $persons = $this->person2JobManager->getAllByRightJoined($id);
        }

        $this->template->persons = $persons;
        $this->template->job = $this->item;

        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
        $this->template->addFilter('dateFT', new DateFilter($this->getTranslator()));
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
            ->setValue($id);

        $this['personForm-personId']->setItems($persons);
    }

    /**
     * @param int $id jobId
     */
    public function renderPerson($id)
    {
        $this->template->job = $this->job;

        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
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
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addText('company', 'job_company');
        $form->addText('position', 'job_position');

        $form->addSelect('townId', $this->getTranslator()->translate('job_town'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('job_select_town'));

        $form->addSelect('addressId', $this->getTranslator()->translate('job_address'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('job_select_address'));

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

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
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

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
