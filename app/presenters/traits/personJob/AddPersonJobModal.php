<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddPersonJobModal.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 0:40
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\PersonJob;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Forms\PersonJobForm;

/**
 * Trait AddPersonJobModal
 * 
 * @package Rendix2\FamilyTree\App\Presenters\Traits\PersonJob
 */
trait AddPersonJobModal
{
    public function handleAddPersonJob()
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());
        $jobs = $this->jobFacade->getAllPairs();
        
        $this['addPersonJobForm-personId']->setItems($persons);
        $this['addPersonJobForm-job']->setItems($jobs);

        $this->template->modalName = 'addPersonJob';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }
    
    public function createAddPersonJobForm()
    {
        $formFactory = new Person2JobForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addPersonJobFormAnchor'];
        $form->onValidate[] = [$this, 'addPersonJobFormValidate'];
        $form->onSuccess[] = [$this, 'savePersonJobForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addPersonJobFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addPersonJobFormValidate(Form $form)
    {
        $persons = $this->personFacade->getAllPairs();
        
        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons);
        $personControl->validate();

        $jobs = $this->jobFacade->getAllPairs();

        $jobControl = $form->getComponent('jobId');
        $jobControl->setItems($jobs);
        $jobControl->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function savePersonJobForm(Form $form, ArrayHash $values)
    {
        $this->personJobManager->add($values);

        $this->flashMessage('person_job_added_person_job', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }    
}
