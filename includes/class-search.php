<?php
if (!defined('ABSPATH')) exit;

class GNSA_Search {
    public static function context_from_query($query, $limit = 6) {
        $query = gnsa_sanitize_string($query, 200);
        $args = [
            's' => $query,
            'posts_per_page' => $limit,
            'post_type' => ['post','page'],
            'post_status' => 'publish',
        ];

        $q = new WP_Query($args);
        $chunks = [];
        $sources = [];
        while ($q->have_posts()) {
            $q->the_post();
            $title = get_the_title();
            $url = get_permalink();
            $excerpt = get_the_excerpt();
            $content = wp_strip_all_tags( get_the_content() );
            $text = $title . " — " . $excerpt . " " . $content;
            $text = preg_replace('/\s+/', ' ', $text);
            $chunks[] = mb_substr($text, 0, 1400);
            $sources[] = ['title' => $title, 'url' => $url];
        }
        wp_reset_postdata();

        return [
            'context' => $chunks ? implode("\n\n---\n\n", $chunks) : __('No relevant content found on this site.', 'gpt5-nano-site-assistant'),
            'sources' => $sources,
        ];
    }
}
