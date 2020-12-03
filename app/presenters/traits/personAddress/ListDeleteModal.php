<?php
/**
 *
 * Created by PhpStorm.
 * Filename: personAddress.php
 * User: Tomáš Babický
 * Date: 06.11.2020
 * Time: 1:09
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\PersonAddress;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait ListDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\PersonAddress
 */
trait ListDeleteModal
{
    /**
     * @param int $personId
     * @param int $addressId
     */
    public function handleListDelete($personId, $addressId)
    {
        if ($this->isAjax()) {

            $this['listDeleteForm']->setDefaults(
                [
                    'personId' => $personId,
                    'addressId' => $addressId
                ]
            );

            $addressFilter = new AddressFilter();
            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

            $this->template->modalName = 'listDelete';
            $this->template->addressModalItem = $addressFilter($addressModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentListDeleteForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'listDeleteFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function listDeleteFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->person2AddressManager->deleteByLeftIdAndRightId($values->personId, $values->addressId);

            $this->flashMessage('person_job_was_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $this->redrawControl('flashes');
        }
    }
}
