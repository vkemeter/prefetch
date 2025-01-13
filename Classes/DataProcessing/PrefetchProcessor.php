<?php

declare(strict_types=1);

namespace Supseven\Prefetch\DataProcessing;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\Exception\ContentRenderingException;

/**
 * PrefetchProcessor class is responsible for processing content object rendering
 * by generating a list of page URLs and appending a JSON-encoded script tag
 * to the additional header data of the frontend controller.
 */
readonly class PrefetchProcessor implements DataProcessorInterface
{
    /**
     * Constructor method.
     *
     * @param ConnectionPool $connectionPool The connection pool instance.
     */
    public function __construct(protected ConnectionPool $connectionPool)
    {
    }

    /**
     * Processes the content object rendering by building a list of page URLs and
     * appending a JSON-encoded script tag to the frontend controller's additional header data.
     *
     * @param ContentObjectRenderer $cObj The content object renderer instance.
     * @param array $contentObjectConfiguration The configuration array for the content object.
     * @param array $processorConfiguration The configuration array for the processor.
     * @param array $processedData The data that has been processed prior to this method.
     * @return array The processed data after applying additional processing.
     * @throws ContentRenderingException
     * @throws Exception
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        $nonce = $cObj->getRequest()->getAttribute('nonce')->value;
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $pages = $queryBuilder
            ->select('uid', 'slug')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('tx_prefetch_enable', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $urls = [];

        foreach ($pages as $page) {
            $urls[] = $page['slug'] . '/';
        }

        $frontendController = $cObj->getRequest()->getAttribute('frontend.controller');

        $json = [
            'prerender' => [
                [
                    'urls'      => $urls,
                    'eagerness' => $processorConfiguration['eagerness'] ?? 'moderate',
                ],
            ],
        ];

        $frontendController->additionalHeaderData['foobar'] = '<script type="speculationrules" nonce="' . $nonce . '">' . json_encode($json) . '</script>';

        return $processedData;
    }
}
