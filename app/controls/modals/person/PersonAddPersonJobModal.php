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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\Person2JobFacade;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Forms\Settings\PersonJobSettings;
use Rendix2\FamilyTree\App\Managers\JobSettingsManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddPersonJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddPersonJobModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var JobSettingsManager $jobSettingsManager
     */
    private $jobSettingsManager;

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
     * PersonAddPersonJobModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonSettingsManager $personSettingsManager
     * @param JobSettingsManager $jobSettingsManager
     * @param Person2JobManager $person2JobManager
     * @param PersonManager $personManager
     * @param Person2JobFacade $person2JobFacade
     */
    public function __construct(
        ITranslator $translator,
        PersonSettingsManager $personSettingsManager,
        JobSettingsManager $jobSettingsManager,
        Person2JobManager $person2JobManager,
        PersonManager $personManager,
        Person2JobFacade $person2JobFacade
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personSettingsManager = $personSettingsManager;
        $this->jobSettingsManager = $jobSettingsManager;
        $this->person2JobManager = $person2JobManager;
        $this->personManager = $personManager;
        $this->person2JobFacade = $person2JobFacade;
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

        $persons = $this->personSettingsManager->getAllPairs($this->translator);
        $jobs = $this->jobSettingsManager->getAllPairs();
        $personsJobs = $this->person2JobManager->getPairsByLeft($personId);

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

        $formFactory = new Person2JobForm($this->translator, $personJobSettings);

        $form = $formFactory->create();
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
        $persons = $this->personManager->getAllPairs($this->translator);

        $personHiddenControl = $form->getComponent('_personId');

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->setValue($personHiddenControl->getValue())
            ->validate();

        $jobs = $this->jobSettingsManager->getAllPairs();

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

        $this->person2JobManager->addGeneral($values);

        $jobs = $this->person2JobFacade->getByLeftCached($values->personId);

        $presenter->template->jobs = $jobs;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_job_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('jobs');
        $presenter->redrawControl('flashes');
    }
}
