<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddJobModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:58
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\JobForm;
use Rendix2\FamilyTree\App\Forms\Settings\JobSettings;

/**
 * Trait AddressAddJobModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressAddJobModal
{
    /**
     * @param int $townId
     * @param int $addressId
     *
     * @return void
     */
    public function handleAddressAddJob($townId, $addressId)
    {
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

        $this->template->modalName = 'addressAddJob';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddJobForm()
    {
        $jobSettings = new JobSettings();

        $formFactory = new JobForm($this->getTranslator(), $jobSettings);

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
        $this->jobManager->add($values);

        $jobs = $this->jobSettingsManager->getByAddressId($values->addressId);

        $this->template->jobs = $jobs;

        $this->payload->showModal = false;

        $this->flashMessage('job_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('jobs');
    }
}
