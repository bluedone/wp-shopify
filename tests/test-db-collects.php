<?php

use WPS\Factories\DB_Collects_Factory;
use WPS\Utils;


/*

Tests the webhooks for Collects

Mock data comes from the "mock-data" folder and is taken directly from
the example output Shopify uses within their documentation found here:

https://help.shopify.com/api/reference/webhook

Collects are not updated -- only created or deleted

*/
class Test_DB_Collects extends WP_UnitTestCase {

  protected static $DB_Collects;
  protected static $mock_data_collect;
  protected static $mock_existing_collect_id;
  protected static $mock_product;
  protected static $mock_collect_insert;
  protected static $mock_collect_delete;
  protected static $lookup_key;

  static function wpSetUpBeforeClass() {

    // Assemble
    self::$DB_Collects                    = DB_Collects_Factory::build();
    self::$mock_product                   = json_decode( file_get_contents( dirname(__FILE__) . "/mock-data/_common/product.json") );
    self::$mock_data_collect              = json_decode( file_get_contents( dirname(__FILE__) . "/mock-data/collects/collect.json") );
    self::$mock_collect_insert            = json_decode( file_get_contents( dirname(__FILE__) . "/mock-data/collects/collects-insert.json") );
    self::$mock_collect_delete            = json_decode( file_get_contents( dirname(__FILE__) . "/mock-data/collects/collects-delete.json") );

    self::$mock_existing_collect_id       = 9488609378327;
    self::$lookup_key                     = self::$DB_Collects->lookup_key;

  }


  /*

  Mock: Product Create

  */
  function test_collect_create() {
    $result = self::$DB_Collects->insert(self::$mock_data_collect);
    $this->assertEquals(1, $result);

  }


  /*

  Mock: Product Delete

  */
  function test_collect_delete() {

    $results = self::$DB_Collects->delete_rows(self::$lookup_key, self::$mock_existing_collect_id);
    $this->assertEquals(1, $results);

  }


  /*

  Should find collects to insert based on mock product

  */
  function test_it_should_find_collects_to_insert() {

    $found_items_to_insert = self::$DB_Collects->gather_items_for_insertion(
      self::$DB_Collects->modify_options( self::$mock_collect_insert )
    );

    $this->assertCount(1, $found_items_to_insert);

  }


  /*

  Should find options to delete based on mock product

  */
  function test_it_should_find_collects_to_delete() {

    $found_items_to_delete = self::$DB_Collects->gather_items_for_deletion(
      self::$DB_Collects->modify_options( self::$mock_collect_delete )
    );

    $this->assertCount(1, $found_items_to_delete);

  }


  /*

  Should perform all three modifications: insert, update, delete

  */
  function test_it_should_modify_collects_from_shopify_product() {

    $results = self::$DB_Collects->modify_from_shopify( self::$DB_Collects->modify_options( self::$mock_product ) );

    // Check if any WP_Errors occured ...
    foreach ( Utils::flatten_array($results) as $result) {
      $this->assertNotWPError($result);
    }

    // Checks that the modification amounts matches mock data
    $this->assertCount(1, $results['created'][0]);
    $this->assertCount(1, $results['updated'][0]);
    $this->assertCount(1, $results['deleted'][0]);

  }


  /*

  Should find all products to delete based on mock product id

  */
  function test_it_should_delete_all_collects_by_product_id() {

    $delete_result = self::$DB_Collects->delete_collects_from_product_id(self::$mock_product->id);

    $this->assertEquals(1, $delete_result);

  }


  /*

  Should rename payload key to lookup key

  */
  function test_it_should_maybe_rename_to_lookup_key() {

    $rename_result = self::$DB_Collects->maybe_rename_to_lookup_key(self::$mock_collect_insert);

    $this->assertObjectHasAttribute(self::$DB_Collects->lookup_key, $rename_result);

  }


  /*

  It should return the complete table name with suffix as string

  */
  function test_it_should_get_table_name() {

    $table_name = self::$DB_Collects->get_table_name();

    $this->assertInternalType('string', $table_name );
    $this->assertEquals('wptests_wps_collects', $table_name );

  }


  /*

  It should return only the table name suffix as string

  */
  function test_it_should_get_table_name_suffix() {

    $table_name_suffix = self::$DB_Collects->table_name_suffix;

    $this->assertInternalType('string', $table_name_suffix );
    $this->assertEquals('wps_collects', $table_name_suffix );

  }


  /*

  It should update the current add to cart color

  */
  function test_it_should_have_default_values() {

    $this->assertObjectHasAttribute('default_collect_id', self::$DB_Collects);
    $this->assertObjectHasAttribute('default_product_id', self::$DB_Collects);
    $this->assertObjectHasAttribute('default_collection_id', self::$DB_Collects);
    $this->assertObjectHasAttribute('default_featured', self::$DB_Collects);
    $this->assertObjectHasAttribute('default_position', self::$DB_Collects);
    $this->assertObjectHasAttribute('default_sort_value', self::$DB_Collects);
    $this->assertObjectHasAttribute('default_created_at', self::$DB_Collects);
    $this->assertObjectHasAttribute('default_updated_at', self::$DB_Collects);

  }


  /*

  It should have table info props

  */
  function test_it_should_have_table_info_props() {

    $this->assertObjectHasAttribute('table_name_suffix', self::$DB_Collects);
    $this->assertObjectHasAttribute('table_name', self::$DB_Collects);
    $this->assertObjectHasAttribute('version', self::$DB_Collects);
    $this->assertObjectHasAttribute('primary_key', self::$DB_Collects);
    $this->assertObjectHasAttribute('lookup_key', self::$DB_Collects);
    $this->assertObjectHasAttribute('cache_group', self::$DB_Collects);
    $this->assertObjectHasAttribute('type', self::$DB_Collects);

  }


  /*

  It should update the current add to cart color

  */
  function test_it_should_match_default_values_and_cols_amount() {

    $cols = self::$DB_Collects->get_columns();
    $default_cols = self::$DB_Collects->get_column_defaults();

    $col_difference = array_diff_key($cols, $default_cols);

    $this->assertCount(1, $col_difference);
    $this->assertArrayHasKey('id', $col_difference);

  }



}
