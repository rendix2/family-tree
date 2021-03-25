<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteJobModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 1:30
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class AddressDeleteJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteJobModal extends Control
{
    /**
     * @param int $addressId
     * @param int $jobId
     */
    public function handleAddressDeleteJob($addressId, $jobId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['addressDeleteJobForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'jobId' => $jobId
                ]
            );

            $jobFilter = $this->jobFilter;

            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

            $presenter->template->modalName = 'addressDeleteJob';
            $presenter->template->jobModalItem = $jobFilter($jobModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteJobForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'addressDeleteJobFormYesOnClick']);
        $form->addHidden('addressId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteJobFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            try {
                $this->jobManager->deleteByPrimaryKey($values->jobId);

                $jobs = $this->jobSettingsManager->getByAddressId($values->addressId);

                $presenter->template->jobs = $jobs;

                $presenter->payload->showModal = false;

                $presenter->flashMessage('job_deleted', BasePresenter::FLASH_SUCCESS);

                $presenter->redrawControl('jobs');
            } catch (ForeignKeyConstraintViolationException $e) {
                if ($e->getCode() === 1451) {
                    $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
                } else {
                    Debugger::log($e, ILogger::EXCEPTION);
                }
            } finally {
                $presenter->redrawControl('flashes');
            }
        } else {
            $presenter->redirect('Address:edit', $values->addressId);
        }
    }
}