<?php

	namespace Habari;

	class ArchiveStatus extends Plugin {
		
		public function action_plugin_activation ( $file ) {
			
			Post::add_new_status( 'archive' );
			
		}
		
		public function action_plugin_deactivation ( $file ) {
			
			// get all posts marked with our custom status
			$posts = Posts::get( array( 'status' => Post::status( 'archive' ) ) );
			
			foreach ( $posts as $post ) {
				$post->status = Post::status('draft');
				$post->update();
			}
			
			Post::delete_post_status( 'archive' );
			
		}

		public function filter_post_actions ( $actions, $post ) {
			
			// if the post is not archived, add that option
			if ( $post->status != Post::status( 'archive' ) ) {
				$actions['archivestatus_archive']['label'] = _t( 'Archive', 'archivestatus' );
				$actions['archivestatus_archive']['title'] = _t( 'Archive this post', 'archivestatus' );
				$actions['archivestatus_archive']['url'] = 'javascript:itemManage.update(\'archivestatus_archive\', ' . $post->id . ');';
			}
			else {
				// otherwise, add a restore
				$actions['archivestatus_restore']['label'] = _t( 'Un-Archive', 'archivestatus' );
				$actions['archivestatus_restore']['title'] = _t( 'Un-Archive this post', 'archivestatus' );
				$actions['archivestatus_restore']['url'] = 'javascript:itemManage.update(\'archivestatus_restore\', ' . $post->id . ');';
			}
						
			return $actions;
			
		}
		
		public function filter_posts_manage_actions ( $actions ) {
			
			$actions['archivestatus_archive']['label'] = _t( 'Archive Selected', 'archivestatus' );
			$actions['archivestatus_archive']['title'] = _t( 'Archive Selected Entries', 'archivestatus' );
			$actions['archivestatus_archive']['action'] = 'itemManage.update(\'archivestatus_archive\'); return false;';
		
			$actions['archivestatus_restore']['label'] = _t( 'Un-Archive Selected', 'archivestatus' );
			$actions['archivestatus_restore']['title'] = _t( 'Un-Archive Selected Entries', 'archivestatus' );
			$actions['archivestatus_restore']['action'] = 'itemManage.update(\'archivestatus_restore\'); return false;';
			
			return $actions;
			
		}
		
		public function action_admin_entries_action ( $response, $action, $posts ) {
			
			$num_posts = 0;
			
			if ( $action == 'archivestatus_archive' ) {
				
				foreach ( $posts as $post ) {
					$post->status = Post::status('archive');
					$post->update();
					$num_posts++;
				}
				
				if ( $num_posts == count( $posts ) ) {
					$response->message = _n( _t( 'Archived %d post', array( $num_posts ), 'archivestatus' ), _t( 'Archived %d posts', array( $num_posts ), 'archivestatus' ), $num_posts );
				}
				else {
					$response->message = _t( 'You did not have permission to archive some posts.', 'archivestatus' );
				}
				
			}
			
			if ( $action == 'archivestatus_restore' ) {
				
				foreach ( $posts as $post ) {
					$post->status = Post::status('draft');
					$post->update();
					$num_posts++;
				}
				
				if ( $num_posts == count( $posts ) ) {
					$response->message = _n( _t( 'Restored %d post', array( $num_posts ), 'archivestatus' ), _t( 'Restored %d posts', array( $num_posts ), 'archivestatus' ), $num_posts );
				}
				else {
					$response->message = _t( 'You did not have permission to restore some posts.', 'archivestatus' );
				}
				
			}
						
		}
		
	}

?>