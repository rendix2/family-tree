<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonPresenter.php
 * User: Tomáš Babický
 * Date: 09.02.2021
 * Time: 22:22
 */

namespace Rendix2\FamilyTree\SettingsModule\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonPresenter
 *
 * @package Rendix2\FamilyTree\SettingsModule\App\Presenters
 */
class PersonPresenter extends BasePresenter
{
    const SETTINGS_PERSON_ORDERING = 'settings_person_order';

    const SETTINGS_PERSON_NAME_ORDER = 'settings_person_name_order';

    const PERSON_ORDERING_ID = 1;

    const PERSON_ORDERING_NAME = 2;

    const PERSON_ORDERING_SURNAME = 3;

    const PERSON_ORDERING_NAME_SURNAME = 4;

    const PERSON_ORDERING_SURNAME_NAME = 5;

    const PERSON_ORDER_NAME_NAME_SURNAME = 1;

    const PERSON_ORDER_NAME_SURNAME_NAME = 2;

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
        $this['personForm']->setDefaults(
            [
                'settings_person_order' => $this->getHttpRequest()->getCookie('settings_person_order'),
                'settings_person_name_order' => $this->getHttpRequest()->getCookie('settings_person_name_order'),
            ]
        );
    }

    /**
     * @return Form
     */
    public function createComponentPersonForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $personOrderItems = [
            self::PERSON_ORDERING_ID => 'settings_person_order_id',
            self::PERSON_ORDERING_NAME => 'settings_person_order_name',
            self::PERSON_ORDERING_SURNAME => 'settings_person_order_surname',
            self::PERSON_ORDERING_NAME_SURNAME => 'settings_person_order_name_surname',
            self::PERSON_ORDERING_SURNAME_NAME => 'settings_person_order_surname_name',
        ];

        $form->addSelect(self::SETTINGS_PERSON_ORDERING, 'settings_person_order', $personOrderItems)
            ->setPrompt('settings_select_person_ordering')
            ->setRequired('settings_person_order_required');

        $personNameOrderItems = [
            self::PERSON_ORDER_NAME_NAME_SURNAME => 'settings_person_order_name_name_surname',
            self::PERSON_ORDER_NAME_SURNAME_NAME => 'settings_person_order_name_surname_name',
        ];

        $form->addSelect(self::SETTINGS_PERSON_NAME_ORDER, 'settings_person_name_order', $personNameOrderItems)
            ->setPrompt('settings_select_person_order_name')
            ->setRequired('settings_person_order_required');

        $form->addSubmit('send', 'save');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onSuccess[]= [$this, 'personFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personFormSuccess(Form $form, ArrayHash $values)
    {
        $this->getHttpResponse()->setCookie(self::SETTINGS_PERSON_ORDERING, $values->settings_person_order, '1 year');
        $this->getHttpResponse()->setCookie(self::SETTINGS_PERSON_NAME_ORDER, $values->settings_person_name_order, '1 year');

        $this->cache->clean([Cache::ALL =>true]);

        $this->redirect(':Settings:Person:default');
    }
}
