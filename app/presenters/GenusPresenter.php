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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Modals\Genus\Container\GenusModalContainer;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\GenusForm;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;

/**
 * Class GenusPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class GenusPresenter extends BasePresenter
{
    /**
     * @var DurationFilter $durationFilter
     */
    private $durationFilter;

    /**
     * @var GenusFilter $genusFilter
     */
    private $genusFilter;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var GenusModalContainer $genusModalContainer
     */
    private $genusModalContainer;

    /**
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

    /**
     * @var NameFilter $nameFilter
     */
    private $nameFilter;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * GenusPresenter constructor.
     *
     * @param DurationFilter $durationFilter
     * @param GenusFilter $genusFilter
     * @param GenusManager $manager
     * @param NameFacade $nameFacade
     * @param NameFilter $nameFilter
     * @param NameManager $nameManager
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param PersonManager $personManager
     */
    public function __construct(
        DurationFilter $durationFilter,
        GenusFilter $genusFilter,
        GenusModalContainer $genusModalContainer,
        GenusManager $manager,
        NameFacade $nameFacade,
        NameFilter $nameFilter,
        NameManager $nameManager,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->genusModalContainer = $genusModalContainer;

        $this->nameFacade = $nameFacade;
        $this->personFacade = $personFacade;

        $this->durationFilter = $durationFilter;
        $this->genusFilter = $genusFilter;
        $this->nameFilter = $nameFilter;
        $this->personFilter = $personFilter;

        $this->genusManager = $manager;
        $this->nameManager = $nameManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $genuses = $this->genusManager->getAllCached();

        $this->template->genuses = $genuses;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        if ($id !== null) {
            $genus = $this->genusManager->getByPrimaryKeyCached($id);

            if (!$genus) {
                $this->error('Item not found.');
            }

            $this['genusForm']->setDefaults((array) $genus);
        }
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $genusPersons = [];
            $genusNamePersons = [];
        } else {
            $genusPersons = $this->personFacade->getByGenusIdCached($id);
            $genusNamePersons = $this->nameFacade->getByGenusIdCached($id);
        }

        $genus = $this->genusManager->getByPrimaryKeyCached($id);

        $this->template->genusPersons = $genusPersons;
        $this->template->genusNamePersons = $genusNamePersons;
        $this->template->genus = $genus;
    }

    /**
     * @return Form
     */
    public function createComponentGenusForm()
    {
        $formFactory = new GenusForm($this->translator);

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'genusFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function genusFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->genusManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('genus_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->genusManager->add($values);

            $this->flashMessage('genus_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Genus:edit', $id);
    }

    public function createComponentGenusDeleteGenusFromListModal()
    {
        return $this->genusModalContainer->getGenusDeleteGenusFromListModalFactory()->create();
    }

    public function createComponentGenusDeleteGenusFromEditModal()
    {
        return $this->genusModalContainer->getGenusDeleteGenusFromEditModalFactory()->create();
    }

    public function createComponentGenusDeletePersonNameModal()
    {
        return $this->genusModalContainer->getGenusDeletePersonNameModalFactory()->create();
    }

    public function createComponentGenusDeletePersonGenusModal()
    {
        return $this->genusModalContainer->getGenusDeletePersonGenusModalFactory()->create();
    }

    public function createComponentGenusAddNameModal()
    {
        return $this->genusModalContainer->getGenusAddNameModalFactory()->create();
    }
}
