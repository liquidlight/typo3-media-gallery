<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Directive;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Mutation;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationCollection;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationMode;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Scope;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceScheme;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\UriValue;
use TYPO3\CMS\Core\Type\Map;

return Map::fromEntries(
    [
        Scope::frontend(),
        // NOTICE: When using `MutationMode::Set` existing declarations will be overridden
        new MutationCollection(
            // results in `default-src 'self'`
            new Mutation(MutationMode::Extend, Directive::ScriptSrc, SourceScheme::data, new UriValue('https://cdn.jsdelivr.net')),
            new Mutation(MutationMode::Extend, Directive::StyleSrc, SourceScheme::data, new UriValue('https://cdn.jsdelivr.net')),
            new Mutation(MutationMode::Extend, Directive::StyleSrcElem, SourceScheme::data, new UriValue('https://cdn.jsdelivr.net')),
        ),
    ]
);
