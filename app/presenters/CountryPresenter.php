<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryPresenter.php
 * User: Tomáš Babický
 * Date: 04.10.2020
 * Time: 21:48
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownManager;

/**
 * Class CountryPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class CountryPresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    /**
     * @var CountryManager $manager
     */
    private $manager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * CountryPresenter constructor.
     *
     * @param CountryManager $countryManager
     * @param TownManager $townManager
     */
    public function __construct(CountryManager $countryManager, TownManager $townManager)
    {
        parent::__construct();

        $this->manager = $countryManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $countries = $this->manager->getAll();

        $this->template->countries = $countries;
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        $towns = $this->townManager->getAllByCountry($id);

        $this->template->towns = $towns;
        $this->template->country = $this->item;

        $this->template->addFilter('country', new CountryFilter());
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();


        $form->addText('name', 'country_name');
        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}