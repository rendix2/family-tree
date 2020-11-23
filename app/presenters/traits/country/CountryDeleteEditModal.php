<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\country;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait GenusEditDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\country
 */
trait CountryDeleteEditModal
{
    /**
     * @param int $countryId
     */
    public function handleEditDeleteItem($countryId)
    {
        if ($this->isAjax()) {
            $this['editDeleteForm']->setDefaults(['countryId' => $countryId]);

            $countryFilter = new CountryFilter();

            $countryModalItem = $this->countryManager->getByPrimaryKeyCached($countryId);

            $this->template->modalName = 'editDeleteItem';
            $this->template->countryModalItem = $countryFilter($countryModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentEditDeleteForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create($this, 'editDeleteFormOk', true);
        $form->addHidden('countryId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function editDeleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->countryManager->deleteByPrimaryKey($values->countryId);

            $this->flashMessage('country_was_deleted', self::FLASH_SUCCESS);

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
