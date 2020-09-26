<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonHusbandsForm.php
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
 * Class PersonHusbandsForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class PersonHusbandsForm extends Control
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

        $this->template->setFile(__DIR__ . $sep . 'templates' . $sep . 'personHusbandsForm.latte');
        $this->template->setTranslator($this->translator);

        $id = $this->presenter->getParameter('id');

        $persons = $this->personManager->getAllExceptMe($id);
        $husbands = $this->weddingManager->getAllByWifeId($id);

        $selectedPersons = [];
        $selectedDates = [];

        foreach ($husbands as $husband) {
            $selectedDates[$husband->husbandId] = [
                'since' => $husband->dateSince,
                'to' => $husband->dateTo,
                'untilNow' => $husband->untilNow
            ];

            $selectedPersons[$husband->husbandId] = $husband->husbandId;
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
        $id = $this->presenter->getParameter('id');

        $this->weddingManager->deleteByWife($id);

        if (isset($formData['husbands'])) {
            foreach ($formData['husbands'] as $key => $husbandId) {
                $this->weddingManager->add([
                    'husbandId' => $husbandId,
                    'wifeId' => $id,
                    'dateSince' => $formData['dateSince'][$key] ? new DateTime($formData['dateSince'][$key]) : null,
                    'dateTo'    => $formData['dateTo'][$key]    ? new DateTime($formData['dateTo'][$key])    : null,
                    'untilNow'  => isset($formData['untilNow'][$key])
                ]);
            }
        }


        $this->presenter->flashMessage('item_saved', BasePresenter::FLASH_SUCCESS);
        $this->presenter->redirect('husbands', $id);
    }
}
