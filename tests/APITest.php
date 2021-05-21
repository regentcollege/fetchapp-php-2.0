<?php declare(strict_types=1);


use FetchApp\API\Currency;
use FetchApp\API\FetchApp;
use FetchApp\API\Product;
use FetchApp\API\FileDetail;
use FetchApp\API\OrderStatus;

use FetchApp\API\Order;
use FetchApp\API\OrderItem;
use PHPUnit\Framework\TestCase;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

final class APITest extends TestCase
{
    private static $fetch;

        public static function setUpBeforeClass(): void
    {
        self::$fetch = new FetchApp();
        self::$fetch->setAuthenticationKey($_ENV['FETCH_API_KEY']);
        self::$fetch->setAuthenticationToken($_ENV['FETCH_API_TOKEN']);
    }

    public static function tearDownAfterClass(): void
    {
        self::$fetch = null;
    }

    public function testClass(): void
    {
        $fetch = self::$fetch;
        $this->assertInstanceOf(
            FetchApp::class,
            $fetch
        );
    }

    public function testAccount(): void
    {
        $fetch = self::$fetch;

        $account = $fetch->getAccountDetails();//    That was easy!

        $this->assertSame((int)$_ENV['TEST_ACCOUNT_ID'], $account->getAccountID());
        $this->assertSame($_ENV['TEST_ACCOUNT_NAME'], $account->getAccountName());
        $this->assertSame($_ENV['TEST_ACCOUNT_BILLING_EMAIL'], $account->getBillingEmail());
        $this->assertSame($_ENV['TEST_ACCOUNT_EMAIL_ADDRESS'], $account->getEmailAddress());
        $this->assertSame($_ENV['TEST_ACCOUNT_URL'], $account->getURL());
        $this->assertNull($account->getItemDownloadLimit());
        $this->assertNull($account->getOrderExpirationInHours());
    }

    public function testOrders(): void
    {
        $fetch = self::$fetch;

        $orders = $fetch->getOrders(); // Grabs all orders (potentially HUGE!)

        $this->assertIsArray($orders);
        $this->assertNotEmpty($orders);

        $orders = $fetch->getOrders(OrderStatus::All, 10, 0); // Grabs orders of all types, 50 per page, page 4.
        $this->assertCount(10, $orders);

        $orders = $fetch->getOrders(OrderStatus::Expired); // Grabs all expired orders.
        $this->assertIsArray($orders);
        $this->assertNotEmpty($orders);

        $orders = $fetch->getOrders(OrderStatus::Open); // Grabs all open orders
        $this->assertIsArray($orders);
        $this->assertNotEmpty($orders);
    }

    public function testSingleOrder(): void
    {
        $fetch = self::$fetch;
        $order = $fetch->getOrderByID($_ENV['TEST_SINGLE_ORDER_ID']);

        $this->assertInstanceOf(
            Order::class,
            $order
        );

        $this->assertSame((int)$_ENV['TEST_SINGLE_ORDER_ID'], $order->getOrderId());
        $this->assertSame($_ENV['TEST_SINGLE_ORDER_VENDOR_ID'], $order->getVendorID());

        $order = $fetch->getOrder($_ENV['TEST_SINGLE_ORDER_VENDOR_ID']);
        $this->assertInstanceOf(
            Order::class,
            $order
        );

        $this->assertSame((int)$_ENV['TEST_SINGLE_ORDER_ID'], $order->getOrderId());
        $this->assertSame($_ENV['TEST_SINGLE_ORDER_VENDOR_ID'], $order->getVendorID());
    }

    // public function testCannotBeCreatedFromInvalidEmailAddress(): void
    // {
    //     $this->expectException(InvalidArgumentException::class);

    //     Email::fromString('invalid');
    // }

    // public function testCanBeUsedAsString(): void
    // {
    //     $this->assertEquals(
    //         'user@example.com',
    //         Email::fromString('user@example.com')
    //     );
    // }
}




// try{
//     // ACCOUNT
//     if(false):
//         $account = $fetch->getAccountDetails();//    That was easy!
//     // Let's write some of the available Data to the page!
//         var_dump($account);
//         echo $account->getAccountID();
//         echo $account->getAccountName();
//         echo $account->getBillingEmail();
//         echo $account->getEmailAddress();
//         echo $account->getURL();
//         echo $account->getItemDownloadLimit();
//         echo $account->getOrderExpirationInHours();
//     endif;