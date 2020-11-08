<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownWeddingModalDelete.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 15:32
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait TownWeddingModalDelete
 * 
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
trait TownWeddingModalDelete
{
    /**
     * @param int $townId
     * @param int $weddingId
     */
    public function handleDeleteWeddingItem($townId, $weddingId)
    {
        $this['deleteTownWeddingForm']->setDefaults(
            [
                'townId' => $townId,
                'weddingId' => $weddingId
            ]
        );

        $this->template->modalName = 'deleteWeddingItem';

        if ($this->isAjax()) {
            $this->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteTownWeddingForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteTownWeddingFormOk');

        $form->addHidden('townId');
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteTownWeddingFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $weddings = $this->weddingManager->getByTownId($values->townId);

            $this->template->weddings = $weddings;
            $this->template->modalName = 'deleteWeddingItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('weddings');
        } else {
            $this->redirect(':edit', $values->townId);
        }
    }
}
