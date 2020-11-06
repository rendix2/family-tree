<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryTownDeleteModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 0:50
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Country;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait CountryTownDeleteModal
 * @package Nette\PhpGenerator\Traits\Country
 */
trait CountryTownDeleteModal
{
    /**
     * @param int $townId
     * @param int $countryId
     */
    public function handleDeleteTownItem($townId, $countryId)
    {
        $this->template->modalName = 'deleteTownItem';

        $this['deleteCountryTownForm']->setDefaults(
            [
                'townId' => $townId,
                'countryId' => $countryId
            ]
        );

        if ($this->isAjax()) {
            $this->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteCountryTownForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteCountryTownFormOk');

        $form->addHidden('townId');
        $form->addHidden('countryId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteCountryTownFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->townManager->deleteByPrimaryKey($values->townId);

            $this->payload->showModal = false;

            $towns = $this->townManager->getAllByCountry($values->townId);

            $this->template->towns = $towns;
            $this->template->modalName = 'deleteTownItem';

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('towns');
        } else {
            $this->redirect(':edit', $values->townId);
        }
    }
}
