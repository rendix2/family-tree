<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddTownModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:00
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\TownForm;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class CountryAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country
 */
class CountryAddTownModal extends Control
{
    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var TownForm $townForm
     */
    private $townForm;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * CountryAddTownModal constructor.
     *
     * @param CountryManager $countryManager
     * @param TownForm       $townForm
     * @param TownManager    $townManager
     */
    public function __construct(
        CountryManager $countryManager,
        TownForm $townForm,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->townForm = $townForm;

        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
    }

    public function render()
    {
        $this['countryAddTownForm']->render();
    }

    /**
     * @param int $countryId
     *
     * @return void
     */
    public function handleCountryAddTown($countryId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Country:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $this['countryAddTownForm-_countryId']->setValue($countryId);
        $this['countryAddTownForm-countryId']->setItems($countries)
            ->setDisabled()
            ->setDefaultValue($countryId);

        $presenter->template->modalName = 'countryAddTown';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentCountryAddTownForm()
    {
        $form = $this->townForm->create();

        $form->addHidden('_countryId');

        $form->onAnchor[] = [$this, 'countryAddTownFormAnchor'];
        $form->onValidate[] = [$this, 'countryAddTownFormValidate'];
        $form->onSuccess[] = [$this, 'countrySuccessTownFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function countryAddTownFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function countryAddTownFormValidate(Form $form)
    {
        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $countryHiddenControl = $form->getComponent('_countryId');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->setValue($countryHiddenControl->getValue())
            ->validate();

        $form->removeComponent($countryHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function countrySuccessTownForm(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Country:edit', $presenter->getParameter('id'));
        }

        $this->townManager->insert()->insert((array) $values);

        $towns = $this->townManager->select()->getManager()->getAllByCountry($values->countryId);

        $presenter->template->towns = $towns;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('town_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('towns');
    }
}
