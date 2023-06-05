<?php
/**
 * Initializes a singleton instance of the plugin.
 *
 * @package WPGraphQL\RankMath
 */

namespace WPGraphQL\RankMath;

use RankMath\Helper as RMHelper;
use WPGraphQL\RankMath\Admin\Settings\Settings;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Helper\Helper;

if ( ! class_exists( 'WPGraphQL\RankMath\Main' ) ) :

	/**
	 * Class - Main
	 */
	final class Main {
		/**
		 * Class instances.
		 *
		 * @var ?self $instance
		 */
		private static $instance;

		/**
		 * Constructor
		 */
		public static function instance(): self {
			if ( ! isset( self::$instance ) || ! ( is_a( self::$instance, self::class ) ) ) {
				// You cant test a singleton.
				// @codeCoverageIgnoreStart .
				if ( ! function_exists( 'is_plugin_active' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				self::$instance = new self();
				self::$instance->includes();
				self::$instance->setup();
				// @codeCoverageIgnoreEnd
			}

			/**
			 * Fire off init action.
			 *
			 * @param self $instance the instance of the plugin class.
			 */
			do_action( 'graphql_seo_init', self::$instance );

			return self::$instance;
		}

		/**
		 * Includes the required files with Composer's autoload.
		 *
		 * @codeCoverageIgnore
		 */
		private function includes(): void {
			if ( defined( 'WPGRAPHQL_SEO_AUTOLOAD' ) && false !== WPGRAPHQL_SEO_AUTOLOAD && defined( 'WPGRAPHQL_SEO_PLUGIN_DIR' ) ) {
				require_once WPGRAPHQL_SEO_PLUGIN_DIR . 'vendor/autoload.php';
			}
		}

		/**
		 * Sets up the schema.
		 *
		 * @codeCoverageIgnore
		 */
		private function setup(): void {
			// // Setup boilerplate hook prefix.
			Helper::set_hook_prefix( 'graphql_seo' );

			// Force enable RankMath headless support.
			$enabled = RMHelper::get_settings( 'general.headless_support' );

			if ( empty( $enabled ) ) {
				$options                     = get_option( 'rank-math-options-general', [] );
				$options['headless_support'] = 'on';
				update_option( 'rank-math-options-general', $options );
			}

			// Setup plugin.
			CoreSchemaFilters::init();
			Settings::init();
			TypeRegistry::init();
		}

		/**
		 * Throw error on object clone.
		 * The whole idea of the singleton design pattern is that there is a single object
		 * therefore, we don't want the object to be cloned.
		 *
		 * @codeCoverageIgnore
		 *
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'The plugin Main class should not be cloned.', 'wp-graphql-rank-math' ), '0.0.1' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @codeCoverageIgnore
		 */
		public function __wakeup(): void {
			// De-serializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the plugin Main class is not allowed.', 'wp-graphql-rank-math' ), '0.0.1' );
		}
	}
endif;
