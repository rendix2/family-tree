<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait CountryDeleteCountryFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\country
 */
class CountryDeleteCountryFromEditModal extends Control
{
    /**
     * @param int $countryId
     */
    public function handleCountryDeleteCountryFromEdit($countryId)
    {
        if ($this->isAjax()) {
            $this['countryDeleteCountryFromEditForm']->setDefaults(['countryId' => $countryId]);

            $countryFilter = $this->countryFilter;

            $countryModalItem = $this->countryManager->getByPrimaryKeyCached($countryId);

            $this->template->modalName = 'countryDeleteCountryFromEdit';
            $this->template->countryModalItem = $countryFilter($countryModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentCountryDeleteCountryFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'countryDeleteCountryFromEditFormYesOnClick'], true);
        $form->addHidden('countryId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function countryDeleteCountryFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->countryManager->deleteByPrimaryKey($values->countryId);

            $this->flashMessage('country_deleted', self::FLASH_SUCCESS);

            $this->redirect('Country:default');
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
