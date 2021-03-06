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
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class CountryDeleteCountryFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country
 */
class CountryDeleteCountryFromEditModal extends Control
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
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * CountryDeleteCountryFromEditModal constructor.
     *
     * @param CountryFilter   $countryFilter
     * @param CountryManager  $countryManager
     * @param DeleteModalForm $deleteModalForm
     */
    public function __construct(
        CountryFilter $countryFilter,
        CountryManager $countryManager,
        DeleteModalForm $deleteModalForm
    ) {
        parent::__construct();

        $this->countryFilter = $countryFilter;
        $this->countryManager = $countryManager;
        $this->deleteModalForm = $deleteModalForm;
    }

    public function render()
    {
        $this['countryDeleteCountryFromEditForm']->render();
    }

    /**
     * @param int $countryId
     */
    public function handleCountryDeleteCountryFromEdit($countryId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Country:edit', $presenter->getParameter('id'));
        }

        $this['countryDeleteCountryFromEditForm']->setDefaults(['countryId' => $countryId]);

        $countryFilter = $this->countryFilter;

        $countryModalItem = $this->countryManager->select()->getManager()->getByPrimaryKey($countryId);

        $presenter->template->modalName = 'countryDeleteCountryFromEdit';
        $presenter->template->countryModalItem = $countryFilter($countryModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentCountryDeleteCountryFromEditForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'countryDeleteCountryFromEditFormYesOnClick'];
        $deleteModalFormSettings->httpRedirect = true;

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('countryId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function countryDeleteCountryFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Country:edit', $presenter->getParameter('id'));
        }

        try {
            $this->countryManager->delete()->deleteByPrimaryKey($values->countryId);

            $presenter->flashMessage('country_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redirect('Country:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);

                $presenter->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
