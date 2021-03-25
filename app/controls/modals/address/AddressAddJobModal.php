<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddJobModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:58
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\JobForm;
use Rendix2\FamilyTree\App\Forms\Settings\JobSettings;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressAddJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressAddJobModal extends Control
{
    /**
     * @param int $townId
     * @param int $addressId
     *
     * @return void
     */
    public function handleAddressAddJob($townId, $addressId)
    {
        $presenter = $this->presenter;

        $addresses = $this->addressFacade->getPairsCached();
        $towns = $this->townSettingsManager->getAllPairsCached();

        $this['addressAddJobForm-_addressId']->setDefaultValue($addressId);
        $this['addressAddJobForm-addressId']->setItems($addresses)
            ->setDisabled()
            ->setDefaultValue($addressId);

        $this['addressAddJobForm-_townId']->setDefaultValue($townId);
        $this['addressAddJobForm-townId']->setItems($towns)
            ->setDisabled()
            ->setDefaultValue($townId);

        $presenter->template->modalName = 'addressAddJob';

        $presenter->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddJobForm()
    {
        $jobSettings = new JobSettings();

        $formFactory = new JobForm($this->translator, $jobSettings);

        $form = $formFactory->create();
        $form->addHidden('_addressId');
        $form->addHidden('_townId');
        $form->onAnchor[] = [$this, 'addressAddJobFormAnchor'];
        $form->onValidate[] = [$this, 'addressAddJobFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddJobFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addressAddJobFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addressAddJobFormValidate(Form $form)
    {
        $towns = $this->townManager->getAllPairs();

        $townHiddenControl = $form->getComponent('_townId');

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->setValue($townHiddenControl->getValue())
            ->validate();

        $addresses = $this->addressFacade->getPairsCached();

        $addressHiddenControl = $form->getComponent('_addressId');

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->setValue($addressHiddenControl->getValue())
            ->validate();

        $form->removeComponent($addressHiddenControl);
        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressAddJobFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->jobManager->add($values);

        $jobs = $this->jobSettingsManager->getByAddressId($values->addressId);

        $presenter->template->jobs = $jobs;

        $presenter->payload->showModal = false;

        $this->flashMessage('job_added', BasePresenter::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('jobs');
    }
}
