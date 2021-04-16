<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPersonSourceModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:04
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\SourceForm;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\SourceManager;
use Rendix2\FamilyTree\App\Model\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddPersonSourceModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddPersonSourceModal extends Control
{
    /**
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var SourceManager $sourceManager
     */
    private $sourceManager;

    /**
     * @var SourceFacade $sourceFacade
     */
    private $sourceFacade;

    /**
     * @var SourceForm $sourceForm
     */
    private $sourceForm;

    /**
     * PersonAddPersonSourceModal constructor.
     *
     * @param SourceTypeManager $sourceTypeManager
     * @param PersonManager     $personManager
     * @param SourceManager     $sourceContainer
     * @param SourceFacade      $sourceFacade
     * @param SourceForm        $sourceForm
     */
    public function __construct(
        SourceTypeManager $sourceTypeManager,
        PersonManager $personManager,
        SourceManager $sourceContainer,
        SourceFacade $sourceFacade,
        SourceForm $sourceForm
    ) {
        parent::__construct();

        $this->sourceTypeManager = $sourceTypeManager;
        $this->personManager = $personManager;
        $this->sourceManager = $sourceContainer;
        $this->sourceFacade = $sourceFacade;
        $this->sourceForm = $sourceForm;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddPersonSourceForm']->render();
    }

    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPersonSource($personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();
        $sourceTypes = $this->sourceTypeManager->select()->getCachedManager()->getPairs('name');

        $this['personAddPersonSourceForm-_personId']->setDefaultValue($personId);
        $this['personAddPersonSourceForm-personId']->setItems($persons)->setDisabled()->setDefaultValue($personId);
        $this['personAddPersonSourceForm-sourceTypeId']->setItems($sourceTypes);

        $presenter->template->modalName = 'personAddPersonSource';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPersonSourceForm()
    {
        $form = $this->sourceForm->create();

        $form->addHidden('_personId');

        $form->onAnchor[] = [$this, 'personAddPersonSourceFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPersonSourceFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPersonSourceFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPersonSourceFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPersonSourceFormValidate(Form $form)
    {
        $persons = $this->personManager->select()->getCachedManager()->getAllPairs();

        $personHiddenControl = $form->getComponent('_personId');

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->setValue($personHiddenControl->getValue())
            ->validate();

        $sourceTypes = $this->sourceTypeManager->select()->getCachedManager()->getPairs('name');

        $sourceTypeControl = $form->getComponent('sourceTypeId');
        $sourceTypeControl->setItems($sourceTypes)
            ->validate();

        $form->removeComponent($personHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddPersonSourceFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->sourceManager->insert()->insert((array) $values);

        $sources = $this->sourceFacade->select()->getManager()->getByPersonId($values->personId);

        $presenter->template->sources = $sources;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('source_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('sources');
    }
}
