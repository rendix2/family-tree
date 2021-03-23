<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteWeddingModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 15:32
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait TownDeleteWeddingModal
 * 
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
class TownDeleteWeddingModal extends Control
{
    /**
     * @param int $townId
     * @param int $weddingId
     */
    public function handleTownDeleteWedding($townId, $weddingId)
    {
        if ($this->isAjax()) {

            $this['townDeleteWeddingForm']->setDefaults(
                [
                    'townId' => $townId,
                    'weddingId' => $weddingId
                ]
            );

            $weddingFilter = $this->weddingFilter;

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

            $this->template->modalName = 'townDeleteWedding';
            $this->template->weddingModalItem = $weddingFilter($weddingModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteWeddingForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'townDeleteWeddingFormYesOnClick']);
        $form->addHidden('townId');
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteWeddingFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $weddings = $this->weddingManager->getByTownId($values->townId);

            $this->template->weddings = $weddings;

            $this->payload->showModal = false;

            $this->flashMessage('wedding_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('weddings');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('Town:edit', $values->townId);
        }
    }
}
