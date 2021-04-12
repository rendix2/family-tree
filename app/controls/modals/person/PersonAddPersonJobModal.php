<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPersonJobModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:03
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

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
 * Class PersonAddPersonJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddPersonJobModal extends Control
{
    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

    /**
     * @var Person2JobForm $person2JobForm
     */
    private $person2JobForm;

    /**
     * PersonAddPersonJobModal constructor.
     *
     * @param JobManager        $jobManager
     * @param Person2JobManager $person2JobManager
     * @param PersonManager     $personManager
     * @param Person2JobFacade  $person2JobFacadeCached
     * @param Person2JobForm    $person2JobForm
     */
    public function __construct(
        JobManager $jobManager,
        Person2JobManager $person2JobManager,
        PersonManager $personManager,
        Person2JobFacade $person2JobFacadeCached,
        Person2JobForm $person2JobForm
    ) {
        parent::__construct();

        $this->jobManager = $jobManager;
        $this->person2JobManager = $person2JobManager;
        $this->personManager = $personManager;
        $this->person2JobFacade = $person2JobFacadeCached;
        $this->person2JobForm = $person2JobForm;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddPersonJobForm']->render();
    }

    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPersonJob($personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personManager->select()->getSettingsManager()->getAllPairs();
        $jobs = $this->jobManager->select()->getSettingsManager()->getAllPairs();
        $personsJobs = $this->person2JobManager->select()->getManager()->getPairsByLeft($personId);

        $this['personAddPersonJobForm-_personId']->setDefaultValue($personId);
        $this['personAddPersonJobForm-personId']->setDisabled()
            ->setItems($persons)
            ->setDefaultValue($personId);

        $this['personAddPersonJobForm-jobId']->setItems($jobs)
            ->setDisabled($personsJobs);

        $presenter->template->modalName = 'personAddPersonJob';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPersonJobForm()
    {
        $personJobSettings = new PersonJobSettings();

        $form = $this->person2JobForm->create($personJobSettings);

        $form->addHidden('_personId');

        $form->onAnchor[] = [$this, 'personAddPersonJobFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPersonJobFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPersonJobFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPersonJobFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPersonJobFormValidate(Form $form)
    {
        $persons = $this->personManager->select()->getManager()->getAllPairs();

        $personHiddenControl = $form->getComponent('_personId');

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->setValue($personHiddenControl->getValue())
            ->validate();

        $jobs = $this->jobManager->select()->getManager()->getAllPairs();

        $jobControl = $form->getComponent('jobId');
        $jobControl->setItems($jobs)
            ->validate();

        $form->removeComponent($personHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddPersonJobFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->person2JobManager->insert()->insert((array) $values);

        $jobs = $this->person2JobFacade->select()->getCachedManager()->getByLeftKey($values->personId);

        $presenter->template->jobs = $jobs;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_job_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('jobs');
        $presenter->redrawControl('flashes');
    }
}
