<?php

/**
 * Copyright 2014 SURFnet bv
 *
 * Modifications copyright (C) 2017 Adactive SAS
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace AdactiveSas\Saml2BridgeBundle\Entity;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use SAML2\Configuration\PrivateKey as PrivateKey;

class HostedEntities
{
    /**
     * @var string
     */
    private $metadataRouteConfiguration;

    /**
     * @var HostedIdentityProvider
     */
    private $identityProvider;

    /**
     * @var array
     */
    private $identityProviderConfiguration;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param null $metadataRouteConfiguration
     * @param array $identityProviderConfiguration
     */
    public function __construct(
        RouterInterface $router,
        RequestStack $requestStack,
        $metadataRouteConfiguration = null,
        array $identityProviderConfiguration = null
    )
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->metadataRouteConfiguration = $metadataRouteConfiguration;
        $this->identityProviderConfiguration = $identityProviderConfiguration;
    }

    /**
     * @return string
     */
    public function getEntityId()
    {
        return $this->generateUrl($this->metadataRouteConfiguration);
    }

    /**
     * @return bool
     */
    public function hasIdentityProvider(){
        return $this->getIdentityProvider() !== null;
    }

    /**
     * @return null|HostedIdentityProvider
     */
    public function getIdentityProvider()
    {
        if (!empty($this->identityProvider)) {
            return $this->identityProvider;
        }

        if (!$this->identityProviderConfiguration['enabled']) {
            return null;
        }

        $configuration = $this->createStandardEntityConfiguration($this->identityProviderConfiguration);
        $configuration['ssoUrl'] = $this->generateUrl(
            $this->identityProviderConfiguration['sso_route']
        );
        $configuration['slsUrl'] = $this->generateUrl(
            $this->identityProviderConfiguration['sls_route']
        );
        $configuration['loginUrl'] = $this->generateUrl(
            $this->identityProviderConfiguration['login_route']
        );
        $configuration['logoutUrl'] = $this->generateUrl(
            $this->identityProviderConfiguration['logout_route']
        );

        return $this->identityProvider = new HostedIdentityProvider($configuration);
    }

    /**
     * @param array $entityConfiguration
     * @return array
     */
    private function createStandardEntityConfiguration($entityConfiguration)
    {
        $privateKey = new PrivateKey($entityConfiguration['private_key'], PrivateKey::NAME_DEFAULT);

        return [
            'entityId' => $this->getEntityId(),
            'certificateFile' => $entityConfiguration['public_key'],
            'privateKeys' => [$privateKey],
            'blacklistedAlgorithms' => [],
            'assertionEncryptionEnabled' => false
        ];
    }

    /**
     * @param string|array $routeDefinition
     * @return string
     */
    private function generateUrl($routeDefinition)
    {
        $route = is_array($routeDefinition) ? $routeDefinition['route'] : $routeDefinition;
        $parameters = is_array($routeDefinition) ? $routeDefinition['parameters'] : [];

        $context = $this->router->getContext();

        $context->fromRequest($this->requestStack->getMasterRequest());

        $url = $this->router->generate($route, $parameters, RouterInterface::ABSOLUTE_URL);

        $context->fromRequest($this->requestStack->getCurrentRequest());

        return $url;
    }
}
