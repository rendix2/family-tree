<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeModalContainer.php
 * User: Tomáš Babický
 * Date: 19.03.2021
 * Time: 22:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType\Container;

use Rendix2\FamilyTree\App\Controls\Modals\SourceType\Factory\SourceTypeAddSourceModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\SourceType\Factory\SourceTypeDeleteSourceModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\SourceType\Factory\SourceTypeDeleteSourceTypeFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\SourceType\Factory\SourceTypeDeleteSourceTypeFromListModalFactory;

/**
 * Class SourceTypeModalContainer
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\SourceType\Container
 */
class SourceTypeModalContainer
{
    /**
     * @var SourceTypeAddSourceModalFactory
     */
    private $sourceTypeAddSourceModalFactory;

    /**
     * @var SourceTypeDeleteSourceModalFactory
     */
    private $sourceTypeDeleteSourceModalFactory;

    /**
     * @var SourceTypeDeleteSourceTypeFromEditModalFactory
     */
    private $sourceTypeDeleteSourceTypeFromEditModalFactory;

    /**
     * @var SourceTypeDeleteSourceTypeFromListModalFactory
     */
    private $sourceTypeDeleteSourceTypeFromListModalFactory;

    /**
     * SourceTypeModalContainer constructor.
     *
     * @param SourceTypeAddSourceModalFactory $sourceTypeAddSourceModalFactory
     * @param SourceTypeDeleteSourceModalFactory $sourceTypeDeleteSourceModalFactory
     * @param SourceTypeDeleteSourceTypeFromEditModalFactory $sourceTypeDeleteSourceTypeFromEditModalFactory
     * @param SourceTypeDeleteSourceTypeFromListModalFactory $sourceTypeDeleteSourceTypeFromListModalFactory
     */
    public function __construct(
        SourceTypeAddSourceModalFactory $sourceTypeAddSourceModalFactory,
        SourceTypeDeleteSourceModalFactory $sourceTypeDeleteSourceModalFactory,
        SourceTypeDeleteSourceTypeFromEditModalFactory $sourceTypeDeleteSourceTypeFromEditModalFactory,
        SourceTypeDeleteSourceTypeFromListModalFactory $sourceTypeDeleteSourceTypeFromListModalFactory,
    ) {
        $this->sourceTypeAddSourceModalFactory = $sourceTypeAddSourceModalFactory;
        $this->sourceTypeDeleteSourceModalFactory = $sourceTypeDeleteSourceModalFactory;
        $this->sourceTypeDeleteSourceTypeFromEditModalFactory = $sourceTypeDeleteSourceTypeFromEditModalFactory;
        $this->sourceTypeDeleteSourceTypeFromListModalFactory = $sourceTypeDeleteSourceTypeFromListModalFactory;
    }

    /**
     * @return SourceTypeAddSourceModalFactory
     */
    public function getSourceTypeAddSourceModalFactory()
    {
        return $this->sourceTypeAddSourceModalFactory;
    }

    /**
     * @return SourceTypeDeleteSourceModalFactory
     */
    public function getSourceTypeDeleteSourceModalFactory()
    {
        return $this->sourceTypeDeleteSourceModalFactory;
    }

    /**
     * @return SourceTypeDeleteSourceTypeFromEditModalFactory
     */
    public function getSourceTypeDeleteSourceTypeFromEditModalFactory()
    {
        return $this->sourceTypeDeleteSourceTypeFromEditModalFactory;
    }

    /**
     * @return SourceTypeDeleteSourceTypeFromListModalFactory
     */
    public function getSourceTypeDeleteSourceTypeFromListModalFactory()
    {
        return $this->sourceTypeDeleteSourceTypeFromListModalFactory;
    }
}