<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\ContentNavigationDataImport\Business;

use Spryker\Zed\ContentNavigationDataImport\Business\Step\CheckContentDataStep;
use Spryker\Zed\ContentNavigationDataImport\Business\Step\CheckLocalizedContentNavigationTermStep;
use Spryker\Zed\ContentNavigationDataImport\Business\Step\ContentNavigationWriterStep;
use Spryker\Zed\ContentNavigationDataImport\Business\Step\PrepareLocalizedContentNavigationTermStep;
use Spryker\Zed\ContentNavigationDataImport\ContentNavigationDataImportDependencyProvider;
use Spryker\Zed\ContentNavigationDataImport\Dependency\Facade\ContentNavigationDataImportToContentInterface;
use Spryker\Zed\ContentNavigationDataImport\Dependency\Facade\ContentNavigationDataImportToContentNavigationFacadeInterface;
use Spryker\Zed\ContentNavigationDataImport\Dependency\Service\ContentNavigationDataImportToUtilEncodingInterface;
use Spryker\Zed\DataImport\Business\DataImportBusinessFactory;
use Spryker\Zed\DataImport\Business\Model\DataImporterInterface;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;

/**
 * @method \Spryker\Zed\ContentNavigationDataImport\ContentNavigationDataImportConfig getConfig()
 * @method \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetStepBrokerTransactionAware createTransactionAwareDataSetStepBroker($bulkSize = null)
 * @method \Spryker\Zed\DataImport\Business\Model\DataImporter getCsvDataImporterFromConfig(\Generated\Shared\Transfer\DataImporterConfigurationTransfer $dataImporterConfigurationTransfer)
 */
class ContentNavigationDataImportBusinessFactory extends DataImportBusinessFactory
{
    public function getContentNavigationDataImport(): DataImporterInterface
    {
        $dataImporter = $this->getCsvDataImporterFromConfig(
            $this->getConfig()->getContentNavigationDataImporterConfiguration(),
        );

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker->addStep($this->createCheckContentDataStep());
        $dataSetStepBroker->addStep($this->createAddLocalesStep());
        $dataSetStepBroker->addStep($this->createPrepareLocalizedContentNavigationTermStep());
        $dataSetStepBroker->addStep($this->createCheckLocalizedContentNavigationTermStep());
        $dataSetStepBroker->addStep($this->createContentNavigationWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    public function createPrepareLocalizedContentNavigationTermStep(): DataImportStepInterface
    {
        return new PrepareLocalizedContentNavigationTermStep();
    }

    public function createCheckLocalizedContentNavigationTermStep(): DataImportStepInterface
    {
        return new CheckLocalizedContentNavigationTermStep($this->getContentNavigationFacade());
    }

    public function createContentNavigationWriterStep(): DataImportStepInterface
    {
        return new ContentNavigationWriterStep($this->getUtilEncoding());
    }

    public function createCheckContentDataStep(): DataImportStepInterface
    {
        return new CheckContentDataStep($this->getContentFacade());
    }

    public function getContentNavigationFacade(): ContentNavigationDataImportToContentNavigationFacadeInterface
    {
        return $this->getProvidedDependency(ContentNavigationDataImportDependencyProvider::FACADE_CONTENT_NAVIGATION);
    }

    public function getContentFacade(): ContentNavigationDataImportToContentInterface
    {
        return $this->getProvidedDependency(ContentNavigationDataImportDependencyProvider::FACADE_CONTENT);
    }

    public function getUtilEncoding(): ContentNavigationDataImportToUtilEncodingInterface
    {
        return $this->getProvidedDependency(ContentNavigationDataImportDependencyProvider::SERVICE_UTIL_ENCODING);
    }
}
