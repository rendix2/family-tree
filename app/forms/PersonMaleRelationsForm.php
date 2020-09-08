<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonMaleRelationsForm.php
 * User: Tomáš Babický
 * Date: 07.09.2020
 * Time: 0:28
 */

namespace Rendix2\FamilyTree\App\Forms;

use Dibi\DateTime;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\PeopleManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;

/**
 * Class PersonMaleRelationsForm
 * @package Rendix2\FamilyTree\App\Forms
 */
class PersonMaleRelationsForm extends Control
{

    /**
     * @var ITranslator
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
     * PersonMaleRelationsForm constructor.
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
     * @return void
     */
    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setFile(__DIR__ . $sep . 'templates'. $sep . 'personMaleRelationsForm.latte');
        $this->template->setTranslator($this->translator);

        $id = $this->presenter->getParameter('id');

        $persons = $this->personManager->getAllExceptMe($id);
        $males = $this->relationManager->getByFemaleId($id);

        $selectedPersons = [];
        $selectedDates = [];

        foreach ($males as $male) {
            $selectedDates[$male->maleId] = [
                'since' => $male->dateSince,
                'to' => $male->dateTo
            ];

            $selectedPersons[$male->maleId] = $male->maleId;
        }

        $this->template->persons = $persons;
        $this->template->selectedPersons = $selectedPersons;
        $this->template->selectedDates = $selectedDates;

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

        bdump($formData);

        $id = $this->presenter->getParameter('id');

        $this->relationManager->deleteByFemaleId($id);

        if (isset($formData['maleRelation'])) {
            foreach ($formData['maleRelation'] as $key => $maleId) {
                $this->relationManager->add([
                    'maleId' => $maleId,
                    'femaleId' => $id,
                    'dateSince' => $formData['dateSince'][$key] ? new DateTime($formData['dateSince'][$key]) : null,
                    'dateTo'    => $formData['dateTo'][$key]    ? new DateTime($formData['dateTo'][$key])    : null,
                ]);
            }
        }

        $this->presenter->flashMessage('item_saved', 'success');
        $this->presenter->redirect('maleRelations', $id);
    }
}
