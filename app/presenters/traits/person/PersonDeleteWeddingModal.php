<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteWeddingModal.php
 * User: Tomáš Babický
 * Date: 26.10.2020
 * Time: 17:38
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteWeddingModal
{
    /**
     * @param int $personId
     * @param int $weddingId
     */
    public function handleDeleteWeddingItem($personId, $weddingId)
    {
        $this['deletePersonWeddingForm']->setDefaults(
            [
                'weddingId' => $weddingId,
                'personId' => $personId
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
    protected function createComponentDeletePersonWeddingForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonWeddingFormOk');

        $form->addHidden('weddingId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonWeddingFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $this->prepareWeddings($values->personId);

            $this->template->modalName = 'deleteWeddingItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('husbands');
            $this->redrawControl('wives');
        } else {
            $this->redirect(':edit', $values->personId);
        }
    }
}
