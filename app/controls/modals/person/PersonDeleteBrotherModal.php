<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteBrotherModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:08
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonDeleteBrotherModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteBrotherModal extends Control
{
    use PersonPrepareMethods;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * PersonDeleteBrotherModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonFilter $personFilter
     * @param PersonFacade $personFacade
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonManager $personManager
     */
    public function __construct(
        ITranslator $translator,
        PersonFilter $personFilter,
        PersonFacade $personFacade,
        PersonSettingsManager $personSettingsManager,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personFilter = $personFilter;
        $this->personFacade = $personFacade;
        $this->personSettingsManager = $personSettingsManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteBrotherForm']->render();
    }

    /**
     * @param int $personId
     * @param int $brotherId
     */
    public function handlePersonDeleteBrother($personId, $brotherId)
    {
        $presenter = $this->presenter;

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->presenter->isAjax()) {
            $this['personDeleteBrotherForm']->setDefaults(
                [
                    'personId' => $personId,
                    'brotherId' => $brotherId
                ]
            );

            $personFilter = $this->personFilter;

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $brotherModalItem = $this->personSettingsManager->getByPrimaryKeyCached($brotherId);

            $this->presenter->template->modalName = 'personDeleteBrother';
            $this->presenter->template->brotherModalItem = $personFilter($brotherModalItem);
            $this->presenter->template->personModalItem = $personFilter($personModalItem);

            $this->presenter->payload->showModal = true;

            $this->presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteBrotherForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'personDeleteBrotherFormYesOnClick']);

        $form->addHidden('personId');
        $form->addHidden('brotherId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteBrotherFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if ($this->presenter->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->brotherId,
                [
                    'fatherId' => null,
                    'motherId' => null
                ]
            );

            $brother = $this->personFacade->getByPrimaryKeyCached($values->brotherId);

            $this->prepareBrothersAndSisters($values->brotherId, $brother->father, $brother->mother);

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('person_brother_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('brothers');
        } else {
            $this->presenter->redirect('Person:edit', $values->personId);
        }
    }
}
