<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownPresenter.php
 * User: Tomáš Babický
 * Date: 09.02.2021
 * Time: 23:20
 */

namespace Rendix2\FamilyTree\SettingsModule\App\Presenters;

use dibi;
use Nette\Application\UI\Form;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownPresenter
 *
 * @package Rendix2\FamilyTree\SettingsModule\App\Presenters
 */
class TownPresenter extends BasePresenter
{
    const TOWN_ORDERING = 'settings_town_order';

    const TOWN_ORDERING_ID = 1;

    const TOWN_ORDERING_NAME = 2;

    const TOWN_ORDERING_ZIP = 3;

    const TOWN_ORDERING_NAME_ZIP = 4;

    const TOWN_ORDERING_ZIP_NAME = 5;


    const TOWN_ORDERING_WAY = 'settings_town_order_way';


    const TOWN_NAME_ORDER = 'settings_town_name_order';

    const TOWN_ORDER_NAME_NAME_ZIP = 1;

    const TOWN_ORDER_NAME_ZIP_NAME = 2;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * SettingsPresenter constructor.
     *
     * @param IStorage $storage
     */
    public function __construct(IStorage $storage)
    {
        parent::__construct();

        $this->cache = new Cache($storage, self::class);
    }

    /**
     * @return void
     */
    public function actionDefault()
    {
        $this['townForm']->setDefaults(
            [
                self::TOWN_ORDERING => $this->getHttpRequest()->getCookie(self::TOWN_ORDERING),
                self::TOWN_NAME_ORDER => $this->getHttpRequest()->getCookie(self::TOWN_NAME_ORDER),
                self::TOWN_ORDERING_WAY => $this->getHttpRequest()->getCookie(self::TOWN_ORDERING_WAY),
            ]
        );
    }

    /**
     * @return Form
     */
    protected function createComponentTownForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $townOrderItems = [
            self::TOWN_ORDERING_ID => 'settings_town_order_id',
            self::TOWN_ORDERING_NAME => 'settings_town_order_name',
            self::TOWN_ORDERING_ZIP => 'settings_town_order_zip',
            self::TOWN_ORDERING_NAME_ZIP => 'settings_town_order_name_zip',
            self::TOWN_ORDERING_ZIP_NAME => 'settings_town_order_zip_name',
        ];

        $form->addSelect(self::TOWN_ORDERING, 'settings_town_order', $townOrderItems)
            ->setPrompt('settings_select_town_ordering')
            ->setRequired('settings_town_order_required');

        $townOrderingWayItems = [
            dibi::ASC => dibi::ASC,
            dibi::DESC => dibi::DESC
        ];

        $form->addRadioList(self::TOWN_ORDERING_WAY, 'settings_order_way', $townOrderingWayItems)
            ->setRequired('settings_order_way_required');

        $townOrderItems = [
            self::TOWN_ORDER_NAME_NAME_ZIP => 'settings_town_order_name_name_zip',
            self::TOWN_ORDER_NAME_ZIP_NAME => 'settings_town_order_name_zip_name',
        ];

        $form->addSelect(self::TOWN_NAME_ORDER, 'settings_town_name_order', $townOrderItems)
            ->setPrompt('settings_select_town_order_name')
            ->setRequired('settings_town_name_order_required');

        $form->addSubmit('send', 'save');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onSuccess[]= [$this, 'townFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function townFormSuccess(Form $form, ArrayHash $values)
    {
        $this->getHttpResponse()->setCookie(self::TOWN_ORDERING, $values->{self::TOWN_ORDERING}, '1 year');
        $this->getHttpResponse()->setCookie(self::TOWN_ORDERING_WAY, $values->{self::TOWN_ORDERING_WAY}, '1 year');
        $this->getHttpResponse()->setCookie(self::TOWN_NAME_ORDER, $values->{self::TOWN_NAME_ORDER}, '1 year');

        $this->cache->clean([Cache::ALL =>true]);

        $this->redirect(':Settings:Town:default');
    }
}
