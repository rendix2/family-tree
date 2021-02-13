<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddFileModal.php
 * User: Tomáš Babický
 * Date: 04.02.2021
 * Time: 14:32
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Image;
use Nette\Utils\Random;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\FileForm;
use Rendix2\FamilyTree\App\Model\FileHelper;

/**
 * Trait PersonAddFileModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddFileModal
{
    /**
     * @param int $personId
     */
    public function handlePersonAddFile($personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairs($this->getTranslator());

        $this['personAddFileForm-personId']->setItems($persons)->setDisabled();
        $this['personAddFileForm-_personId']->setDefaultValue($personId);
        $this['personAddFileForm']->setDefaults(['personId' => $personId,]);

        $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $this->template->modalName = 'personAddFile';
        $this->template->personModalItem = $personFilter($personModalItem);

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentPersonAddFileForm()
    {
        $formFactory = new FileForm($this->getTranslator());

        $form = $formFactory->create();

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
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddFileValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());

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

        $this->template->files = array_chunk($files, 5);

        $this->flashMessage('file_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl('flashes');
        $this->redrawControl('files');
    }
}
