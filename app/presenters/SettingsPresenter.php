<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SettingsPresenter.php
 * User: Tomáš Babický
 * Date: 06.11.2020
 * Time: 21:11
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\LanguageManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Settings;

/**
 * Class SettingsPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class SettingsPresenter extends BasePresenter
{
    /**
     * @var LanguageManager $languageManager
     */
    private $languageManager;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * SettingsPresenter constructor.
     *
     * @param LanguageManager $languageManager
     * @param IStorage $storage
     */
    public function __construct(LanguageManager $languageManager, IStorage $storage)
    {
        parent::__construct();

        $this->cache = new Cache($storage, self::class);
        $this->languageManager = $languageManager;
    }

    /**
     * @return void
     */
    public function actionDefault()
    {
        $this['settingsForm']->setDefaults(
            [
                'settings_person_order' => $this->getHttpRequest()->getCookie('settings_person_order'),
                'settings_person_name_order' => $this->getHttpRequest()->getCookie('settings_person_name_order'),
                'settings_language' => $this->getHttpRequest()->getCookie('settings_language')
            ]
        );
    }

    /**
     * @return Form
     */
    protected function createComponentSettingsForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $personOrderItems = [
            Settings::PERSON_ORDERING_ID => 'settings_person_order_id',
            Settings::PERSON_ORDERING_NAME => 'settings_person_order_name',
            Settings::PERSON_ORDERING_SURNAME => 'settings_person_order_surname',
            Settings::PERSON_ORDERING_NAME_SURNAME => 'settings_person_order_name_surname',
            Settings::PERSON_ORDERING_SURNAME_NAME => 'settings_person_order_surname_name',
        ];

        $form->addSelect(Settings::SETTINGS_PERSON_ORDERING, 'settings_person_order', $personOrderItems)
            ->setPrompt('settings_select_person_ordering')
            ->setRequired('settings_person_order_required');

        $personNameOrderItems = [
            Settings::PERSON_ORDER_NAME_NAME_SURNAME => 'settings_person_order_name_name_surname',
            Settings::PERSON_ORDER_NAME_SURNAME_NAME => 'settings_person_order_name_surname_name',
        ];

        $form->addSelect(Settings::SETTINGS_PERSON_NAME_ORDER, 'settings_person_name_order', $personNameOrderItems)
            ->setPrompt('settings_select_person_order_name')
            ->setRequired('settings_person_order_required');

        $form->addSelect(Settings::SETTINGS_LANGUAGE, 'settings_language', $this->languageManager->getAllFluent()->fetchPairs('langName', 'langName'))
            ->setPrompt('settings_select_language')
            ->setRequired('settings_language_required');

        $form->addSubmit('send', 'save');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onSuccess[]= [$this, 'settingsFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function settingsFormSuccess(Form $form, ArrayHash $values)
    {
        $this->getHttpResponse()->setCookie(Settings::SETTINGS_PERSON_ORDERING, $values->settings_person_order, '1 year');
        $this->getHttpResponse()->setCookie(Settings::SETTINGS_PERSON_NAME_ORDER, $values->settings_person_name_order, '1 year');
        $this->getHttpResponse()->setCookie(Settings::SETTINGS_LANGUAGE, $values->settings_language, '1 year');

        $this->cache->clean([Cache::ALL =>true]);

        $this->redirect('Settings:default');
    }
}
