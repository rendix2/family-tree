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

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

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
    public function handleListDeleteItem($personId, $addressId)
    {
        $this['listDeleteForm']->setDefaults(
            [
                'personId' => $personId,
                'addressId' => $addressId
            ]
        );

        $this->template->modalName = 'listDeleteItem';

        if ($this->isAjax()) {
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
        $form = $formFactory->create($this, 'listDeleteFormOk');

        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function listDeleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        $this->person2AddressManager->deleteByLeftIdAndRightId($values->personId, $values->addressId);

        $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

        $this->redrawControl('modal');
        $this->redrawControl('flashes');
        $this->redrawControl('list');
    }
}
