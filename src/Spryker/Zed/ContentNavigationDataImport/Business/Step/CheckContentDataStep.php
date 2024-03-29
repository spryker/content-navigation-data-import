<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\ContentNavigationDataImport\Business\Step;

use Generated\Shared\Transfer\ContentTransfer;
use Generated\Shared\Transfer\ContentValidationResponseTransfer;
use Spryker\Zed\ContentNavigationDataImport\Dependency\Facade\ContentNavigationDataImportToContentInterface;
use Spryker\Zed\DataImport\Business\Exception\InvalidDataException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class CheckContentDataStep implements DataImportStepInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'Failed to import content navigation: %s';

    /**
     * @var \Spryker\Zed\ContentNavigationDataImport\Dependency\Facade\ContentNavigationDataImportToContentInterface
     */
    protected $contentFacade;

    /**
     * @param \Spryker\Zed\ContentNavigationDataImport\Dependency\Facade\ContentNavigationDataImportToContentInterface $contentFacade
     */
    public function __construct(ContentNavigationDataImportToContentInterface $contentFacade)
    {
        $this->contentFacade = $contentFacade;
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @throws \Spryker\Zed\DataImport\Business\Exception\InvalidDataException
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $validationResult = $this->contentFacade->validateContent($this->createContentTransfer($dataSet));

        if (!$validationResult->getIsSuccess()) {
            $errorMessages = $this->getErrorMessages($validationResult);

            throw new InvalidDataException(
                sprintf(
                    static::ERROR_MESSAGE,
                    implode(';', $errorMessages),
                ),
            );
        }
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return \Generated\Shared\Transfer\ContentTransfer
     */
    protected function createContentTransfer(DataSetInterface $dataSet): ContentTransfer
    {
        return (new ContentTransfer())->setName($dataSet[ContentTransfer::NAME])
            ->setDescription($dataSet[ContentTransfer::DESCRIPTION])
            ->setKey($dataSet[ContentTransfer::KEY]);
    }

    /**
     * @param \Generated\Shared\Transfer\ContentValidationResponseTransfer $contentValidationResponseTransfer
     *
     * @return array<string>
     */
    protected function getErrorMessages(ContentValidationResponseTransfer $contentValidationResponseTransfer): array
    {
        $messages = [];
        foreach ($contentValidationResponseTransfer->getParameterMessages() as $parameterMessages) {
            foreach ($parameterMessages->getMessages() as $message) {
                $messages[] = '[' . $parameterMessages->getParameter() . '] ' . $message->getValue();
            }
        }

        return $messages;
    }
}
