<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceModalContainer.php
 * User: Tomáš Babický
 * Date: 19.03.2021
 * Time: 22:03
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Source\Container;


use Rendix2\FamilyTree\App\Controls\Modals\Source\Factory\SourceAddSourceTypeModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Source\Factory\SourceDeleteSourceFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Source\Factory\SourceDeleteSourceFromListModalFactory;

class SourceModalContainer
{
    /**
     * @var SourceAddSourceTypeModalFactory
     */
    private $sourceAddSourceTypeModalFactory;

    /**
     * @var SourceDeleteSourceFromEditModalFactory
     */
    private $sourceDeleteSourceFromEditModalFactory;

    /**
     * @var SourceDeleteSourceFromListModalFactory
     */
    private $sourceDeleteSourceFromListModalFactory;

    /**
     * SourceModalContainer constructor.
     *
     * @param SourceAddSourceTypeModalFactory $sourceAddSourceTypeModalFactory
     * @param SourceDeleteSourceFromEditModalFactory $sourceDeleteSourceFromEditModalFactory
     * @param SourceDeleteSourceFromListModalFactory $sourceDeleteSourceFromListModalFactory
     */
    public function __construct(
        SourceAddSourceTypeModalFactory $sourceAddSourceTypeModalFactory,
        SourceDeleteSourceFromEditModalFactory $sourceDeleteSourceFromEditModalFactory,
        SourceDeleteSourceFromListModalFactory $sourceDeleteSourceFromListModalFactory
    ) {
        $this->sourceAddSourceTypeModalFactory = $sourceAddSourceTypeModalFactory;
        $this->sourceDeleteSourceFromEditModalFactory = $sourceDeleteSourceFromEditModalFactory;
        $this->sourceDeleteSourceFromListModalFactory = $sourceDeleteSourceFromListModalFactory;
    }

    /**
     * @return SourceAddSourceTypeModalFactory
     */
    public function getSourceAddSourceTypeModalFactory()
    {
        return $this->sourceAddSourceTypeModalFactory;
    }

    /**
     * @return SourceDeleteSourceFromEditModalFactory
     */
    public function getSourceDeleteSourceFromEditModalFactory()
    {
        return $this->sourceDeleteSourceFromEditModalFactory;
    }

    /**
     * @return SourceDeleteSourceFromListModalFactory
     */
    public function getSourceDeleteSourceFromListModalFactory()
    {
        return $this->sourceDeleteSourceFromListModalFactory;
    }
}
