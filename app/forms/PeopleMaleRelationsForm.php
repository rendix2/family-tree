<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PeopleMaleRelationsForm.php
 * User: Tomáš Babický
 * Date: 07.09.2020
 * Time: 0:28
 */

namespace Rendix2\FamilyTree\App\Forms;


use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Managers\PeopleManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;

/**
 * Class PeopleMaleRelationsForm
 * @package Rendix2\FamilyTree\App\Forms
 */
class PeopleMaleRelationsForm extends Control
{

    /**
     * @var ITranslator
     */
    private $translator;

    /**
     * @var PeopleManager $peopleManager
     */
    private $peopleManager;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * PeopleMaleRelationsForm constructor.
     * @param ITranslator $translator
     * @param PeopleManager $peopleManager
     * @param RelationManager $relationManager
     */
    public function __construct(ITranslator $translator, PeopleManager $peopleManager, RelationManager $relationManager)
    {
        parent::__construct();

        $this->translator = $translator;
        $this->peopleManager = $peopleManager;
        $this->relationManager = $relationManager;
    }

    /**
     *
     */
    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setFile(__DIR__ . $sep . 'templates'. $sep . 'peopleMaleRelations.latte');
        $this->template->setTranslator($this->translator);

        $peoples = $this->peopleManager->getAll();
        $partners = $this->relationManager->getByMaleId($this->presenter->getParameter('id'));

        $selectedPeoples = [];

        foreach ($partners as $partner) {
            $selectedPeoples[] = $partner->femaleId;
        }

        $this->template->peoples = $peoples;
        $this->template->selectedPeoples = array_flip($selectedPeoples);

        $this->template->render();
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();
        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'save'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function save(Form $form, ArrayHash $values)
    {
        $formData = $form->getHttpData();

        $id = $this->presenter->getParameter('id');

        $this->relationManager->deleteByMaleId($id);

        foreach ($formData['maleRelation'] as $partnerId) {
            $this->relationManager->add([
                'maleId' => $id,
                'femaleId' => $partnerId
            ]);
        }

        $this->presenter->flashMessage('item_saved', 'success');
        $this->presenter->redirect('edit', $id);
    }
}
