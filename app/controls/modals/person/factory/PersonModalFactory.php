<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonModalFactory.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 12:22
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person\Factory;


/**
 * Class PersonModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person\Factor
 */
class PersonModalFactory
{
    /**
     * @var PersonAddAddressModalFactory $personAddAddressModalFactory
     */
    private $personAddAddressModalFactory;

    /**
     * @var PersonAddBrotherModalFactory $personAddBrotherModalFactory
     */
    private $personAddBrotherModalFactory;

    /**
     * @var PersonAddDaughterModalFactory $personAddDaughterModalFactory
     */
    private $personAddDaughterModalFactory;

    /**
     * @var PersonAddFileModalFactory $personAddFileModalFactory
     */
    private $personAddFileModalFactory;

    /**
     * @var PersonAddGenusModalFactory $personAddHusbandModalFactory
     */
    private $personAddGenusModalFactory;

    /**
     * @var PersonAddHusbandModalFactory $personAddHusbandModalFactory
     */
    private $personAddHusbandModalFactory;

    /**
     * @var PersonAddParentPartnerFemaleModalFactory $personAddParentPartnerFemaleModalFactory
     */
    private $personAddParentPartnerFemaleModalFactory;

    /**
     * @var PersonAddParentPartnerMaleModalFactory $personAddParentPartnerMaleModalFactory
     */
    private $personAddParentPartnerMaleModalFactory;

    /**
     * @var PersonAddPartnerFemaleModalFactory $personAddPartnerFemaleModalFactory
     */
    private $personAddPartnerFemaleModalFactory;

    /**
     * @var PersonAddPartnerMaleModalFactory $personAddPartnerMaleModalFactory
     */
    private $personAddPartnerMaleModalFactory;

    /**
     * @var PersonAddPersonAddressModalFactory $personAddPersonAddressModalFactory
     */
    private $personAddPersonAddressModalFactory;

    /**
     * @var PersonAddPersonJobModalFactory $personAddPersonJobModalFactory
     */
    private $personAddPersonJobModalFactory;

    /**
     * @var PersonAddPersonNameModalFactory $personAddPersonNameModalFactory
     */
    private $personAddPersonNameModalFactory;

    /**
     * @var PersonAddPersonSourceModalFactory $personAddPersonSourceModalFactory
     */
    private $personAddPersonSourceModalFactory;

    /**
     * @var PersonAddSisterModalFactory $personAddSisterModalFactory
     */
    private $personAddSisterModalFactory;

    /**
     * @var PersonAddSonModalFactory $personAddSonModalFactory
     */
    private $personAddSonModalFactory;

    /**
     * @var PersonAddTownModalFactory $personAddTownModalFactory
     */
    private $personAddTownModalFactory;

    /**
     * @var PersonAddWifeModalFactory $personAddWifeModalFactory
     */
    private $personAddWifeModalFactory;

    /**
     * @var PersonDeleteBrotherModalFactory $personDeleteBrotherModalFactory
     */
    private $personDeleteBrotherModalFactory;

    /**
     * @var PersonDeleteDaughterModalFactory $personDeleteDaughterModalFactory
     */
    private $personDeleteDaughterModalFactory;

    /**
     * @var PersonDeleteFileModalFactory $personDeleteFileModalFactory
     */
    private $personDeleteFileModalFactory;

    /**
     * @var PersonDeleteGenusModalFactory $personDeleteGenusModalFactory
     */
    private $personDeleteGenusModalFactory;

    /**
     * @var PersonDeleteHistoryNoteModalFactory $personDeleteHistoryNoteModalFactory
     */
    private $personDeleteHistoryNoteModalFactory;

    /**
     * @var PersonDeletePersonNameModalFactory $personDeletePersonNameModalFactory
     */
    private $personDeletePersonNameModalFactory;

    /**
     * @var PersonDeletePersonAddressModalFactory $personDeletePersonAddressModalFactory
     */
    private $personDeletePersonAddressModalFactory;

    /**
     * @var PersonDeletePersonFromEditModalFactory $personDeletePersonFromEditModalFactory
     */
    private $personDeletePersonFromEditModalFactory;

    /**
     * @var PersonDeletePersonFromEditModalFactory $personDeletePersonFromEditModalFactory
     */
    private $personDeletePersonFromListModalFactory;

    /**
     * @var PersonDeletePersonJobModalFactory $personDeletePersonJobModalFactory
     */
    private $personDeletePersonJobModalFactory;

    /**
     * @var PersonDeleteRelationModalFactory $personDeleteRelationModalFactory
     */
    private $personDeleteRelationModalFactory;

    /**
     * @var PersonDeleteRelationParentModalFactory $personDeleteRelationParentModalFactory
     */
    private $personDeleteRelationParentModalFactory;

    /**
     * @var PersonDeleteSisterModalFactory $personDeleteSisterModalFactory
     */
    private $personDeleteSisterModalFactory;

    /**
     * @var PersonDeleteSonModalFactory $personDeleteSonModalFactory
     */
    private $personDeleteSonModalFactory;

    /**
     * @var PersonDeleteSourceModalFactory $personDeleteSourceModalFactory
     */
    private $personDeleteSourceModalFactory;

    /**
     * @var PersonDeleteWeddingModalFactory $personDeleteWeddingModalFactory
     */
    private $personDeleteWeddingModalFactory;

    /**
     * @var PersonDeleteWeddingParentModalFactory $personDeleteWeddingParentModalFactory
     */
    private $personDeleteWeddingParentModalFactory;

    /**
     * @var PersonShowImageModalFactory $personShowImageModalFactory
     */
    private $personShowImageModalFactory;

    /**
     * PersonModalFactory constructor.
     * @param PersonAddAddressModalFactory $personAddAddressModalFactory
     * @param PersonAddBrotherModalFactory $personAddBrotherModalFactory
     * @param PersonAddDaughterModalFactory $personAddDaughterModalFactory
     * @param PersonAddFileModalFactory $personAddFileModalFactory
     * @param PersonAddGenusModalFactory $personAddGenusModalFactory
     * @param PersonAddHusbandModalFactory $personAddHusbandModalFactory
     * @param PersonAddParentPartnerFemaleModalFactory $personAddParentPartnerFemaleModalFactory
     * @param PersonAddParentPartnerMaleModalFactory $personAddParentPartnerMaleModalFactory
     * @param PersonAddPartnerFemaleModalFactory $personAddPartnerFemaleModalFactory
     * @param PersonAddPartnerMaleModalFactory $personAddPartnerMaleModalFactory
     * @param PersonAddPersonAddressModalFactory $personAddPersonAddressModalFactory
     * @param PersonAddPersonJobModalFactory $personAddPersonJobModalFactory
     * @param PersonAddPersonNameModalFactory $personAddPersonNameModalFactory
     * @param PersonAddPersonSourceModalFactory $personAddPersonSourceModalFactory
     * @param PersonAddSisterModalFactory $personAddSisterModalFactory
     * @param PersonAddSonModalFactory $personAddSonModalFactory
     * @param PersonAddTownModalFactory $personAddTownModalFactory
     * @param PersonAddWifeModalFactory $personAddWifeModalFactory
     * @param PersonDeleteBrotherModalFactory $personDeleteBrotherModalFactory
     * @param PersonDeleteDaughterModalFactory $personDeleteDaughterModalFactory
     * @param PersonDeleteFileModalFactory $personDeleteFileModalFactory
     * @param PersonDeleteGenusModalFactory $personDeleteGenusModalFactory
     * @param PersonDeleteHistoryNoteModalFactory $personDeleteHistoryNoteModalFactory
     * @param PersonDeletePersonNameModalFactory $personDeletePersonNameModalFactory
     * @param PersonDeletePersonAddressModalFactory $personDeletePersonAddressModalFactory
     * @param PersonDeletePersonFromEditModalFactory $personDeletePersonFromEditModalFactory
     * @param PersonDeletePersonFromListModalFactory $personDeletePersonFromListModalFactory
     * @param PersonDeletePersonJobModalFactory $personDeletePersonJobModalFactory
     * @param PersonDeleteRelationModalFactory $personDeleteRelationModalFactory
     * @param PersonDeleteRelationParentModalFactory $personDeleteRelationParentModalFactory
     * @param PersonDeleteSisterModalFactory $personDeleteSisterModalFactory
     * @param PersonDeleteSonModalFactory $personDeleteSonModalFactory
     * @param PersonDeleteSourceModalFactory $personDeleteSourceModalFactory
     * @param PersonDeleteWeddingModalFactory $personDeleteWeddingModalFactory
     * @param PersonDeleteWeddingParentModalFactory $personDeleteWeddingParentModalFactory
     * @param PersonShowImageModalFactory $personShowImageModalFactory
     */
    public function __construct(
        PersonAddAddressModalFactory $personAddAddressModalFactory,
        PersonAddBrotherModalFactory $personAddBrotherModalFactory,
        PersonAddDaughterModalFactory $personAddDaughterModalFactory,
        PersonAddFileModalFactory $personAddFileModalFactory,
        PersonAddGenusModalFactory $personAddGenusModalFactory,
        PersonAddHusbandModalFactory $personAddHusbandModalFactory,
        PersonAddParentPartnerFemaleModalFactory $personAddParentPartnerFemaleModalFactory,
        PersonAddParentPartnerMaleModalFactory $personAddParentPartnerMaleModalFactory,
        PersonAddPartnerFemaleModalFactory $personAddPartnerFemaleModalFactory,
        PersonAddPartnerMaleModalFactory $personAddPartnerMaleModalFactory,
        PersonAddPersonAddressModalFactory $personAddPersonAddressModalFactory,
        PersonAddPersonJobModalFactory $personAddPersonJobModalFactory,
        PersonAddPersonNameModalFactory $personAddPersonNameModalFactory,
        PersonAddPersonSourceModalFactory $personAddPersonSourceModalFactory,
        PersonAddSisterModalFactory $personAddSisterModalFactory,
        PersonAddSonModalFactory $personAddSonModalFactory,
        PersonAddTownModalFactory $personAddTownModalFactory,
        PersonAddWifeModalFactory $personAddWifeModalFactory,
        PersonDeleteBrotherModalFactory $personDeleteBrotherModalFactory,
        PersonDeleteDaughterModalFactory $personDeleteDaughterModalFactory,
        PersonDeleteFileModalFactory $personDeleteFileModalFactory,
        PersonDeleteGenusModalFactory $personDeleteGenusModalFactory,
        PersonDeleteHistoryNoteModalFactory $personDeleteHistoryNoteModalFactory,
        PersonDeletePersonNameModalFactory $personDeletePersonNameModalFactory,
        PersonDeletePersonAddressModalFactory $personDeletePersonAddressModalFactory,
        PersonDeletePersonFromEditModalFactory $personDeletePersonFromEditModalFactory,
        PersonDeletePersonFromListModalFactory $personDeletePersonFromListModalFactory,
        PersonDeletePersonJobModalFactory $personDeletePersonJobModalFactory,
        PersonDeleteRelationModalFactory $personDeleteRelationModalFactory,
        PersonDeleteRelationParentModalFactory $personDeleteRelationParentModalFactory,
        PersonDeleteSisterModalFactory $personDeleteSisterModalFactory,
        PersonDeleteSonModalFactory $personDeleteSonModalFactory,
        PersonDeleteSourceModalFactory $personDeleteSourceModalFactory,
        PersonDeleteWeddingModalFactory $personDeleteWeddingModalFactory,
        PersonDeleteWeddingParentModalFactory $personDeleteWeddingParentModalFactory,
        PersonShowImageModalFactory $personShowImageModalFactory
    ) {
        $this->personAddAddressModalFactory = $personAddAddressModalFactory;
        $this->personAddBrotherModalFactory = $personAddBrotherModalFactory;
        $this->personAddDaughterModalFactory = $personAddDaughterModalFactory;
        $this->personAddFileModalFactory = $personAddFileModalFactory;
        $this->personAddGenusModalFactory = $personAddGenusModalFactory;
        $this->personAddHusbandModalFactory = $personAddHusbandModalFactory;
        $this->personAddParentPartnerFemaleModalFactory = $personAddParentPartnerFemaleModalFactory;
        $this->personAddParentPartnerMaleModalFactory = $personAddParentPartnerMaleModalFactory;
        $this->personAddPartnerFemaleModalFactory = $personAddPartnerFemaleModalFactory;
        $this->personAddPartnerMaleModalFactory = $personAddPartnerMaleModalFactory;
        $this->personAddPersonAddressModalFactory = $personAddPersonAddressModalFactory;
        $this->personAddPersonJobModalFactory = $personAddPersonJobModalFactory;
        $this->personAddPersonNameModalFactory = $personAddPersonNameModalFactory;
        $this->personAddPersonSourceModalFactory = $personAddPersonSourceModalFactory;
        $this->personAddSisterModalFactory = $personAddSisterModalFactory;
        $this->personAddSonModalFactory = $personAddSonModalFactory;
        $this->personAddTownModalFactory = $personAddTownModalFactory;
        $this->personAddWifeModalFactory = $personAddWifeModalFactory;
        $this->personDeleteBrotherModalFactory = $personDeleteBrotherModalFactory;
        $this->personDeleteDaughterModalFactory = $personDeleteDaughterModalFactory;
        $this->personDeleteFileModalFactory = $personDeleteFileModalFactory;
        $this->personDeleteGenusModalFactory = $personDeleteGenusModalFactory;
        $this->personDeleteHistoryNoteModalFactory = $personDeleteHistoryNoteModalFactory;
        $this->personDeletePersonNameModalFactory = $personDeletePersonNameModalFactory;
        $this->personDeletePersonAddressModalFactory = $personDeletePersonAddressModalFactory;
        $this->personDeletePersonFromEditModalFactory = $personDeletePersonFromEditModalFactory;
        $this->personDeletePersonFromListModalFactory = $personDeletePersonFromListModalFactory;
        $this->personDeletePersonJobModalFactory = $personDeletePersonJobModalFactory;
        $this->personDeleteRelationModalFactory = $personDeleteRelationModalFactory;
        $this->personDeleteRelationParentModalFactory = $personDeleteRelationParentModalFactory;
        $this->personDeleteSisterModalFactory = $personDeleteSisterModalFactory;
        $this->personDeleteSonModalFactory = $personDeleteSonModalFactory;
        $this->personDeleteSourceModalFactory = $personDeleteSourceModalFactory;
        $this->personDeleteWeddingModalFactory = $personDeleteWeddingModalFactory;
        $this->personDeleteWeddingParentModalFactory = $personDeleteWeddingParentModalFactory;
        $this->personShowImageModalFactory = $personShowImageModalFactory;
    }

    /**
     * @return PersonAddAddressModalFactory
     */
    public function getPersonAddAddressModalFactory()
    {
        return $this->personAddAddressModalFactory;
    }

    /**
     * @return PersonAddBrotherModalFactory
     */
    public function getPersonAddBrotherModalFactory()
    {
        return $this->personAddBrotherModalFactory;
    }

    /**
     * @return PersonAddDaughterModalFactory
     */
    public function getPersonAddDaughterModalFactory()
    {
        return $this->personAddDaughterModalFactory;
    }

    /**
     * @return PersonAddFileModalFactory
     */
    public function getPersonAddFileModalFactory()
    {
        return $this->personAddFileModalFactory;
    }

    /**
     * @return PersonAddGenusModalFactory
     */
    public function getPersonAddGenusModalFactory()
    {
        return $this->personAddGenusModalFactory;
    }

    /**
     * @return PersonAddHusbandModalFactory
     */
    public function getPersonAddHusbandModalFactory()
    {
        return $this->personAddHusbandModalFactory;
    }

    /**
     * @return PersonAddParentPartnerFemaleModalFactory
     */
    public function getPersonAddParentPartnerFemaleModalFactory()
    {
        return $this->personAddParentPartnerFemaleModalFactory;
    }

    /**
     * @return PersonAddParentPartnerMaleModalFactory
     */
    public function getPersonAddParentPartnerMaleModalFactory()
    {
        return $this->personAddParentPartnerMaleModalFactory;
    }

    /**
     * @return PersonAddPartnerFemaleModalFactory
     */
    public function getPersonAddPartnerFemaleModalFactory()
    {
        return $this->personAddPartnerFemaleModalFactory;
    }

    /**
     * @return PersonAddPartnerMaleModalFactory
     */
    public function getPersonAddPartnerMaleModalFactory()
    {
        return $this->personAddPartnerMaleModalFactory;
    }

    /**
     * @return PersonAddPersonAddressModalFactory
     */
    public function getPersonAddPersonAddressModalFactory()
    {
        return $this->personAddPersonAddressModalFactory;
    }

    /**
     * @return PersonAddPersonJobModalFactory
     */
    public function getPersonAddPersonJobModalFactory()
    {
        return $this->personAddPersonJobModalFactory;
    }

    /**
     * @return PersonAddPersonNameModalFactory
     */
    public function getPersonAddPersonNameModalFactory()
    {
        return $this->personAddPersonNameModalFactory;
    }

    /**
     * @return PersonAddPersonSourceModalFactory
     */
    public function getPersonAddPersonSourceModalFactory()
    {
        return $this->personAddPersonSourceModalFactory;
    }

    /**
     * @return PersonAddSisterModalFactory
     */
    public function getPersonAddSisterModalFactory()
    {
        return $this->personAddSisterModalFactory;
    }

    /**
     * @return PersonAddSonModalFactory
     */
    public function getPersonAddSonModalFactory()
    {
        return $this->personAddSonModalFactory;
    }

    /**
     * @return PersonAddTownModalFactory
     */
    public function getPersonAddTownModalFactory()
    {
        return $this->personAddTownModalFactory;
    }

    /**
     * @return PersonAddWifeModalFactory
     */
    public function getPersonAddWifeModalFactory()
    {
        return $this->personAddWifeModalFactory;
    }

    /**
     * @return PersonDeleteBrotherModalFactory
     */
    public function getPersonDeleteBrotherModalFactory()
    {
        return $this->personDeleteBrotherModalFactory;
    }

    /**
     * @return PersonDeleteDaughterModalFactory
     */
    public function getPersonDeleteDaughterModalFactory()
    {
        return $this->personDeleteDaughterModalFactory;
    }

    /**
     * @return PersonDeleteFileModalFactory
     */
    public function getPersonDeleteFileModalFactory()
    {
        return $this->personDeleteFileModalFactory;
    }

    /**
     * @return PersonDeleteGenusModalFactory
     */
    public function getPersonDeleteGenusModalFactory()
    {
        return $this->personDeleteGenusModalFactory;
    }

    /**
     * @return PersonDeleteHistoryNoteModalFactory
     */
    public function getPersonDeleteHistoryNoteModalFactory()
    {
        return $this->personDeleteHistoryNoteModalFactory;
    }

    /**
     * @return PersonDeletePersonNameModalFactory
     */
    public function getPersonDeletePersonNameModalFactory()
    {
        return $this->personDeletePersonNameModalFactory;
    }

    /**
     * @return PersonDeletePersonAddressModalFactory
     */
    public function getPersonDeletePersonAddressModalFactory()
    {
        return $this->personDeletePersonAddressModalFactory;
    }

    /**
     * @return PersonDeletePersonFromEditModalFactory
     */
    public function getPersonDeletePersonFromEditModalFactory()
    {
        return $this->personDeletePersonFromEditModalFactory;
    }

    /**
     * @return PersonDeletePersonFromEditModalFactory
     */
    public function getPersonDeletePersonFromListModalFactory()
    {
        return $this->personDeletePersonFromListModalFactory;
    }

    /**
     * @return PersonDeletePersonJobModalFactory
     */
    public function getPersonDeletePersonJobModalFactory()
    {
        return $this->personDeletePersonJobModalFactory;
    }

    /**
     * @return PersonDeleteRelationModalFactory
     */
    public function getPersonDeleteRelationModalFactory()
    {
        return $this->personDeleteRelationModalFactory;
    }

    /**
     * @return PersonDeleteRelationParentModalFactory
     */
    public function getPersonDeleteRelationParentModalFactory()
    {
        return $this->personDeleteRelationParentModalFactory;
    }

    /**
     * @return PersonDeleteSisterModalFactory
     */
    public function getPersonDeleteSisterModalFactory()
    {
        return $this->personDeleteSisterModalFactory;
    }

    /**
     * @return PersonDeleteSonModalFactory
     */
    public function getPersonDeleteSonModalFactory()
    {
        return $this->personDeleteSonModalFactory;
    }

    /**
     * @return PersonDeleteSourceModalFactory
     */
    public function getPersonDeleteSourceModalFactory()
    {
        return $this->personDeleteSourceModalFactory;
    }

    /**
     * @return PersonDeleteWeddingModalFactory
     */
    public function getPersonDeleteWeddingModalFactory()
    {
        return $this->personDeleteWeddingModalFactory;
    }

    /**
     * @return PersonDeleteWeddingParentModalFactory
     */
    public function getPersonDeleteWeddingParentModalFactory()
    {
        return $this->personDeleteWeddingParentModalFactory;
    }

    /**
     * @return PersonShowImageModalFactory
     */
    public function getPersonShowImageModalFactory()
    {
        return $this->personShowImageModalFactory;
    }
}
