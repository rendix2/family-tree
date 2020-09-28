<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ${FILE_NAME}
 * User: Tomáš Babický
 * Date: 29.09.2020
 * Time: 0:30
 */

namespace Rendix2\FamilyTree\App\Forms;

use Dibi\DateTime;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

class PersonNamesForm extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * PersonNamesForm constructor.
     * @param ITranslator $translator
     * @param NameManager $nameManager
     * @param PersonManager $personManager
     * @param GenusManager $genusManager
     */
    public function __construct(
        ITranslator $translator,
        NameManager $nameManager,
        PersonManager $personManager,
        GenusManager $genusManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->nameManager = $nameManager;
        $this->personManager = $personManager;
        $this->genusManager = $genusManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setFile(__DIR__ . $sep . 'templates' . $sep . 'personNameForm.latte');
        $this->template->setTranslator($this->translator);

        $personId = $this->presenter->getParameter('id');

        $person = $this->personManager->getByPrimaryKey($personId);
        $persons = $this->personManager->getAll();

        $names = $this->nameManager->getAll();
        $selectedNames = $this->nameManager->getByPersonId($personId);

        $genuses = $this->genusManager->getPairs('surname');

        $selectedPersons = [];
        $selectedDates = [];

        foreach ($selectedNames as $name) {
            $selectedDates[$name->id] = [
                'since' => $name->dateSince,
                'to' => $name->dateTo,
                'untilNow' => $name->untilNow
            ];

            $selectedPersons[$name->id] = $name->id;
        }

        $this->template->persons = $persons;
        $this->template->selectedPersons = $selectedPersons;
        $this->template->selectedDates = $selectedDates;

        $this->template->names = $names;
        $this->template->person = $person;

        $this->template->genuses = $genuses;

        $this->template->addFilter('person', new PersonFilter($this->translator));
        $this->template->addFilter('name', new NameFilter($this->translator));

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
        $personId = $this->presenter->getParameter('id');
        $addedIds = [];

        if (isset($formData['names'])) {
            foreach ($formData['names'] as $key => $nameId) {
                $nameExists = $this->nameManager->getByPrimaryKeyAndPersonId($nameId, $personId);
                $name = $this->nameManager->getByPrimaryKey($nameId);

                $data = [
                    'personId' => $personId,
                    'genusId' => $formData['genusId'][$key],
                    'name' => $name->name,
                    'surname' => $name->surname,
                    'dateSince' => $formData['dateSince'][$key] ? new DateTime($formData['dateSince'][$key]) : null,
                    'dateTo' => $formData['dateTo'][$key] ? new DateTime($formData['dateTo'][$key]) : null,
                    'untilNow' => isset($formData['untilNow'][$key])
                ];

                if ($nameExists) {
                    $this->nameManager->updateByPrimaryKey($nameExists->id, $data);
                } else {
                    $addedIds[] =$this->nameManager->add($data);
                }
            }
        }

        $savedNames = $this->nameManager->getByPersonId($personId);

        $savedNamesId = [];

        foreach ($savedNames as $savedName) {
            $savedNamesId[] = $savedName->id;
        }

        $sentNameId = $addedIds;

        if (isset($formData['names'])) {
            foreach ($formData['names'] as $nameId) {
                $sentNameId[] = (int)$nameId;
            }
        }

        $sentNameId = array_unique($sentNameId);
        $deletedNames = array_diff($savedNamesId, $sentNameId);

        foreach ($deletedNames as $nameId) {
            $this->nameManager->deleteByPrimaryKey($nameId);
        }

        $this->presenter->flashMessage('item_saved', BasePresenter::FLASH_SUCCESS);
        $this->presenter->redirect('names', $personId);
    }
}
