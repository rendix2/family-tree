<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusAddNameModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 0:45
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;

use Rendix2\FamilyTree\App\Controls\Forms\NameForm;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class GenusAddNameModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus
 */
class GenusAddNameModal extends Control
{
    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

    /**
     * @var NameForm $nameForm
     */
    private $nameForm;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * GenusAddNameModal constructor.
     *
     * @param GenusManager          $genusManager
     * @param NameFacade            $nameFacade
     * @param NameForm              $nameForm
     * @param NameManager           $nameManager
     * @param PersonManager         $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param ITranslator           $translator
     */
    public function __construct(
        GenusManager $genusManager,
        NameFacade $nameFacade,
        NameForm $nameForm,
        NameManager $nameManager,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->genusManager = $genusManager;
        $this->nameFacade = $nameFacade;
        $this->nameForm = $nameForm;
        $this->nameManager = $nameManager;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['genusAddNameForm']->render();
    }

    /**
     * @param int $genusId
     *
     * @return void
     */
    public function handleGenusAddName($genusId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Genus:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairs($this->translator);
        $genuses = $this->genusManager->getPairsCached('surname');

        $this['genusAddNameForm-personId']->setItems($persons);
        $this['genusAddNameForm-_genusId']->setValue($genusId);
        $this['genusAddNameForm-genusId']->setItems($genuses)
            ->setDisabled()
            ->setDefaultValue($genusId);

        $presenter->template->modalName = 'genusAddName';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentGenusAddNameForm()
    {
        $form = $this->nameForm->create();

        $form->addHidden('_genusId');

        $form->onAnchor[] = [$this, 'genusAddNameFormAnchor'];
        $form->onValidate[] = [$this, 'genusAddNameFormValidate'];
        $form->onSuccess[] = [$this, 'genusAddNameFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function genusAddNameFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function genusAddNameFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->translator);

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->validate();

        $genuses = $this->genusManager->getPairsCached('surname');

        $genusHiddenControl = $form->getComponent('_genusId');

        $genusControl = $form->getComponent('genusId');
        $genusControl->setItems($genuses)
            ->setValue($genusHiddenControl->getValue())
            ->validate();

        $form->removeComponent($genusHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function genusAddNameFormNameFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Genus:edit', $presenter->getParameter('id'));
        }

        $this->nameManager->add($values);

        $genusNamePersons = $this->nameFacade->getByGenusIdCached($values->genusId);

        $presenter->template->genusNamePersons = $genusNamePersons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('name_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('genus_name_persons');
    }
}
