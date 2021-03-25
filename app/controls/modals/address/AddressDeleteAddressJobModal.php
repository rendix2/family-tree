<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteAddressTownModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 20:38
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressDeleteAddressJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteAddressJobModal extends Control
{
    /**
     * @param int $addressId
     * @param int $jobId
     */
    public function handleAddressDeleteAddressJob($addressId, $jobId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['addressDeleteAddressJobForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'jobId' => $jobId
                ]
            );

            $addressFilter = $this->addressFilter;
            $jobFilter = $this->jobFilter;

            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);
            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

            $this->template->modalName = 'addressDeleteAddressJob';
            $this->template->addressModalItem = $addressFilter($addressModalItem);
            $this->template->jobModalItem = $jobFilter($jobModalItem);

            $presenter->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteAddressJobForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'addressDeleteAddressJobFormYesOnClick']);
        $form->addHidden('addressId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteAddressJobFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this->jobManager->updateByPrimaryKey($values->jobId, ['addressId' => null]);

            $jobs = $this->jobSettingsManager->getByAddressId($values->addressId);

            $this->template->jobs = $jobs;

            $presenter->payload->showModal = false;

            $this->flashMessage('job_updated', BasePresenter::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('jobs');
        } else {
            $this->redirect('Address:edit', $values->addressId);
        }
    }
}
