<?php

if ( ! defined( 'ABSPATH' ) ) die( 'restricted access' );

if ( ! class_exists( 'Dashboard_Directory_Size_Dashboard_Widget' ) ) {

	class Dashboard_Directory_Size_Dashboard_Widget {

		static $version             = '2015-04-28-01';

		var $plugin_dir_url         = '';


		public function plugins_loaded( ) {

			add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widgets' ) );

		}


		public function register_dashboard_widgets() {

			// filterable
			$can_show_widget =  apply_filters( Dashboard_Directory_Size_Common::$plugin_name . '-can-show-widget', current_user_can( 'manage_options' ) );

			if ( $can_show_widget ) {
				wp_add_dashboard_widget( $this->plugin_name . '-dashboard-widget',
					__('Dashboard Directory Size', 'dashboard-directory-size' ),
					array( $this, 'dashboard_widget' )
				);
			}
		}


		public function dashboard_widget() {

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( Dashboard_Directory_Size_Common::$plugin_name . '-dashboard-widget', $this->plugin_dir_url. '/admin/js/dashboard-widget.js', array( 'jquery' ), self::$version, true );
			wp_enqueue_style( Dashboard_Directory_Size_Common::$plugin_name . '-dashboard-widget', $this->plugin_dir_url. '/admin/css/dashboard-widget.css', array( ), self::$version );

			?>
				<div class="inside">
					<?php $this->display_sizes_table(); ?>
					<p><a href="<?php echo admin_url( 'options-general.php?page=' . Dashboard_Directory_Size_Common::$plugin_name . '-settings' ); ?>"><?php _e( 'Settings', 'dashboard-directory-size' ); ?></a></p>
				</div>

			<?php
		}


		private function display_sizes_table() {


			$directories = apply_filters( Dashboard_Directory_Size_Common::$plugin_name . '-get-directories', array() );

			?>
				<table class="dashboard-directory-size-table">
					<thead>
						<tr>
							<th><?php _e( 'Name', 'dashboard-directory-size' ); ?></th>
							<th><?php _e( 'Path', 'dashboard-directory-size' ); ?></th>
							<th><?php _e( 'Size', 'dashboard-directory-size' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $this->display_size_rows( $directories ); ?>
					</tbody>
				</table>

			<?php
		}


		private function display_size_rows( $directories ) {
			foreach ( $directories as $directory ) {
				?>
					<tr>
						<td><?php echo esc_html( $directory['name'] ) ?></td>
						<td><?php $this->output_trimmed_path( $directory['path'] ) ?></td>
						<td><?php

							switch ( intval( $directory['size'] ) ) {
								case -1:
									_e( 'Error', 'dashboard-directory-size' );
									break;
								case 0;
									_e( 'Empty', 'dashboard-directory-size' );
									break;
								default:
									echo esc_html( size_format( $directory['size'] ) );
								break;
							}

						?></td>
					</tr>
				<?php
			}
		}


		private function output_trimmed_path( $path ) {
			$trim_size = 25;
			$trimmed = false;
			$full_path = $path;

			// if this is part of the install, remove the start to show relative path
			if ( stripos( $path , ABSPATH ) !== false ) {
				$path = substr( $path, strlen( ABSPATH ) );
			}

			// trim directory name
			if ( ! empty( $path ) && strlen( $path ) > $trim_size ) {
				$path = substr( $path, 0, $trim_size );
				$trimmed = true;
			}

			?>
				<span class="trimmed-path">
					<?php if ( $trimmed ) { ?><a class="trimmed-path-expand" href="#"><?php } ?><?php echo esc_html( $path ); ?><?php if ( $trimmed ) { ?>...<?php } ?><?php if ( $trimmed ) { ?></a><?php } ?>
				</span>
				<span class="full-path" style="display: none;">
					<?php echo esc_html( $full_path ); ?>
				</span>
			<?php

		}



	} // end class

}