<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryDeleteTownModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 0:50
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Country;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait CountryDeleteTownModal
 * @package Nette\PhpGenerator\Traits\Country
 */
trait CountryDeleteTownModal
{
    /**
     * @param int $townId
     * @param int $countryId
     */
    public function handleCountryDeleteTown($townId, $countryId)
    {
        if ($this->isAjax()) {
            $this['countryDeleteTownForm']->setDefaults(
                [
                    'countryId' => $countryId,
                    'townId' => $townId
                ]
            );

            $townFilter = new TownFilter();

            $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);

            $this->template->modalName = 'countryDeleteTown';
            $this->template->townModalItem = $townFilter($townModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentCountryDeleteTownForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'countryDeleteTownFormYesOnClick']);
        $form->addHidden('countryId');
        $form->addHidden('townId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function countryDeleteTownFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {

            try {
                $this->townManager->deleteByPrimaryKey($values->townId);

                $towns = $this->townFacade->getByCountryId($values->townId);

                $this->template->towns = $towns;

                $this->payload->showModal = false;

                $this->flashMessage('town_was_deleted', self::FLASH_SUCCESS);

                $this->redrawControl('towns');
            } catch (ForeignKeyConstraintViolationException $e) {
                if ($e->getCode() === 1451) {
                    $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
                } else {
                    Debugger::log($e, ILogger::EXCEPTION);
                }
            } finally {
                $this->redrawControl('flashes');
            }
        } else {
            $this->redirect('Country:edit', $values->countryId);
        }
    }
}
