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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\PersonJobSettings;
use Rendix2\FamilyTree\App\Model\Facades\Person2JobFacade;
use Rendix2\FamilyTree\App\Model\Managers\JobManager;
use Rendix2\FamilyTree\App\Model\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
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
     * JobAddPersonJobModal constructor.
     *
     * @param JobManager        $jobManager
     * @param Person2JobFacade  $person2JobFacade
     * @param Person2JobForm    $person2JobForm
     * @param Person2JobManager $person2JobContainer
     * @param PersonManager     $personManager
     */
    public function __construct(
        JobManager $jobManager,
        Person2JobFacade $person2JobFacade,
        Person2JobForm $person2JobForm,
        Person2JobManager $person2JobContainer,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->jobManager = $jobManager;
        $this->person2JobFacade = $person2JobFacade;
        $this->person2JobForm = $person2JobForm;
        $this->person2JobManager = $person2JobContainer;
        $this->personManager = $personManager;
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

        $persons = $this->personManager->select()->getSettingsManager()->getAllPairs();
        $jobs = $this->jobManager->select()->getSettingsManager()->getAllPairs();
        $jobsPersons = $this->person2JobManager->select()->getManager()->getPairsByRight($jobId);

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
        $persons = $this->personManager->select()->getManager()->getAllPairs();

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->validate();

        $jobs = $this->jobManager->select()->getManager()->getAllPairs();

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

        $this->person2JobManager->insert()->insert((array) $values);

        $persons = $this->person2JobFacade->select()->getCachedManager()->getByRightKey($values->jobId);

        $presenter->template->persons = $persons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_job_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('persons');
    }
}
