<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Country;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait AddressDeleteAddressListModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Country
 */
trait CountryDeleteListModal
{
    /**
     * @param int $countryId
     */
    public function handleListDeleteItem($countryId)
    {
        if ($this->isAjax()) {
            $countryModalItem = $this->countryManager->getByPrimaryKeyCached($countryId);

            $this['listDeleteForm']->setDefaults(['countryId' => $countryId]);

            $countryFilter = new CountryFilter();

            $this->template->modalName = 'listDeleteItem';
            $this->template->countryModalItem = $countryFilter($countryModalItem);

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
        $form->addHidden('countryId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function listDeleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->countryManager->deleteByPrimaryKey($values->countryId);

            $countries = $this->countryManager->getAll();

            $this->template->countries = $countries;

            $this->flashMessage('country_was_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('list');
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