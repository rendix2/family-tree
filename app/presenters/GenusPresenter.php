<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 22:34
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;

/**
 * Class GenusPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class GenusPresenter extends BasePresenter
{
    use CrudPresenter;

    /**
     * @var GenusManager $manager
     */
    private $manager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * GenusPresenter constructor.
     *
     * @param GenusManager $manager
     * @param PersonManager $personManager
     */
    public function __construct(GenusManager $manager, PersonManager $personManager)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $genuses = $this->manager->getAll();

        $this->template->genuses = $genuses;
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $allPersons = [];
            $genusOrderedPersons = [];
        } else {
            $allPersons = $this->personManager->getByGenusId($id);
            $genusOrderedPersons = $this->personManager->getByGenusIdOrderedByParent($id);
        }

        $this->template->allPersons = $allPersons;
        $this->template->genusOrderedPersons = $genusOrderedPersons;
        $this->template->genus = $this->item;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
        $this->template->addFilter('genus', new GenusFilter());
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addText('surname', 'genus_surname')
            ->setRequired('genus_surname_required');

        $form->addText('surnameFonetic', 'genus_surname_fonetic')
            ->setNullable();

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
