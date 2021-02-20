<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddGenusModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:57
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\GenusForm;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddGenusModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddGenusModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * PersonAddGenusModal constructor.
     *
     * @param ITranslator $translator
     * @param GenusManager $genusManager
     */
    public function __construct(
        ITranslator $translator,
        GenusManager $genusManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->genusManager = $genusManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddGenusForm']->render();
    }

    /**
     * @return void
     */
    public function handlePersonAddGenus()
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $this->presenter->template->modalName = 'personAddGenus';

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddGenusForm()
    {
        $formFactory = new GenusForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'personAddGenusFormAnchor'];
        $form->onValidate[] = [$this, 'personAddGenusFormValidate'];
        $form->onSuccess[] = [$this, 'personAddGenusFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddGenusFormAnchor()
    {
        $this->presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddGenusFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddGenusFormSuccess(Form $form, ArrayHash $values)
    {
        $this->genusManager->add($values);

        $genuses = $this->genusManager->getPairs('surname');

        $this['personForm-genusId']->setItems($genuses);

        $this->presenter->payload->showModal = false;

        $this->presenter->flashMessage('genus_added', BasePresenter::FLASH_SUCCESS);

        $this->presenter->redrawControl('flashes');
        $this->presenter->redrawControl('personFormWrapper');
        $this->presenter->redrawControl('ks');
    }
}