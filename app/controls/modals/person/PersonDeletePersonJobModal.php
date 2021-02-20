<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeletePersonJobModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:13
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\Person2JobFacade;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonDeletePersonJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeletePersonJobModal extends Control
{

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var JobFilter $jobFilter
     */
    private $jobFilter;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * PersonDeletePersonJobModal constructor.
     *
     * @param ITranslator $translator
     * @param Person2JobFacade $person2JobFacade
     * @param Person2JobManager $person2JobManager
     * @param JobManager $jobManager
     * @param PersonFacade $personFacade
     * @param JobFilter $jobFilter
     * @param PersonFilter $personFilter
     */
    public function __construct(
        ITranslator $translator,
        Person2JobFacade $person2JobFacade,
        Person2JobManager $person2JobManager,
        JobManager $jobManager,
        PersonFacade $personFacade,
        JobFilter $jobFilter,
        PersonFilter $personFilter
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->person2JobFacade = $person2JobFacade;
        $this->person2JobManager = $person2JobManager;
        $this->jobManager = $jobManager;
        $this->personFacade = $personFacade;
        $this->jobFilter = $jobFilter;
        $this->personFilter = $personFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeletePersonJobForm']->render();
    }

    /**
     * @param int $personId
     * @param int $jobId
     */
    public function handlePersonDeletePersonJob($personId, $jobId)
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $this['personDeletePersonJobForm']->setDefaults(
            [
                'personId' => $personId,
                'jobId' => $jobId
            ]
        );

        $personFilter = $this->personFilter;
        $jobFilter = $this->jobFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
        $jobModalItem = $this->jobManager->getByPrimaryKeyCached($jobId);

        $this->presenter->template->modalName = 'personDeletePersonJob';
        $this->presenter->template->jobModalItem = $jobFilter($jobModalItem);
        $this->presenter->template->personModalItem = $personFilter($personModalItem);

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeletePersonJobForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeletePersonJobFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeletePersonJobFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->presenter->isAjax()) {
            $this->person2JobManager->deleteByLeftIdAndRightId($values->personId, $values->jobId);

            $jobs = $this->person2JobFacade->getByLeft($values->personId);

            $this->presenter->template->jobs = $jobs;

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('person_job_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('jobs');
        } else {
            $this->presenter->redirect('Person:edit', $values->personId);
        }
    }
}