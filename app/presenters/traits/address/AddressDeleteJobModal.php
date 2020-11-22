<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteJobModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 1:30
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait AddressDeleteJobModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressDeleteJobModal
{
    /**
     * @param int $addressId
     * @param int $jobId
     */
    public function handleDeleteJobItem($addressId, $jobId)
    {
        if ($this->isAjax()) {
            $this['deleteJobForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'jobId' => $jobId
                ]
            );

            $jobFilter = new JobFilter();

            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

            $this->template->modalName = 'deleteJobItem';
            $this->template->jobModalItem = $jobFilter($jobModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteJobForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create($this, 'deleteJobFormOk');
        $form->addHidden('addressId');
        $form->addHidden('jobId');


        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteJobFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->jobManager->deleteByPrimaryKey($values->jobId);

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
