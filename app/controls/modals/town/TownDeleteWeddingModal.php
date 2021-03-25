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
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownDeleteWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownDeleteWeddingModal extends Control
{
    /**
     * @param int $townId
     * @param int $weddingId
     */
    public function handleTownDeleteWedding($townId, $weddingId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {

            $this['townDeleteWeddingForm']->setDefaults(
                [
                    'townId' => $townId,
                    'weddingId' => $weddingId
                ]
            );

            $weddingFilter = $this->weddingFilter;

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

            $presenter->template->modalName = 'townDeleteWedding';
            $presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
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
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $weddings = $this->weddingManager->getByTownId($values->townId);

            $presenter->template->weddings = $weddings;

            $presenter->payload->showModal = false;

            $this->flashMessage('wedding_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('weddings');
            $presenter->redrawControl('flashes');
        } else {
            $this->redirect('Town:edit', $values->townId);
        }
    }
}
