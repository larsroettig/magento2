<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Controller\Shared;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\Shared\Allcart;
use Magento\Wishlist\Controller\Shared\WishlistProvider;
use Magento\Wishlist\Model\ItemCarrier;
use Magento\Wishlist\Model\Wishlist;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AllcartTest extends TestCase
{
    /**
     * @var Allcart
     */
    protected $allcartController;

    /**
     * @var WishlistProvider|MockObject
     */
    protected $wishlistProviderMock;

    /**
     * @var Context|MockObject
     */
    protected $contextMock;

    /**
     * @var ItemCarrier|MockObject
     */
    protected $itemCarrierMock;

    /**
     * @var Wishlist|MockObject
     */
    protected $wishlistMock;

    /**
     * @var Http|MockObject
     */
    protected $requestMock;

    /**
     * @var ResultFactory|MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var Redirect|MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var Forward|MockObject
     */
    protected $resultForwardMock;

    protected function setUp(): void
    {
        $this->wishlistProviderMock = $this->getMockBuilder(WishlistProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemCarrierMock = $this->getMockBuilder(ItemCarrier::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->wishlistMock = $this->getMockBuilder(Wishlist::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultForwardMock = $this->getMockBuilder(Forward::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->allcartController = new Allcart(
            $this->contextMock,
            $this->itemCarrierMock,
            $this->wishlistProviderMock
        );
    }

    public function testExecuteWithWishlist()
    {
        $url = 'http://redirect-url.com';
        $quantity = 2;

        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->willReturn($this->wishlistMock);
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with('qty')
            ->willReturn($quantity);
        $this->itemCarrierMock->expects($this->once())
            ->method('moveAllToCart')
            ->with($this->wishlistMock, 2)
            ->willReturn($url);
        $this->resultRedirectMock->expects($this->once())
            ->method('setUrl')
            ->with($url)
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->allcartController->execute());
    }

    public function testExecuteWithNoWishlist()
    {
        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->willReturn(false);
        $this->resultForwardMock->expects($this->once())
            ->method('forward')
            ->with('noroute')
            ->willReturnSelf();

        $this->assertSame($this->resultForwardMock, $this->allcartController->execute());
    }
}
