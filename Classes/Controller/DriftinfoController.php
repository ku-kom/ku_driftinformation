<?php

declare(strict_types=1);

/**
 * Render json from driftinfo: https://cms.ku.dk/system/driftinfo_xml.mason?xml=10&json=1
 */

namespace UniversityOfCopenhagen\KuDriftinformation\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class DriftinfoController extends ActionController
{
    /**
     * @var RequestFactory
     */

    protected RequestFactory $requestFactory;
    /**
     * Initiate the RequestFactory.
     */
    public function __construct(RequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    /**
     * Request url and return response to fluid template.
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $url = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('ku_driftinformation', 'uri');

        // Check if the url is valid
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            $message = sprintf('Feed URL is not valid "%s". Update extension settings.', $url);
            throw new \RuntimeException($message, 1320651278);
        }
       
        // Return response object
        if (isset($url)) {
            try {
                $response = $requestFactory->request($url, 'GET');
                // Get the content on a successful request
                
                if ($response->getStatusCode() === 200) {
                    // Check if response header is json
                    if (false !== strpos($response->getHeaderLine('Content-Type'), 'application/json')) {
                        // getContents() returns a string
                        $string = $response->getBody()->getContents();
  
                        // Decode string to json
                        $data = json_decode((string) $string, true);

                        $previous = $data['tidligere'];
                        $current = $data['aktuelle'];
                        $planned = $data['planlagte'];

                        if ($previous) {
                            $this->view->assign('previousinfo', $previous);
                        }
                        if ($current) {
                            $this->view->assign('currentinfo', $current);
                        }
                        if ($planned) {
                            $this->view->assign('plannedinfo', $planned);
                        }
                    
                        $contentObject = $this->configurationManager->getContentObject();
                        $this->view->assign('data', $contentObject->data);
                    }
                }
            } catch (\Exception $e) {
                // Display error message
                $this->addFlashMessage(
                    'Error: ' . $e->getMessage(),
                    '',
                    FlashMessage::ERROR,
                    false
                );
            }
        }
        
        return $this->htmlResponse();
    }
}
