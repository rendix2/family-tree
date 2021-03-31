<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddPersonJobModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 1:21
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\PersonJobSettings;
use Rendix2\FamilyTree\App\Facades\Person2JobFacade;


use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\JobSettingsManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class JobAddPersonJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job
 */
class JobAddPersonJobModal extends Control
{
    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var JobSettingsManager $jobSettingsManager
     */
    private $jobSettingsManager;

    /**
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

    /**
     * @var Person2JobForm $person2JobForm
     */
    private $person2JobForm;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * JobAddPersonJobModal constructor.
     * @param JobManager $jobManager
     * @param JobSettingsManager $jobSettingsManager
     * @param Person2JobFacade $person2JobFacade
     * @param Person2JobManager $person2JobManager
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param ITranslator $translator
     */
    public function __construct(
        JobManager $jobManager,
        JobSettingsManager $jobSettingsManager,
        Person2JobFacade $person2JobFacade,
        Person2JobForm $person2JobForm,
        Person2JobManager $person2JobManager,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->jobManager = $jobManager;
        $this->jobSettingsManager = $jobSettingsManager;
        $this->person2JobFacade = $person2JobFacade;
        $this->person2JobForm = $person2JobForm;
        $this->person2JobManager = $person2JobManager;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['jobAddPersonJobForm']->render();
    }

    /**
     * @param int $jobId
     *
     * @return void
     */
    public function handleJobAddPersonJob($jobId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Job:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairs($this->translator);
        $jobs = $this->jobSettingsManager->getAllPairs($this->translator);
        $jobsPersons = $this->person2JobManager->getPairsByRight($jobId);

        $this['jobAddPersonJobForm-personId']->setItems($persons)
            ->setDisabled($jobsPersons);

        $this['jobAddPersonJobForm-_jobId']->setDefaultValue($jobId);
        $this['jobAddPersonJobForm-jobId']->setItems($jobs)
            ->setDisabled()
            ->setDefaultValue($jobId);

        $presenter->template->modalName = 'jobAddPersonJob';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentJobAddPersonJobForm()
    {
        $personJobSettings = new PersonJobSettings();

        $form = $this->person2JobForm->create($personJobSettings);

        $form->addHidden('_jobId');

        $form->onValidate[] = [$this, 'jobAddPersonJobFormValidate'];
        $form->onSuccess[] = [$this, 'jobAddPersonJobFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function jobAddPersonJobFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->translator);

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->validate();

        $jobs = $this->jobManager->getAllPairs($this->translator);

        $jobHiddenControl = $form->getComponent('_jobId');

        $jobControl = $form->getComponent('jobId');
        $jobControl->setItems($jobs)
            ->setValue($jobHiddenControl->getValue())
            ->validate();

        $form->removeComponent($jobHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function jobAddPersonJobFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Job:edit', $presenter->getParameter('id'));
        }

        $this->person2JobManager->addGeneral($values);

        $persons = $this->person2JobFacade->getByRightCached($values->jobId);

        $presenter->template->persons = $persons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_job_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('persons');
    }
}
