<?php
namespace wp2static;

//BORROWED FROM https://core.trac.wordpress.org/browser/trunk/wp-admin/includes/export.php

function export($args = array()) {
	global $wpdb, $post, $wp_post_types;

	$defaults = array(
        'content'    => 'all', 
        'author'     => false, 
        'category'   => false,
        'status'     => false
	);
	
    $args = wp_parse_args($args, $defaults);
    
    //error_log(print_r($wp_post_types, 1)); //TODO: REMOVE DEBUGGING
    
    $results = array();
    
	if ('all' != $args['content'] && post_type_exists($args['content'])) {
		$ptype = get_post_type_object($args['content']);
		if (!$ptype->can_export)
			$args['content'] = 'post';

		$where = $wpdb->prepare("{$wpdb->posts}.post_type = %s", $args['content']);
	} else {
		//limits the export to posts & pages (for now)
        //TODO: more configurable filtering!
        $post_types = get_post_types(array('can_export' => true, 'show_in_admin_bar' => true));
        
		$esses = array_fill(0, count($post_types), '%s');
		$where = $wpdb->prepare("{$wpdb->posts}.post_type IN (" . implode(',', $esses) . ')', $post_types);
	}

	if ($args['status'] && ('post' == $args['content'] || 'page' == $args['content'])) {
		$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_status = %s", $args['status']);
	} else {
		$where .= " AND {$wpdb->posts}.post_status != 'auto-draft'";
    }

	$join = '';
    if ($args['category'] && 'post' == $args['content']) {
		if ($term = term_exists($args['category'], 'category')) {
			$join = "INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)";
			$where .= $wpdb->prepare(" AND {$wpdb->term_relationships}.term_taxonomy_id = %d", $term['term_taxonomy_id']);
		}
	}

	if ('post' == $args['content'] || 'page' == $args['content']) {
		if ($args['author']) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_author = %d", $args['author']);
        }
	}
    
	// grab a snapshot of post IDs, just in case it changes during the export
	$post_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} $join WHERE $where");

	// get the requested terms ready, empty unless posts filtered by category or all content
	//TODO: add support for category/tag/taxonomy output
    /*
    $cats = $tags = $terms = array();
	if (isset($term) && $term) {
		$cat = get_term($term['term_id'], 'category');
		$cats = array($cat->term_id => $cat);
		unset($term, $cat);
	} else if ('all' == $args['content']) {
		$categories = (array) get_categories(array('get' => 'all'));
		$tags = (array) get_tags(array('get' => 'all'));

		$custom_taxonomies = get_taxonomies(array('_builtin' => false));
		$custom_terms = (array) get_terms($custom_taxonomies, array('get' => 'all'));

		// put categories in order with no child going before its parent
		while ($cat = array_shift($categories)) {
			if ($cat->parent == 0 || isset($cats[$cat->parent]))
				$cats[$cat->term_id] = $cat;
			else
				$categories[] = $cat;
		}

		// put terms in order with no child going before its parent
		while ($t = array_shift($custom_terms)) {
			if ($t->parent == 0 || isset($terms[$t->parent]))
				$terms[$t->term_id] = $t;
			else
				$custom_terms[] = $t;
		}

		unset($categories, $custom_taxonomies, $custom_terms);
	}
    */

    if ($post_ids) {
        global $wp_query;
        $wp_query->in_the_loop = true; // Fake being in the loop.

        // fetch 20 posts at a time rather than loading the entire table into memory
        while ($next_posts = array_splice($post_ids, 0, 50)) {
            $where = 'WHERE ID IN (' . join(',', $next_posts) . ')';
            $posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} $where");

            // Begin Loop
            foreach ($posts as $post) {
                setup_postdata($post);
                
                $results[] = get_permalink();
            }
        }
    }
    
    return $results;
}
