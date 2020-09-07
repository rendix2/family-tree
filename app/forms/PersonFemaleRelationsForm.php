<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonFemaleRelationsForm.php
 * User: Tomáš Babický
 * Date: 07.09.2020
 * Time: 0:28
 */

namespace Rendix2\FamilyTree\App\Forms;


use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\PeopleManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;

/**
 * Class PersonFemaleRelationsForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class PersonFemaleRelationsForm extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PeopleManager $personManager
     */
    private $personManager;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * PeopleFemaleRelationsForm constructor.
     * @param ITranslator $translator
     * @param PeopleManager $personManager
     * @param RelationManager $relationManager
     */
    public function __construct(ITranslator $translator, PeopleManager $personManager, RelationManager $relationManager)
    {
        parent::__construct();

        $this->translator = $translator;
        $this->personManager = $personManager;
        $this->relationManager = $relationManager;
    }

    /**
     *
     */
    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setFile(__DIR__ . $sep . 'templates'. $sep . 'personFemaleRelationsForm.latte');
        $this->template->setTranslator($this->translator);

        $persons = $this->personManager->getAll();
        $females = $this->relationManager->getByMaleId($this->presenter->getParameter('id'));

        $selectedPersons = [];

        foreach ($females as $female) {
            $selectedPersons[$female->femaleId] = $female->femaleId;
        }

        $this->template->persons = $persons;
        $this->template->selectedPersons = $selectedPersons;

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
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

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

        if (isset($formData['femaleRelation'])) {
            foreach ($formData['femaleRelation'] as $femaleId) {
                $this->relationManager->add([
                    'maleId' => $id,
                    'femaleId' => $femaleId
                ]);
            }
        }

        $this->presenter->flashMessage('item_saved', 'success');
        $this->presenter->redirect('femaleRelations', $id);
    }
}
