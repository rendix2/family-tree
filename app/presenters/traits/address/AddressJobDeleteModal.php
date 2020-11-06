<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressJobDeleteModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 1:30
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait AddressJobDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressJobDeleteModal
{
    /**
     * @param int $addressId
     * @param int $jobId
     */
    public function handleDeleteJobItem($addressId, $jobId)
    {
        $this->template->modalName = 'deleteJobItem';

        $this['deleteAddressJobForm']->setDefaults(
            [
                'jobId' => $jobId,
                'addressId' => $addressId
            ]
        );

        if ($this->isAjax()) {
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

        $form->addHidden('jobId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteAddressJobFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->jobManager->deleteByPrimaryKey($values->jobId);

            $jobs = $this->jobManager->getByAddressId($values->addressId);

            $this->template->jobs = $jobs;
            $this->template->modalName = 'deleteJobItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('jobs');
        } else {
            $this->redirect(':edit', $values->addressId);
        }
    }
}
