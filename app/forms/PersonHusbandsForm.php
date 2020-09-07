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

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\PeopleManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;

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
     * @var PeopleManager $personManager
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
     * @param PeopleManager $personManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        ITranslator $translator,
        PeopleManager $personManager,
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

        $persons = $this->personManager->getAll();
        $husbands = $this->weddingManager->getAllByWifeId($this->presenter->getParameter('id'));

        $selectedPersons = [];

        foreach ($husbands as $husband) {
            $selectedPersons[$husband->husbandId] = $husband->husbandId;
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

        $this->weddingManager->deleteByWife($id);

        if (isset($formData['husbands'])) {
            foreach ($formData['husbands'] as $husbandId) {
                $this->weddingManager->add([
                    'husbandId' => $husbandId,
                    'wifeId' => $id
                ]);
            }
        }


        $this->presenter->flashMessage('item_saved', 'success');
        $this->presenter->redirect('husbands', $id);
    }
}
