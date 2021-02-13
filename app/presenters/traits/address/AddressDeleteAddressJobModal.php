<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteAddressTownModal.php
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
 * Trait TownDeleteAddressTownModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressDeleteAddressJobModal
{
    /**
     * @param int $addressId
     * @param int $jobId
     */
    public function handleAddressDeleteAddressJob($addressId, $jobId)
    {
        if ($this->isAjax()) {
            $this['addressDeleteAddressJobForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'jobId' => $jobId
                ]
            );

            $addressFilter = new AddressFilter();
            $jobFilter = new JobFilter($this->getHttpRequest());

            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);
            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

            $this->template->modalName = 'addressDeleteAddressJob';
            $this->template->addressModalItem = $addressFilter($addressModalItem);
            $this->template->jobModalItem = $jobFilter($jobModalItem);

            $this->payload->showModal = true;

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
        if ($this->isAjax()) {
            $this->jobManager->updateByPrimaryKey($values->jobId, ['addressId' => null]);

            $jobs = $this->jobSettingsManager->getByAddressId($values->addressId);

            $this->template->jobs = $jobs;

            $this->payload->showModal = false;

            $this->flashMessage('job_updated', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('jobs');
        } else {
            $this->redirect('Address:edit', $values->addressId);
        }
    }
}
