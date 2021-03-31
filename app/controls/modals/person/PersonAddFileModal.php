<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddFileModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:55
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Image;
use Nette\Utils\Random;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Controls\Forms\FileForm;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\FileManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\FileDir;
use Rendix2\FamilyTree\App\Model\FileHelper;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddFileModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddFileModal extends Control
{
    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var FileManager $fileManager
     */
    private $fileManager;

    /**
     * @var string $fileDir
     */
    private $fileDir;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var FileForm $fileForm
     */
    private $fileForm;

    /**
     * PersonAddFileModal constructor.
     *
     * @param PersonFacade          $personFacade
     * @param PersonFilter          $personFilter
     * @param ITranslator           $translator
     * @param FileManager           $fileManager
     * @param FileDir               $fileDir
     * @param FileForm              $fileForm
     * @param PersonManager         $personManager
     * @param PersonSettingsManager $personSettingsManager
     */
    public function __construct(
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        ITranslator $translator,
        FileManager $fileManager,
        FileDir $fileDir,
        FileForm $fileForm,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager
    ) {
        parent::__construct();

        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->translator = $translator;
        $this->fileManager = $fileManager;
        $this->fileDir = $fileDir->getFileDir();
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;

        $this->fileForm = $fileForm;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddFileForm']->render();
    }

    /**
     * @param int $personId
     */
    public function handlePersonAddFile($personId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairs($this->translator);

        $this['personAddFileForm-personId']->setItems($persons)->setDisabled();
        $this['personAddFileForm-_personId']->setDefaultValue($personId);
        $this['personAddFileForm']->setDefaults(['personId' => $personId,]);

        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $presenter->template->modalName = 'personAddFile';
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentPersonAddFileForm()
    {
        $form = $this->fileForm->create();

        $form->addHidden('_personId');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onAnchor[] = [$this, 'personAddFileFormAnchor'];
        $form->onValidate[] = [$this, 'personAddFileValidate'];
        $form->onSuccess[] = [$this, 'personAddFileFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddFileFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddFileValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->translator);

        $personHiddenComponent = $form->getComponent('_personId');

        $personComponent = $form->getComponent('personId');
        $personComponent->setItems($persons)
            ->setValue($personHiddenComponent->getValue())
            ->validate();

        $form->removeComponent($personHiddenComponent);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddFileFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $sep = DIRECTORY_SEPARATOR;

        $originFileName = $values->file->getName();
        $explodedName = explode('.', $originFileName);
        $explodedCount = count($explodedName);
        $extension = $explodedName[$explodedCount - 1];
        $originName = str_replace('.' . $extension, '', $originFileName);

        $generatedName = Random::generate();
        $newFileName = $generatedName . '.' . $extension;
        $newPath = $this->fileDir . $newFileName;
        $fileType = FileHelper::getFileType($extension);

        $values->file->move($newPath);

        // crete thumbnail
        if ($fileType === 'image') {
            $image = Image::fromFile($newPath);

            $image->resize(400, 400);

            $thumbnailPath = $this->fileDir . 'thumbnails' . $sep . $newFileName;

            $image->save($thumbnailPath);

            $image->resize(120, 160);

            $thumbnailPath = $this->fileDir . 'thumbnails' . $sep . 's'. $newFileName;

            $image->save($thumbnailPath);
        }

        $data = [
            'personId' => $values->personId,
            'originName' => $originName,
            'newName' => $generatedName,
            'extension' => $extension,
            'type' => $fileType,
            'size' => $values->file->getSize(),
            'description' => $values->description
        ];

        $id = $this->fileManager->add($data);

        $files = $this->fileManager->getByPersonId($id);

        $presenter->template->files = array_chunk($files, 5);

        $presenter->flashMessage('file_added', BasePresenter::FLASH_SUCCESS);

        $presenter->payload->showModal = false;

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('files');
    }
}
