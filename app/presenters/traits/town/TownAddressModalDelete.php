<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddressModalDelete.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 15:21
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait TownAddressModalDelete
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
trait TownAddressModalDelete
{
    /**
     * @param int $townId
     * @param int $addressId
     */
    public function handleDeleteAddressItem($townId, $addressId)
    {
        $this['deleteTownAddressForm']->setDefaults(
            [
                'townId' => $townId,
                'addressId' => $addressId
            ]
        );

        $this->template->modalName = 'deleteAddressItem';

        if ($this->isAjax()) {
            $this->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteTownAddressForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteTownAddressFormOk');

        $form->addHidden('townId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteTownAddressFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->addressManager->deleteByPrimaryKey($values->addressId);

            $addresses = $this->addressManager->getByTownId($values->townId);

            $this->template->addresses = $addresses;
            $this->template->modalName = 'deleteAddressItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('addresses');
        } else {
            $this->redirect(':edit', $values->townId);
        }
    }
}
