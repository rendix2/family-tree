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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownSettingsFacade;
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
     * @var TownSettingsFacade $townSettingsFacade
     */
    private $townSettingsFacade;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * CountryDeleteTownModal constructor.
     * @param TownFacade $townFacade
     * @param TownFilter $townFilter
     * @param TownManager $townManager
     * @param TownSettingsFacade $townSettingsFacade
     * @param ITranslator $translator
     */
    public function __construct(
        TownFacade $townFacade,
        TownFilter $townFilter,
        TownManager $townManager,
        TownSettingsFacade $townSettingsFacade,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->townFacade = $townFacade;
        $this->townFilter = $townFilter;
        $this->townManager = $townManager;
        $this->townSettingsFacade = $townSettingsFacade;
        $this->translator = $translator;
    }

    /**
     * @param int $townId
     * @param int $countryId
     */
    public function handleCountryDeleteTown($townId, $countryId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['countryDeleteTownForm']->setDefaults(
                [
                    'countryId' => $countryId,
                    'townId' => $townId
                ]
            );

            $townFilter = $this->townFilter;

            $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);

            $presenter->template->modalName = 'countryDeleteTown';
            $presenter->template->townModalItem = $townFilter($townModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentCountryDeleteTownForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

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
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            try {
                $this->townManager->deleteByPrimaryKey($values->townId);

                $towns = $this->townSettingsFacade->getByCountryId($values->townId);

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
        } else {
            $presenter->redirect('Country:edit', $values->countryId);
        }
    }
}
