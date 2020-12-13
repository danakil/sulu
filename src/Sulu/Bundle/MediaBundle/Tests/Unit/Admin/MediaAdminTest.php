<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\MediaBundle\Tests\Unit\Admin;

use PHPUnit\Framework\TestCase;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\MediaBundle\Admin\MediaAdmin;
use Sulu\Component\Localization\Manager\LocalizationManagerInterface;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;
use Sulu\Component\Webspace\Manager\WebspaceCollection;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Sulu\Component\Webspace\Security;
use Sulu\Component\Webspace\Webspace;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MediaAdminTest extends TestCase
{
    /**
     * @var ViewBuilderFactoryInterface
     */
    private $viewBuilderFactory;

    /**
     * @var SecurityCheckerInterface
     */
    private $securityChecker;

    /**
     * @var LocalizationManagerInterface
     */
    private $localizationManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var WebspaceManagerInterface
     */
    private $webspaceManager;

    /**
     * @var MediaAdmin
     */
    private $mediaAdmin;

    public function setUp(): void
    {
        $this->viewBuilderFactory = $this->prophesize(ViewBuilderFactoryInterface::class);
        $this->securityChecker = $this->prophesize(SecurityCheckerInterface::class);
        $this->localizationManager = $this->prophesize(LocalizationManagerInterface::class);
        $this->urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $this->webspaceManager = $this->prophesize(WebspaceManagerInterface::class);

        $this->mediaAdmin = new MediaAdmin(
            $this->viewBuilderFactory->reveal(),
            $this->securityChecker->reveal(),
            $this->localizationManager->reveal(),
            $this->urlGenerator->reveal(),
            $this->webspaceManager->reveal()
        );
    }

    public function testGetSecurityContexts()
    {
        $webspace1 = new Webspace();
        $security1 = new Security();
        $security1->setSystem('Webspace1');
        $webspace1->setSecurity($security1);

        $webspace2 = new Webspace();
        $security2 = new Security();
        $security2->setSystem('Webspace2');
        $webspace2->setSecurity($security2);
        $this->webspaceManager->getWebspaceCollection()->willReturn(new WebspaceCollection([$webspace1, $webspace2]));

        $this->assertEquals(
            [
                'Sulu' => [
                    'Media' => [
                        'sulu.media.collections' => [
                            'view',
                            'add',
                            'edit',
                            'delete',
                            'security',
                        ],
                        'sulu.media.system_collections' => [
                            'view',
                        ],
                    ],
                ],
                'Webspace1' => [
                    'Media' => [
                        'sulu.media.collections' => [
                            'view',
                        ],
                    ],
                ],
                'Webspace2' => [
                    'Media' => [
                        'sulu.media.collections' => [
                            'view',
                        ],
                    ],
                ],
            ],
            $this->mediaAdmin->getSecurityContexts()
        );
    }
}