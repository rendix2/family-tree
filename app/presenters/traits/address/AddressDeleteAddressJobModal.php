<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressJobModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 20:38
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait AddressDeleteAddressJobModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressDeleteAddressJobModal
{
    /**
     * @param int $addressId
     * @param int $jobId
     */
    public function handleDeleteAddressJobItem($addressId, $jobId)
    {
        if ($this->isAjax()) {
            $this['deleteAddressJobForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'jobId' => $jobId
                ]
            );

            $addressFilter = new AddressFilter();
            $jobFilter = new JobFilter();

            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);
            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);


            $this->template->modalName = 'deleteAddressJobItem';
            $this->template->addressModalItem = $addressFilter($addressModalItem);
            $this->template->jobModalItem = $jobFilter($jobModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteAddressJobForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create($this, 'deleteAddressJobFormOk');
        $form->addHidden('addressId');
        $form->addHidden('jobId');


        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteAddressJobFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->jobManager->updateByPrimaryKey($values->jobId, ['addressId' => null]);

            $jobs = $this->jobManager->getByAddressId($values->addressId);

            $this->template->jobs = $jobs;

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('jobs');
        } else {
            $this->redirect('Address:edit', $values->addressId);
        }
    }
}
