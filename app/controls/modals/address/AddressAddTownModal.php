<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddTownModal.php
 * User: Tomáš Babický
 * Date: 09.12.2020
 * Time: 0:37
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

use Rendix2\FamilyTree\App\Controls\Forms\TownForm;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressAddTownModal extends Control
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
     * AddressAddTownModal constructor.
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
        $this['addressAddTownForm']->render();
    }

    /**
     * @return void
     */
    public function handleAddressAddTown()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $this['addressAddTownForm-countryId']->setItems($countries);

        $presenter->template->modalName = 'addressAddTown';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddTownForm()
    {
        $form = $this->townForm->create();

        $form->onAnchor[] = [$this, 'addressAddTownFormAnchor'];
        $form->onValidate[] = [$this, 'addressAddTownFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddTownFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addressAddTownFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addressAddTownFormValidate(Form $form)
    {
        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressAddTownFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this->townManager->insert()->insert((array) $values);

        $towns = $this->townManager->select()->getCachedManager()->getPairs('name');

        $presenter['addressForm-townId']->setItems($towns);

        $presenter->payload->showModal = false;
        $presenter->payload->snippets = [
            $presenter['addressForm-townId']->getHtmlId() => (string) $presenter['addressForm-townId']->getControl(),
        ];

        $presenter->flashMessage('town_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
    }
}
