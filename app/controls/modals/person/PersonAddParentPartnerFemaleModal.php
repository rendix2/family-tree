<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddParentPartnerFemaleModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:58
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\RelationForm;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\RelationManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonAddParentPartnerFemaleModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddParentPartnerFemaleModal extends Control
{
    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var RelationForm $relationForm
     */
    private $relationForm;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * PersonAddParentPartnerFemaleModal constructor.
     *
     * @param PersonFacade        $personFacade
     * @param RelationForm        $relationForm
     * @param RelationManager     $relationManager
     * @param PersonManager       $personManager
     * @param PersonUpdateService $personUpdateService
     */
    public function __construct(
        PersonFacade $personFacade,
        RelationForm $relationForm,
        RelationManager $relationManager,
        PersonManager $personManager,
        PersonUpdateService $personUpdateService
    ) {
        parent::__construct();

        $this->relationForm = $relationForm;

        $this->personFacade = $personFacade;
        $this->relationManager = $relationManager;
        $this->personManager = $personManager;
        $this->personUpdateService = $personUpdateService;
    }

    public function __destruct()
    {
        $this->relationForm = null;


        $this->relationManager = null;
        $this->personManager = null;
        $this->personUpdateService = null;

        $this->personFacade = null;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddParentPartnerFemaleForm']->render();
    }

    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddParentPartnerFemale($personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();

        $this['personAddParentPartnerFemaleForm-_maleId']->setDefaultValue($personId);
        $this['personAddParentPartnerFemaleForm-maleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $this['personAddParentPartnerFemaleForm-femaleId']->setItems($persons);

        $presenter->template->modalName = 'personAddParentFemalePartner';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddParentPartnerFemaleForm()
    {
        $form = $this->relationForm->create();

        $form->addHidden('_maleId');

        $form->onAnchor[] = [$this, 'personAddParentPartnerFemaleFormAnchor'];
        $form->onValidate[] = [$this, 'personAddParentPartnerFemaleFormValidate'];
        $form->onSuccess[] = [$this, 'personAddParentPartnerFemaleFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddParentPartnerFemaleFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddParentPartnerFemaleFormValidate(Form $form)
    {
        $persons = $this->personManager->select()->getCachedManager()->getAllPairs();

        $maleHiddenControl = $form->getComponent('_maleId');

        $maleControl = $form->getComponent('maleId');
        $maleControl->setItems($persons);
        $maleControl->setValue($maleHiddenControl->getValue())
            ->validate();

        $femaleControl = $form->getComponent('femaleId');
        $femaleControl->setItems($persons)
            ->validate();

        $form->removeComponent($maleHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddParentPartnerFemaleFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->relationManager->insert()->insert((array) $values);

        $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($presenter->getParameter('id'));

        $this->personUpdateService->prepareParentsRelations($presenter, $person->father, $person->mother);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('relation_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('father_relations');
        $presenter->redrawControl('mother_relations');
    }
}
