<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonWivesForm.php
 * User: Tomáš Babický
 * Date: 08.09.2020
 * Time: 0:39
 */

namespace Rendix2\FamilyTree\App\Forms;

use Dibi\DateTime;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonWivesForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class PersonWivesForm extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * PersonHusbandsForm constructor.
     *
     * @param ITranslator $translator
     * @param PersonManager $personManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        ITranslator $translator,
        PersonManager $personManager,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personManager = $personManager;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->setFile(__DIR__ . $sep . 'templates' . $sep . 'personWivesForm.latte');
        $this->template->setTranslator($this->translator);

        $id = $this->presenter->getParameter('id');

        $persons = $this->personManager->getFemalesExceptMe($id);
        $wives = $this->weddingManager->getAllByHusbandId($id);

        $selectedPersons = [];
        $selectedDates = [];

        foreach ($wives as $wife) {
            $selectedDates[$wife->wifeId] = [
                'since' => $wife->dateSince,
                'to' => $wife->dateTo,
                'untilNow' => $wife->untilNow
            ];

            $selectedPersons[$wife->wifeId] = $wife->wifeId;
        }

        $this->template->persons = $persons;
        $this->template->selectedPersons = $selectedPersons;
        $this->template->selectedDates = $selectedDates;

        $this->template->addFilter('person', new PersonFilter($this->translator));

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
        $husbandId = $this->presenter->getParameter('id');

        if (isset($formData['wives'])) {
            foreach ($formData['wives'] as $key => $wifeId) {
                $weddingExists = $this->weddingManager->getByWifeIdAndHusbandId($wifeId, $husbandId);

                $data = [
                    'husbandId' => $husbandId,
                    'wifeId' => $wifeId,
                    'dateSince' => $formData['dateSince'][$key] ? new DateTime($formData['dateSince'][$key]) : null,
                    'dateTo' => $formData['dateTo'][$key] ? new DateTime($formData['dateTo'][$key]) : null,
                    'untilNow' => isset($formData['untilNow'][$key])
                ];

                if ($weddingExists) {
                    $this->weddingManager->updateByPrimaryKey($weddingExists->id, $data);
                } else {
                    $this->weddingManager->add($data);
                }
            }
        }

        $savedWives = $this->weddingManager->getAllByHusbandId($husbandId);

        $savedWivesId = [];

        foreach ($savedWives as $savedWife) {
            $savedWivesId[] = $savedWife->wifeId;
        }

        $sentWivesId = [];

        if (isset($formData['wives'])) {
            foreach ($formData['wives'] as $wifeId) {
                $sentWivesId[] = (int)$wifeId;
            }
        }

        $deletedWives = array_diff($savedWivesId, $sentWivesId);

        foreach ($deletedWives as $wifeId) {
            $this->weddingManager->deleteByHusbandAndWife($husbandId, $wifeId);
        }

        $this->presenter->flashMessage('item_saved', BasePresenter::FLASH_SUCCESS);
        $this->presenter->redirect('wives', $husbandId);
    }
}
