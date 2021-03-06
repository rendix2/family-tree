<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryDeleteTownModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 0:50
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class CountryDeleteTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country
 */
class CountryDeleteTownModal extends Control
{
    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var TownFilter $townFilter
     */
    private $townFilter;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * CountryDeleteTownModal constructor.
     *
     * @param DeleteModalForm $deleteModalForm
     * @param TownFacade $townFacade
     * @param TownFilter $townFilter
     * @param TownManager $townManager
     */
    public function __construct(
        DeleteModalForm $deleteModalForm,
        TownFacade $townFacade,
        TownFilter $townFilter,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->townFacade = $townFacade;
        $this->townFilter = $townFilter;
        $this->townManager = $townManager;
        $this->deleteModalForm = $deleteModalForm;
    }

    public function render()
    {
        $this['countryDeleteTownForm']->render();
    }

    /**
     * @param int $townId
     * @param int $countryId
     */
    public function handleCountryDeleteTown($townId, $countryId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Country:edit', $presenter->getParameter('id'));
        }

        $this['countryDeleteTownForm']->setDefaults(
            [
                'countryId' => $countryId,
                'townId' => $townId
            ]
        );

        $townFilter = $this->townFilter;

        $townModalItem = $this->townFacade->select()->getCachedManager()->getByPrimaryKey($townId);

        $presenter->template->modalName = 'countryDeleteTown';
        $presenter->template->townModalItem = $townFilter($townModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentCountryDeleteTownForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'countryDeleteTownFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

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
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Country:edit', $presenter->getParameter('id'));
        }

        try {
            $this->townManager->delete()->deleteByPrimaryKey($values->townId);

            $towns = $this->townFacade->select()->getSettingsCachedManager()->getAllByCountry($values->townId);

            $presenter->template->towns = $towns;

            $presenter->payload->showModal = false;

            $presenter->flashMessage('town_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('towns');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $presenter->redrawControl('flashes');
        }
    }
}
