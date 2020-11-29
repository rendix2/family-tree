<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddJobModal.php
 * User: Tomáš Babický
 * Date: 25.11.2020
 * Time: 1:38
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Job;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\JobForm;

/**
 * Trait AddJobModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Job
 */
trait AddJobModal
{
    /**
     * @return void
     */
    public function handleAddJob()
    {
        $towns = $this->townManager->getAllPairs();
        $addresses = $this->addressFacade->getPairsCached();

        $this['addJobForm-townId']->setItems($towns);
        $this['addJobForm-addressId']->setItems($addresses);

        $this->template->modalName = 'addJob';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddJobForm()
    {
        $formFactory = new JobForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addJobFormAnchor'];
        $form->onValidate[] = [$this, 'addJobFormValidate'];
        $form->onSuccess[] = [$this, 'saveJobForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addJobFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addJobFormValidate(Form $form)
    {
        $towns = $this->townManager->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns);
        $townControl->validate();

        $addresses = $this->addressFacade->getPairsCached();

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses);
        $addressControl->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveJobForm(Form $form, ArrayHash $values)
    {
        $this->jobManager->add($values);

        $this->flashMessage('job_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }
}