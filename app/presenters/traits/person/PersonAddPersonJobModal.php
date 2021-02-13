<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonPersonAddPersonJobModal.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 0:51
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Forms\Settings\PersonJobSettings;

/**
 * Trait PersonAddPersonJobModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddPersonJobModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPersonJob($personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairs($this->getTranslator());
        $jobs = $this->jobSettingsManager->getAllPairs();
        $personsJobs = $this->person2JobManager->getPairsByLeft($personId);

        $this['personAddPersonJobForm-_personId']->setDefaultValue($personId);
        $this['personAddPersonJobForm-personId']->setDisabled()
            ->setItems($persons)
            ->setDefaultValue($personId);

        $this['personAddPersonJobForm-jobId']->setItems($jobs)
            ->setDisabled($personsJobs);

        $this->template->modalName = 'personAddPersonJob';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPersonJobForm()
    {
        $personJobSettings = new PersonJobSettings();

        $formFactory = new Person2JobForm($this->getTranslator(), $personJobSettings);

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
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPersonJobFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());

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
        $this->person2JobManager->addGeneral($values);

        $jobs = $this->person2JobFacade->getByLeftCached($values->personId);

        $this->template->jobs = $jobs;

        $this->payload->showModal = false;

        $this->flashMessage('person_job_added', self::FLASH_SUCCESS);

        $this->redrawControl('jobs');
        $this->redrawControl('flashes');
    }
}