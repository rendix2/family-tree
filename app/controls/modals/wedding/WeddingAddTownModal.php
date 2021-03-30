<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingAddTownModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:25
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class WeddingAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding
 */
class WeddingAddTownModal extends Control
{
    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * WeddingAddTownModal constructor.
     *
     * @param CountryManager $countryManager
     * @param ITranslator $translator
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(
        CountryManager $countryManager,
        ITranslator $translator,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager
    ) {
        parent::__construct();

        $this->countryManager = $countryManager;
        $this->translator = $translator;
        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;
    }

    public function render()
    {
        $this['weddingAddTownForm']->render();
    }

    /**
     * @return void
     */
    public function handleWeddingAddTown()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Wedding:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->getPairs('name');

        $this['weddingAddTownForm-countryId']->setItems($countries);

        $presenter->template->modalName = 'weddingAddTown';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingAddTownForm()
    {
        $formFactory = new TownForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'weddingAddTownFormAnchor'];
        $form->onValidate[] = [$this, 'weddingAddTownFormValidate'];
        $form->onSuccess[] = [$this, 'weddingAddTownFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function weddingAddTownFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function weddingAddTownFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function weddingAddTownFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Wedding:edit', $presenter->getParameter('id'));
        }

        $this->townManager->add($values);

        $towns = $this->townSettingsManager->getAllPairsCached();

        $presenter['weddingForm-townId']->setItems($towns);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('town_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jsFormCallback');

        $presenter->payload->snippets = [
            $presenter['weddingForm-townId']->getHtmlId() => (string) $presenter['weddingForm-townId']->getControl(),
        ];
    }
}
