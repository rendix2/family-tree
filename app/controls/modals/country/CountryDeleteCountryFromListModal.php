<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class CountryDeleteCountryFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country
 */
class CountryDeleteCountryFromListModal extends Control
{
    /**
     * @var CountryFilter $countryFilter
     */
    private $countryFilter;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * CountryDeleteCountryFromListModal constructor.
     *
     * @param CountryFilter $countryFilter
     * @param CountryManager $countryManager
     * @param ITranslator $translator
     */
    public function __construct(
        CountryFilter $countryFilter,
        CountryManager $countryManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->countryFilter = $countryFilter;
        $this->countryManager = $countryManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['countryDeleteCountryFromListForm']->render();
    }

    /**
     * @param int $countryId
     */
    public function handleCountryDeleteCountryFromList($countryId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $countryModalItem = $this->countryManager->getByPrimaryKeyCached($countryId);

            $this['countryDeleteCountryFromListForm']->setDefaults(['countryId' => $countryId]);

            $countryFilter = $this->countryFilter;

            $presenter->template->modalName = 'countryDeleteCountryFromList';
            $presenter->template->countryModalItem = $countryFilter($countryModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentCountryDeleteCountryFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'countryDeleteCountryFromListFormYesOnClick']);
        $form->addHidden('countryId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function countryDeleteCountryFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->countryManager->deleteByPrimaryKey($values->countryId);

            $countries = $this->countryManager->getAllCached();

            $presenter->template->countries = $countries;

            $presenter->flashMessage('country_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('list');
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
