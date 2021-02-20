<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteGenusModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:09
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Facades\PersonSettingsFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonDeleteGenusModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteGenusModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonSettingsFacade $personSettingsFacade
     */
    private $personSettingsFacade;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var GenusFilter $genusFilter
     */
    private $genusFilter;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * PersonDeleteGenusModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonSettingsFacade $personSettingsFacade
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     * @param GenusManager $genusManager
     * @param GenusFilter $genusFilter
     * @param PersonFilter $personFilter
     */
    public function __construct(
        ITranslator $translator,
        PersonSettingsFacade $personSettingsFacade,
        PersonFacade $personFacade,
        PersonManager $personManager,
        GenusManager $genusManager,
        GenusFilter $genusFilter,
        PersonFilter $personFilter
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personSettingsFacade = $personSettingsFacade;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->genusManager = $genusManager;
        $this->genusFilter = $genusFilter;
        $this->personFilter = $personFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteGenusForm']->render();
    }

    /**
     * @param int $personId
     * @param int $currentGenusId
     * @param int $deleteGenusPersonId
     */
    public function handlePersonDeleteGenus($personId, $currentGenusId, $deleteGenusPersonId)
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->presenter->isAjax()) {
            $this['personDeleteGenusForm']->setDefaults(
                [
                    'genusId' => $currentGenusId,
                    'personId' => $personId,
                    'deleteGenusPersonId' => $deleteGenusPersonId,
                ]
            );

            $personFilter = $this->personFilter;
            $genusFilter = $this->genusFilter;

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($deleteGenusPersonId);
            $genusModalItem = $this->genusManager->getByPrimaryKeyCached($currentGenusId);

            $this->presenter->template->personModalItem = $personFilter($personModalItem);
            $this->presenter->template->genusModalItem = $genusFilter($genusModalItem);
            $this->presenter->template->modalName = 'personDeleteGenus';

            $this->presenter->payload->showModal = true;

            $this->presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteGenusForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'personDeleteGenusFormYesOnClick']);

        $form->addHidden('genusId');
        $form->addHidden('deleteGenusPersonId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteGenusFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->presenter->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->deleteGenusPersonId, ['genusId' => null]);

            $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

            $genusPersons = [];

            if ($person->genus) {
                $genusPersons = $this->personSettingsFacade->getByGenusIdCached($person->genus->id);
            }

            $this->presenter->template->genusPersons = $genusPersons;

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('person_saved', BasePresenter::FLASH_SUCCESS);

            if ($values->personId === $values->deleteGenusPersonId) {
                $this['personForm-genusId']->setDefaultValue(null);

                $this->presenter->redrawControl('personFormWrapper');
            }

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('genus_persons');
            $this->presenter->redrawControl('jsFormCallback');
        } else {
            $this->presenter->redirect('Person:edit', $values->personId);
        }
    }
}