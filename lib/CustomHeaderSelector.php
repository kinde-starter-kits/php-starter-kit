<?php

namespace OpenAPIServer;

use Kinde\KindeSDK\HeaderSelector;

/**
 * Custom HeaderSelector that fixes the parameter mismatch issue in the generated SDK
 */
class CustomHeaderSelector extends HeaderSelector
{
    /**
     * @param string[] $accept
     * @param string[]|string $contentTypes
     * @return array
     */
    public function selectHeaders($accept, $contentTypes)
    {
        // Fix: Ensure contentTypes is always an array
        if (!is_array($contentTypes)) {
            $contentTypes = [$contentTypes];
        }
        
        return parent::selectHeaders($accept, $contentTypes);
    }
} 