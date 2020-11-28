<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteWeddingModal.php
 * User: Tomáš Babický
 * Date: 28.11.2020
 * Time: 1:08
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;


use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait AddressDeleteWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressDeleteWeddingModal
{
    /**
     * @param int $addressId
     * @param int $weddingId
     */
    public function handleDeleteWeddingItem($addressId, $weddingId)
    {
        if ($this->isAjax()) {
            $this['deleteWeddingForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'weddingId' => $weddingId
                ]
            );

            $weddingModalItem = $this->weddingFacade->getByPrimaryKey($weddingId);

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $weddingFilter = new WeddingFilter($personFilter);

            $this->template->modalName = 'deleteWeddingItem';
            $this->template->weddingModalItem = $weddingFilter($weddingModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteWeddingForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteWeddingFormOk', true);

        $form->addHidden('addressId');
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteWeddingFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $weddings = $this->weddingFacade->getByAddressId($values->addressId);

            $this->template->weddings = $weddings;

            $this->flashMessage('wedding_was_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('weddings');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);

                $this->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }

}