<?php

declare(strict_types=1);

namespace Supseven\Prefetch\DataProcessing;

use Doctrine\DBAL\Exception;
use Supseven\Prefetch\Enumerations\Eagerness;
use Supseven\Prefetch\Enumerations\Types;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\SiteFinder;
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
    public function __construct(protected ConnectionPool $connectionPool, protected SiteFinder $siteFinder)
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
            ->select('uid', 'slug', 'tx_prefetch_type', 'tx_prefetch_eagerness')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('tx_prefetch_enable', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $rootPageId = $cObj->getRequest()->getAttribute('site')->getRootPageId();
        $json = $this->convertArrayToSpeculationRules($pages, $rootPageId);
        $frontendController = $cObj->getRequest()->getAttribute('frontend.controller');
        $frontendController->additionalHeaderData['foobar'] = '<script type="speculationrules" nonce="' . $nonce . '">' . json_encode($json) . '</script>';

        return $processedData;
    }

    /**
     * Converts an array of items into a structured set of speculation rules.
     *
     * @param array $array The array containing items to be converted into speculation rules.
     * @param int $rootPageId The root page ID used to filter items based on their site context.
     * @return array Returns an array of speculation rules structured by type and eagerness.
     */
    private function convertArrayToSpeculationRules(array $array, int $rootPageId): array
    {
        $json = [];

        array_walk($array, function ($item) use (&$json, $rootPageId): void {
            $type = strtolower(Types::getHumanReadableName($item['tx_prefetch_type']));
            $eagerness = strtolower(Eagerness::getHumanReadableName($item['tx_prefetch_eagerness']));

            if ($this->siteFinder->getSiteByPageId($item['uid'])->getRootPageId() !== $rootPageId) {
                return;
            }

            $key = array_search($eagerness, array_column($json[$type] ?? [], 'eagerness'));
            $slug = $item['slug'] . ($item['slug'] !== '/' ? '/' : '');

            if ($key !== false) {
                $json[$type][$key]['urls'][] = $slug;
            } else {
                $json[$type][] = [
                    'urls'      => [$slug],
                    'eagerness' => $eagerness,
                ];
            }
        });

        return $json;
    }
}
