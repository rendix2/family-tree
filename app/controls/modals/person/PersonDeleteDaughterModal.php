<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteDaughterMoodal.php
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

/**
 * Class PersonDeleteDaughterModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteDaughterModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonSettingsManager $personSettingsManage
     */
    private $personSettingsManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * PersonDeleteDaughterModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonManager $personManager
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     */
    public function __construct(
        ITranslator $translator,
        PersonSettingsManager $personSettingsManager,
        PersonManager $personManager,
        PersonFacade $personFacade,
        PersonFilter $personFilter
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personSettingsManager = $personSettingsManager;
        $this->personManager = $personManager;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteDaughterForm']->render();
    }

    /**
     * @param int $personId
     * @param int $daughterId
     */
    public function handlePersonDeleteDaughter($personId, $daughterId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteDaughterForm']->setDefaults(
            [
                'personId' => $personId,
                'daughterId' => $daughterId
            ]
        );

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
        $daughterModalItem = $this->personManager->getByPrimaryKeyCached($daughterId);

        $presenter->template->modalName = 'personDeleteDaughter';
        $presenter->template->personModalItem = $personFilter($personModalItem);
        $presenter->template->daughterModalItem = $personFilter($daughterModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteDaughterForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeleteDaughterFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('daughterId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteDaughterFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $parent = $this->personManager->getByPrimaryKeyCached($values->personId);

        if ($parent->gender === 'm') {
            $this->personManager->updateByPrimaryKey($values->daughterId, ['fatherId' => null,]);
        } elseif ($parent->gender === 'f') {
            $this->personManager->updateByPrimaryKey($values->daughterId, ['motherId' => null,]);
        }

        $daughters = $this->personSettingsManager->getDaughtersByPersonCached($parent);

        $presenter->template->daughters = $daughters;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_daughter_deleted');

        $presenter->redrawControl('daughters');
        $presenter->redrawControl('flashes');
    }
}
