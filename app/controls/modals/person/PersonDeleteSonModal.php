<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSonModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:16
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

/**
 * Class PersonDeleteSonModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteSonModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * PersonDeleteSonModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonSettingsManager $personSettingsManager
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     * @param PersonFilter $personFilter
     */
    public function __construct(
        ITranslator $translator,
        PersonSettingsManager $personSettingsManager,
        PersonFacade $personFacade,
        PersonManager $personManager,
        PersonFilter $personFilter
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personSettingsManager = $personSettingsManager;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->personFilter = $personFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteSonForm']->render();
    }

    /**
     * @param int $personId
     * @param int $sonId
     */
    public function handlePersonDeleteSon($personId, $sonId)
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

            $this['personDeleteSonForm']->setDefaults(
                [
                    'personId' => $personId,
                    'sonId' => $sonId
                ]
            );

            $personFilter = $this->personFilter;

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $sonModalItem = $this->personFacade->getByPrimaryKeyCached($sonId);

            $this->presenter->template->modalName = 'personDeleteSon';
            $this->presenter->template->personModalItem = $personFilter($personModalItem);
            $this->presenter->template->sonModalItem = $personFilter($sonModalItem);

            $this->presenter->payload->showModal = true;

            $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteSonForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeleteSonFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('sonId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteSonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->presenter->isAjax()) {
            $parent = $this->personManager->getByPrimaryKeyCached($values->personId);

            if ($parent->gender === 'm') {
                $this->personManager->updateByPrimaryKey($values->sonId, ['fatherId' => null,]);
            } elseif ($parent->gender === 'f') {
                $this->personManager->updateByPrimaryKey($values->sonId, ['motherId' => null,]);
            }

            $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

            $sons = $this->personSettingsManager->getSonsByPersonCached($person);

            $this->presenter->template->sons = $sons;

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('person_son_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('sons');
        } else {
            $this->presenter->redirect('Person:edit', $values->personId);
        }
    }
}