<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteWeddingModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 15:32
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;

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
trait TownDeleteWeddingModal
{
    /**
     * @param int $townId
     * @param int $weddingId
     */
    public function handleDeleteWeddingItem($townId, $weddingId)
    {
        if ($this->isAjax()) {

            $this['deleteTownWeddingForm']->setDefaults(
                [
                    'townId' => $townId,
                    'weddingId' => $weddingId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $weddingFilter = new WeddingFilter($personFilter);

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

            $this->template->modalName = 'deleteWeddingItem';
            $this->template->weddingModalItem = $weddingFilter($weddingModalItem);

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

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('weddings');
        } else {
            $this->redirect(':edit', $values->townId);
        }
    }
}
