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
use Rendix2\FamilyTree\App\Forms\PersonJobForm;

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
        $persons = $this->personManager->getAllPairs($this->getTranslator());
        $jobs = $this->jobManager->getAllPairs();

        $this['personAddPersonJobForm-_personId']->setDefaultValue($personId);
        $this['personAddPersonJobForm-personId']->setDisabled()->setItems($persons)->setDefaultValue($personId);
        $this['personAddPersonJobForm-jobId']->setItems($jobs);

        $this->template->modalName = 'personAddPersonJob';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentPersonAddPersonJobForm()
    {
        $formFactory = new Person2JobForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_personId');
        $form->onAnchor[] = [$this, 'personAddPersonJobFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPersonJobFormValidate'];
        $form->onSuccess[] = [$this, 'personSavePersonJobForm'];

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

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons);
        $personControl->setValue($form->getComponent('_personId')->getValue());
        $personControl->validate();

        $form->removeComponent($form->getComponent('_personId'));

        $jobs = $this->jobManager->getAllPairs();

        $jobControl = $form->getComponent('jobId');
        $jobControl->setItems($jobs);
        $jobControl->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personSavePersonJobForm(Form $form, ArrayHash $values)
    {
        $this->person2JobManager->addGeneral($values);

        $this->flashMessage('person_job_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }
}