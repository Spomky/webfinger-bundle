<?php

declare(strict_types=1);
use OAuth2Framework\Component\WebFingerEndpoint\IdentifierResolver\IdentifierResolverManager;
use OAuth2Framework\Component\WebFingerEndpoint\IdentifierResolver\UriResolver;
use OAuth2Framework\Component\WebFingerEndpoint\IdentifierResolver\AccountResolver;
use OAuth2Framework\Component\WebFingerEndpoint\IdentifierResolver\EmailResolver;

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2019 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use OAuth2Framework\Component\WebFingerEndpoint\IdentifierResolver;
use OAuth2Framework\Component\WebFingerEndpoint\WebFingerEndpoint;
use OAuth2Framework\WebFingerBundle\Middleware\Pipe;
use OAuth2Framework\WebFingerBundle\Service\RouteLoader;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return function (ContainerConfigurator $container) {
    $container = $container->services()->defaults()
        ->private()
        ->autoconfigure()
    ;

    $container->set(RouteLoader::class)
        ->tag('routing.loader')
        ->call('addRoute', [
            'webfinger_route',
            'webfinger_pipe',
            'dispatch',
            '%webfinger.path%',
            [], // defaults
            [], // requirements
            [], // options
            null, // host
            ['https'], // schemes
            ['GET'], // methods
            '', // condition
        ])
    ;

    $container->set(WebFingerEndpoint::class)
        ->args([
            ref(ResponseFactoryInterface::class),
            ref('webfinger.resource_repository'),
            ref(IdentifierResolverManager::class),
        ])
    ;

    $container->set('webfinger_pipe')
        ->class(Pipe::class)
        ->args([[
            ref(WebFingerEndpoint::class),
        ]])
        ->tag('controller.service_arguments')
    ;

    $container->set(IdentifierResolverManager::class);
    $container->set(UriResolver::class);
    $container->set(AccountResolver::class);
    $container->set(EmailResolver::class);
};
