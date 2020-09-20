<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PlacePresenter.php
 * User: Tomáš Babický
 * Date: 20.09.2020
 * Time: 0:11
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\PeopleManager;
use Rendix2\FamilyTree\App\Managers\PlaceManager;

/**
 * Class PlacePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PlacePresenter extends BasePresenter
{
    use CrudPresenter;

    /**
     * @var PlaceManager $manager
     */
    private $manager;

    /**
     * @var PeopleManager $personManager
     */
    private $personManager;

    /**
     * PlacePresenter constructor.
     *
     * @param PlaceManager $placeManager
     * @param PeopleManager $personManager
     */
    public function __construct(PlaceManager $placeManager, PeopleManager $personManager)
    {
        parent::__construct();

        $this->manager = $placeManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $places = $this->manager->getAll();

        $this->template->places = $places;
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        $birthPersons = $this->personManager->getByBirthPlaceId($id);
        $deathPersons = $this->personManager->getByDeathPlaceId($id);

        $this->template->birthPersons = $birthPersons;
        $this->template->deathPersons = $deathPersons;
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addText('name', 'place_name')
            ->setRequired('place_name_required');

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
