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
    public function handlePersonJobAddPersonJob()
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());
        $jobs = $this->jobFacade->getAllPairs();
        
        $this['personJobAddPersonJobForm-personId']->setItems($persons);
        $this['personJobAddPersonJobForm-jobId']->setItems($jobs);

        $this->template->modalName = 'personJobAddPersonJob';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createPersonJobAddPersonJobForm()
    {
        $formFactory = new Person2JobForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'personJobAddPersonJobFormAnchor'];
        $form->onValidate[] = [$this, 'personJobAddPersonJobFormValidate'];
        $form->onSuccess[] = [$this, 'personJobAddPersonJobFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personJobAddPersonJobFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personJobAddPersonJobFormValidate(Form $form)
    {
        $persons = $this->personFacade->getAllPairs();

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->validate();

        $jobs = $this->jobFacade->getAllPairs();

        $jobControl = $form->getComponent('jobId');
        $jobControl->setItems($jobs)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personJobAddPersonJobFormSuccess(Form $form, ArrayHash $values)
    {
        $this->personJobManager->add($values);

        $this->payload->showModal = false;

        $this->flashMessage('person_job_added_person_job', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
    }    
}
