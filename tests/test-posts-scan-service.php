<?php
/**
 * Class TestPostsScanService
 *
 * @package YTAHA\PostScanner
 */

use PHPUnit\Framework\TestCase;
use YTAHA\PostScanner\App\Services\Posts_Scan_Service;

/**
 * Test class for the Posts Scan Service.
 */
class TestPostsScanService extends TestCase
{
    /**
     * The instance of the Posts Scan Service.
     *
     * @var Posts_Scan_Service
     */
    protected $service;

    /**
     * Array to store IDs of test posts.
     *
     * @var array
     */
    protected $post_ids = [];

    /**
     * Setup method to initialize the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = (new Posts_Scan_Service())->init();
        $this->createTestPosts();
    }

    /**
     * Tear down method to clean up the test environment.
     */
    protected function tearDown(): void
    {
        $this->deleteTestPosts();
        parent::tearDown();
    }

    /**
     * Helper method to create test posts.
     */
    protected function createTestPosts()
    {
        // Create 3 test posts in the database.
        for ($i = 0; $i < 3; $i++) {
            $post_id = wp_insert_post(
                [
                'post_title'   => "Test Post $i",
                'post_content' => "Content for test post $i",
                'post_status'  => 'publish',
                'post_type'    => 'post',
                ]
            );
            if ($post_id) {
                $this->post_ids[] = $post_id;
            }
        }
    }

    /**
     * Helper method to delete test posts.
     */
    protected function deleteTestPosts()
    {
        // Delete the created test posts from the database.
        foreach ($this->post_ids as $post_id) {
            wp_delete_post($post_id, true);
        }
    }

    /**
     * Test method to verify if performing a scan updates post meta.
     */
    public function testPerformScanUpdatesPostMeta()
    {
        // Current MySQL Time
        $current_time = current_time('mysql');

        // Mock get_posts to return the created post IDs.
        add_filter(
            'pre_get_posts', function () {
                return $this->post_ids;
            }
        );

        $args = $this->service->get_scan_arguments();
        $this->service->perform_scan($args);

        foreach ($this->post_ids as $post_id) {
            $meta = get_post_meta($post_id, $args['meta_key'], true);
            $this->assertEquals($current_time, $meta);
        }

        // Remove the filter after the test
        remove_filter('pre_get_posts', '__return_null');
    }
}
