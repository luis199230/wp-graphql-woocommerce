<?php

class ConnectionPaginationTest extends \Tests\WPGraphQL\WooCommerce\TestCase\WooGraphQLTestCase {

	public function toCursor( $id ) {
		if ( $id instanceof \WC_Product_Download ) {
			return base64_encode( 'arrayconnection:' . $id['id'] );
		}

		return base64_encode( 'arrayconnection:' . $id );
	}

    // tests
    public function testCouponsPagination() {
		$coupons = array(
            $this->factory->coupon->create(),
			$this->factory->coupon->create(),
			$this->factory->coupon->create(),
            $this->factory->coupon->create(),
			$this->factory->coupon->create(),
        );

        usort(
            $coupons,
            function( $key_a, $key_b ) {
				return $key_a < $key_b;
			}
        );

		$query = '
			query ($first: Int, $last: Int, $after: String, $before: String) {
				coupons(first: $first, last: $last, after: $after, before: $before) {
					nodes {
						databaseId
                    }
                    pageInfo {
                        hasPreviousPage
                        hasNextPage
                        startCursor
                        endCursor
                    }
                }
			}
        ';

        $this->loginAsShopManager();

        /**
		 * Assertion One
		 *
		 * Test "first" parameter.
		 */
        $variables = array( 'first' => 2 );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'coupons.pageInfo.hasPreviousPage', false ),
			$this->expectedObject( 'coupons.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'coupons.pageInfo.startCursor', $this->toCursor( $coupons[0] ) ),
			$this->expectedObject( 'coupons.pageInfo.endCursor', $this->toCursor( $coupons[1] ) ),
			$this->expectedObject( 'coupons.nodes.0.databaseId', $coupons[0] ),
			$this->expectedObject( 'coupons.nodes.1.databaseId', $coupons[1] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

        /**
		 * Assertion Two
		 *
		 * Test "after" parameter.
		 */
        $variables = array(
			'first' => 3,
			'after' => $this->toCursor( $coupons[1] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'coupons.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'coupons.pageInfo.hasNextPage', false ),
			$this->expectedObject( 'coupons.pageInfo.startCursor', $this->toCursor( $coupons[2] ) ),
			$this->expectedObject( 'coupons.pageInfo.endCursor', $this->toCursor( $coupons[4] ) ),
			$this->expectedObject( 'coupons.nodes.0.databaseId', $coupons[2] ),
			$this->expectedObject( 'coupons.nodes.1.databaseId', $coupons[3] ),
			$this->expectedObject( 'coupons.nodes.2.databaseId', $coupons[4] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

		/**
		 * Assertion Three
		 *
		 * Test "last" parameter.
		 */
		\WPGraphQL::set_is_graphql_request( true );
        $variables = array(
			'last'   => 2,
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'coupons.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'coupons.pageInfo.hasNextPage', false ),
			$this->expectedObject( 'coupons.pageInfo.startCursor', $this->toCursor( $coupons[3] ) ),
			$this->expectedObject( 'coupons.pageInfo.endCursor', $this->toCursor( $coupons[4] ) ),
			$this->expectedObject( 'coupons.nodes.0.databaseId', $coupons[3] ),
			$this->expectedObject( 'coupons.nodes.1.databaseId', $coupons[4] ),
		);

        //$this->assertQuerySuccessful( $response, $expected );

		/**
		 * Assertion Four
		 *
		 * Test "before" parameter.
		 */
        $variables = array(
			'last'   => 2,
			'before' => $this->toCursor( $coupons[3] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'coupons.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'coupons.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'coupons.pageInfo.startCursor', $this->toCursor( $coupons[1] ) ),
			$this->expectedObject( 'coupons.pageInfo.endCursor', $this->toCursor( $coupons[2] ) ),
			$this->expectedObject( 'coupons.nodes.0.databaseId', $coupons[1] ),
			$this->expectedObject( 'coupons.nodes.1.databaseId', $coupons[2] ),
		);

        //$this->assertQuerySuccessful( $response, $expected );
    }

    public function testProductsPagination() {
        $products = array(
            $this->factory->product->createSimple(),
            $this->factory->product->createSimple(),
            $this->factory->product->createSimple(),
            $this->factory->product->createSimple(),
            $this->factory->product->createSimple(),
        );

        usort(
            $products,
            function( $key_a, $key_b ) {
				return $key_a < $key_b;
			}
        );

		$query = '
			query ($first: Int, $last: Int, $after: String, $before: String) {
				products(first: $first, last: $last, after: $after, before: $before) {
					nodes {
						databaseId
                    }
                    pageInfo {
                        hasPreviousPage
                        hasNextPage
                        startCursor
                        endCursor
                    }
                }
			}
        ';

        /**
		 * Assertion One
		 *
		 * Test "first" parameter.
		 */
        $variables = array( 'first' => 2 );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'products.pageInfo.hasPreviousPage', false ),
			$this->expectedObject( 'products.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'products.pageInfo.startCursor', $this->toCursor( $products[0] ) ),
			$this->expectedObject( 'products.pageInfo.endCursor', $this->toCursor( $products[1] ) ),
			$this->expectedObject( 'products.nodes.0.databaseId', $products[0] ),
			$this->expectedObject( 'products.nodes.1.databaseId', $products[1] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

        /**
		 * Assertion Two
		 *
		 * Test "after" parameter.
		 */
        $variables = array(
			'first' => 3,
			'after' => $this->toCursor( $products[1] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'products.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'products.pageInfo.hasNextPage', false ),
			$this->expectedObject( 'products.pageInfo.startCursor', $this->toCursor( $products[2] ) ),
			$this->expectedObject( 'products.pageInfo.endCursor', $this->toCursor( $products[4] ) ),
			$this->expectedObject( 'products.nodes.0.databaseId', $products[2] ),
			$this->expectedObject( 'products.nodes.1.databaseId', $products[3] ),
			$this->expectedObject( 'products.nodes.2.databaseId', $products[4] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

		/**
		 * Assertion Three
		 *
		 * Test "last" parameter.
		 */
        $variables = array( 'last' => 2 );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'products.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'products.pageInfo.hasNextPage', false ),
			$this->expectedObject( 'products.pageInfo.startCursor', $this->toCursor( $products[3] ) ),
			$this->expectedObject( 'products.pageInfo.endCursor', $this->toCursor( $products[4] ) ),
			$this->expectedObject( 'products.nodes.0.databaseId', $products[3] ),
			$this->expectedObject( 'products.nodes.1.databaseId', $products[4] ),
		);

        //$this->assertQuerySuccessful( $response, $expected );

		/**
		 * Assertion Four
		 *
		 * Test "before" parameter.
		 */
        $variables = array(
			'last'   => 2,
			'before' => $this->toCursor( $products[3] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'products.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'products.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'products.pageInfo.startCursor', $this->toCursor( $products[1] ) ),
			$this->expectedObject( 'products.pageInfo.endCursor', $this->toCursor( $products[2] ) ),
			$this->expectedObject( 'products.nodes.0.databaseId', $products[1] ),
			$this->expectedObject( 'products.nodes.1.databaseId', $products[2] ),
		);

        //$this->assertQuerySuccessful( $response, $expected );
    }

    public function testOrdersPagination() {
		$this->loginAsShopManager();

		$query      = new \WC_Order_Query();
		$old_orders = $query->get_orders();
		foreach ( $old_orders as $order ) {
			$order->delete( true );
		}
		unset( $old_orders );
		unset( $query );

        $orders = array(
            $this->factory->order->createNew(),
			$this->factory->order->createNew(),
			$this->factory->order->createNew(),
            $this->factory->order->createNew(),
			$this->factory->order->createNew(),
        );

        usort(
            $orders,
            function( $key_a, $key_b ) {
				return $key_a < $key_b;
			}
        );

		$query = '
			query ($first: Int, $last: Int, $after: String, $before: String) {
				orders(first: $first, last: $last, after: $after, before: $before) {
					nodes {
						databaseId
                    }
                    pageInfo {
                        hasPreviousPage
                        hasNextPage
                        startCursor
                        endCursor
                    }
                }
			}
        ';

        /**
		 * Assertion One
		 *
		 * Test "first" parameter.
		 */
        $variables = array( 'first' => 2 );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'orders.pageInfo.hasPreviousPage', false ),
			$this->expectedObject( 'orders.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'orders.pageInfo.startCursor', $this->toCursor( $orders[0] ) ),
			$this->expectedObject( 'orders.pageInfo.endCursor', $this->toCursor( $orders[1] ) ),
			$this->expectedObject( 'orders.nodes.0.databaseId', $orders[0] ),
			$this->expectedObject( 'orders.nodes.1.databaseId', $orders[1] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

        /**
		 * Assertion Two
		 *
		 * Test "after" parameter.
		 */
        $variables = array(
			'first' => 3,
			'after' => $this->toCursor( $orders[1] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'orders.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'orders.pageInfo.hasNextPage', false ),
			$this->expectedObject( 'orders.pageInfo.startCursor', $this->toCursor( $orders[2] ) ),
			$this->expectedObject( 'orders.pageInfo.endCursor', $this->toCursor( $orders[4] ) ),
			$this->expectedObject( 'orders.nodes.0.databaseId', $orders[2] ),
			$this->expectedObject( 'orders.nodes.1.databaseId', $orders[3] ),
			$this->expectedObject( 'orders.nodes.2.databaseId', $orders[4] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

		/**
		 * Assertion Three
		 *
		 * Test "last" parameter.
		 */
        $variables = array( 'last' => 2 );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'orders.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'orders.pageInfo.hasNextPage', false ),
			$this->expectedObject( 'orders.pageInfo.startCursor', $this->toCursor( $orders[3] ) ),
			$this->expectedObject( 'orders.pageInfo.endCursor', $this->toCursor( $orders[4] ) ),
			$this->expectedObject( 'orders.nodes.0.databaseId', $orders[3] ),
			$this->expectedObject( 'orders.nodes.1.databaseId', $orders[4] ),
		);

        //$this->assertQuerySuccessful( $response, $expected );

        /**
		 * Assertion Four
		 *
		 * Test "before" parameter.
		 */
        $variables = array(
			'last'   => 2,
			'before' => $this->toCursor( $orders[3] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'orders.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'orders.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'orders.pageInfo.startCursor', $this->toCursor( $orders[1] ) ),
			$this->expectedObject( 'orders.pageInfo.endCursor', $this->toCursor( $orders[2] ) ),
			$this->expectedObject( 'orders.nodes.0.databaseId', $orders[1] ),
			$this->expectedObject( 'orders.nodes.1.databaseId', $orders[2] ),
		);

        //$this->assertQuerySuccessful( $response, $expected );
    }

    public function testRefundsPagination() {
        $order   = $this->factory->order->createNew();
        $refunds = array(
            $this->factory->refund->createNew( $order, array( 'amount' => 0.5 ) ),
			$this->factory->refund->createNew( $order, array( 'amount' => 0.5 ) ),
			$this->factory->refund->createNew( $order, array( 'amount' => 0.5 ) ),
            $this->factory->refund->createNew( $order, array( 'amount' => 0.5 ) ),
			$this->factory->refund->createNew( $order, array( 'amount' => 0.5 ) ),
        );

        usort(
            $refunds,
            function( $key_a, $key_b ) {
				return $key_a < $key_b;
			}
        );

		$query = '
			query ($first: Int, $last: Int, $after: String, $before: String) {
				refunds(first: $first, last: $last, after: $after, before: $before) {
					nodes {
						databaseId
                    }
                    pageInfo {
                        hasPreviousPage
                        hasNextPage
                        startCursor
                        endCursor
                    }
                }
			}
        ';

        $this->loginAsShopManager();

        /**
		 * Assertion One
		 *
		 * Test "first" parameter.
		 */
        $variables = array( 'first' => 2 );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'refunds.pageInfo.hasPreviousPage', false ),
			$this->expectedObject( 'refunds.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'refunds.pageInfo.startCursor', $this->toCursor( $refunds[0] ) ),
			$this->expectedObject( 'refunds.pageInfo.endCursor', $this->toCursor( $refunds[1] ) ),
			$this->expectedObject( 'refunds.nodes.0.databaseId', $refunds[0] ),
			$this->expectedObject( 'refunds.nodes.1.databaseId', $refunds[1] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

        /**
		 * Assertion Two
		 *
		 * Test "after" parameter.
		 */
        $variables = array(
			'first' => 3,
			'after' => $this->toCursor( $refunds[1] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'refunds.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'refunds.pageInfo.hasNextPage', false ),
			$this->expectedObject( 'refunds.pageInfo.startCursor', $this->toCursor( $refunds[2] ) ),
			$this->expectedObject( 'refunds.pageInfo.endCursor', $this->toCursor( $refunds[4] ) ),
			$this->expectedObject( 'refunds.nodes.0.databaseId', $refunds[2] ),
			$this->expectedObject( 'refunds.nodes.1.databaseId', $refunds[3] ),
			$this->expectedObject( 'refunds.nodes.2.databaseId', $refunds[4] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

		/**
		 * Assertion Three
		 *
		 * Test "last" parameter.
		 */
        $variables = array( 'last' => 2 );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'refunds.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'refunds.pageInfo.hasNextPage', false ),
			$this->expectedObject( 'refunds.pageInfo.startCursor', $this->toCursor( $refunds[3] ) ),
			$this->expectedObject( 'refunds.pageInfo.endCursor', $this->toCursor( $refunds[4] ) ),
			$this->expectedObject( 'refunds.nodes.0.databaseId', $refunds[3] ),
			$this->expectedObject( 'refunds.nodes.1.databaseId', $refunds[4] ),
		);

        //$this->assertQuerySuccessful( $response, $expected );

        /**
		 * Assertion Four
		 *
		 * Test "before" parameter.
		 */
        $variables = array(
			'last'   => 2,
			'before' => $this->toCursor( $refunds[3] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'refunds.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'refunds.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'refunds.pageInfo.startCursor', $this->toCursor( $refunds[1] ) ),
			$this->expectedObject( 'refunds.pageInfo.endCursor', $this->toCursor( $refunds[2] ) ),
			$this->expectedObject( 'refunds.nodes.0.databaseId', $refunds[1] ),
			$this->expectedObject( 'refunds.nodes.1.databaseId', $refunds[2] ),
		);

        //$this->assertQuerySuccessful( $response, $expected );
    }

    public function testCustomersPagination() {
        $some_customers = array(
            $this->factory->customer->create(),
            $this->factory->customer->create(),
            $this->factory->customer->create(),
            $this->factory->customer->create(),
            $this->factory->customer->create(),
        );

		$customers = get_users(
			array(
				'fields'  => 'id',
				'role'    => 'customer',
				'orderby' => 'user_login',
				'order'   => 'ASC',
			)
		);

		$customers = array_map( 'absint', $customers );

        $query = '
            query ($first: Int, $last: Int, $after: String, $before: String) {
                customers(first: $first, last: $last, after: $after, before: $before) {
                    nodes {
                        databaseId
						username
                    }
                    pageInfo {
                        hasPreviousPage
                        hasNextPage
                        startCursor
                        endCursor
                    }
                }
            }
        ';

        $this->loginAsShopManager();

        /**
		 * Assertion One
		 *
		 * Test "first" parameter.
		 */
        $variables = array( 'first' => 2 );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'customers.pageInfo.hasPreviousPage', false ),
			$this->expectedObject( 'customers.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'customers.nodes.0.databaseId', $customers[0] ),
			$this->expectedObject( 'customers.nodes.1.databaseId', $customers[1] ),
			$this->expectedObject( 'customers.pageInfo.startCursor', $this->toCursor( $customers[0] ) ),
			$this->expectedObject( 'customers.pageInfo.endCursor', $this->toCursor( $customers[1] ) ),
		);

        $this->assertQuerySuccessful( $response, $expected );

        /**
		 * Assertion Two
		 *
		 * Test "after" parameter.
		 */
        $variables = array(
			'first' => 3,
			'after' => $this->toCursor( $customers[1] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'customers.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'customers.pageInfo.startCursor', $this->toCursor( $customers[2] ) ),
			$this->expectedObject( 'customers.pageInfo.endCursor', $this->toCursor( $customers[4] ) ),
			$this->expectedObject( 'customers.nodes.0.databaseId', $customers[2] ),
			$this->expectedObject( 'customers.nodes.1.databaseId', $customers[3] ),
			$this->expectedObject( 'customers.nodes.2.databaseId', $customers[4] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

		/**
		 * Assertion Three
		 *
		 * Test "last" parameter.
		 */
        $variables = array( 'last' => 2 );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'customers.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'customers.pageInfo.hasNextPage', false ),
			$this->expectedObject( 'customers.pageInfo.startCursor', $this->toCursor( $customers[3] ) ),
			$this->expectedObject( 'customers.pageInfo.endCursor', $this->toCursor( $customers[4] ) ),
			$this->expectedObject( 'customers.nodes.0.databaseId', $customers[3] ),
			$this->expectedObject( 'customers.nodes.1.databaseId', $customers[4] ),
		);

        //$this->assertQuerySuccessful( $response, $expected );

        /**
		 * Assertion Four
		 *
		 * Test "before" parameter.
		 */
        $variables = array(
			'last'   => 2,
			'before' => $this->toCursor( $customers[3] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'customers.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'customers.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'customers.pageInfo.startCursor', $this->toCursor( $customers[1] ) ),
			$this->expectedObject( 'customers.pageInfo.endCursor', $this->toCursor( $customers[2] ) ),
			$this->expectedObject( 'customers.nodes.0.databaseId', $customers[1] ),
			$this->expectedObject( 'customers.nodes.1.databaseId', $customers[2] ),
		);

        //$this->assertQuerySuccessful( $response, $expected );
    }

    public function testDownloadableItemsPagination() {
        $customer_id         = $this->factory->customer->create();

		$downloads = array(
			$this->factory->product->createDownload(),
			$this->factory->product->createDownload(),
			$this->factory->product->createDownload(),
			$this->factory->product->createDownload(),
			$this->factory->product->createDownload(),

		);
        $products = array_map(
			function( $download ) {
				return $this->factory->product->createSimple(
					array(
						'downloadable' => true,
						'downloads'    => array( $download )
					)
				);
			},
			$downloads
		);

        $order_id = $this->factory->order->createNew(
            array(
                'status'      => 'completed',
                'customer_id' => $customer_id,
            ),
            array(
                'line_items' => array_map(
                    function( $product_id ) {
                        return array(
                            'product' => $product_id,
                            'qty'     => 1,
                        );
                    },
                    $products
                ),
            )
        );

		$order = \wc_get_order( $order_id );

        // Force download permission updated.
        wc_downloadable_product_permissions( $order_id, true );

        $query = '
            query ($first: Int, $last: Int, $after: String, $before: String) {
                customer {
                    orders {
                        nodes {
                            downloadableItems(first: $first, last: $last, after: $after, before: $before) {
                                nodes {
                                    product {
                                        databaseId
                                    }
                                }
                                pageInfo {
                                    hasPreviousPage
                                    hasNextPage
                                    startCursor
                                    endCursor
                                }
                            }
                        }

                    }
                }
            }
        ';

        $this->loginAs( $customer_id );

         /**
		 * Assertion One
		 *
		 * Test "first" parameter.
		 */
        $variables = array( 'first' => 2 );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.hasPreviousPage', false ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.startCursor', $this->toCursor( $downloads[0] ) ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.endCursor', $this->toCursor( $downloads[1] ) ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.nodes.0.product.databaseId', $products[0] ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.nodes.1.product.databaseId', $products[1] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

        /**
		 * Assertion Two
		 *
		 * Test "after" parameter.
		 */
        $variables = array(
			'first' => 3,
			'after' => $this->toCursor( $downloads[1] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.hasNextPage', false ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.startCursor', $this->toCursor( $downloads[2] ) ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.endCursor', $this->toCursor( $downloads[4] ) ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.nodes.0.product.databaseId', $products[2] ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.nodes.1.product.databaseId', $products[3] ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.nodes.2.product.databaseId', $products[4] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

		/**
		 * Assertion Three
		 *
		 * Test "last" parameter.
		 */
        $variables = array( 'last' => 2 );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.hasPreviousPage', true ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.hasNextPage', false ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.startCursor', $this->toCursor( $downloads[3] ) ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.endCursor', $this->toCursor( $downloads[4] ) ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.nodes.0.product.databaseId', $products[3] ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.nodes.1.product.databaseId', $products[4] ),
		);

        $this->assertQuerySuccessful( $response, $expected );

        /**
		 * Assertion Two
		 *
		 * Test "before" parameter.
		 */
        $variables = array(
			'last' => 3,
			'before' => $this->toCursor( $downloads[3] ),
		);
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = array(
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.hasPreviousPage', false ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.hasNextPage', true ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.startCursor', $this->toCursor( $downloads[0] ) ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.pageInfo.endCursor', $this->toCursor( $downloads[2] ) ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.nodes.0.product.databaseId', $products[0] ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.nodes.1.product.databaseId', $products[1] ),
			$this->expectedObject( 'customer.orders.nodes.0.downloadableItems.nodes.2.product.databaseId', $products[2] ),
		);

        $this->assertQuerySuccessful( $response, $expected );
    }
}
