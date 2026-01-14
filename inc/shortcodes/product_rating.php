<?php
function product_rating_shortcode($atts) {
    // Extract shortcode attributes with a default fallback
    $atts = shortcode_atts(array(
        'id' => null
    ), $atts, 'product_rating');

    // Use global post if no ID is specified and ensure it's a product page
    if (null === $atts['id']) {
        global $post;
        if ('product' === $post->post_type) {
            $atts['id'] = $post->ID;
        } else {
            return ''; // Return empty if not a product page and no ID provided
        }
    }
    $product_id = (int) $atts['id'];

    $all_review_ids = get_comments([
        'post_id'    => $product_id,
        'status'     => 'approve',
        'type'       => 'review',
        'fields'     => 'ids',
        'meta_query' => [
            [
                'key'     => 'rating',
                'value'   => 0,
                'compare' => '>',
                'type'    => 'NUMERIC',
            ],
        ],
    ]);

    // Calculate average rating and count from these reviews
    $sum = 0;
    foreach ($all_review_ids as $cid) {
        $r = (int) get_comment_meta($cid, 'rating', true);
        if ($r > 0) {
            $sum += $r;
        }
    }

    $count = count($all_review_ids);
    $avg   = $count ? round($sum / $count, 2) : 0;

    // Normalize into the structure your template expects
    $review_data = [
        'review_count' => $count,
        'average_rate' => $avg,
    ];

    $output = '';
    if ($review_data['review_count'] > 0) {
        // Prepare the rating HTML with stars
        $output .= '<div class="rateMeta">';
        // $output .= '<span style="width:' . (($average / 5) * 100) . '%"><strong class="rating">' . $average . '</strong> ' . esc_html__(' / 5', 'woocommerce') . '</span>';
        $output .= '<img class="star" src="/wp-content/uploads/2024/03/5-stars-01-1.svg"><img class="star" src="/wp-content/uploads/2024/03/5-stars-01-1.svg"><img class="star" src="/wp-content/uploads/2024/03/5-stars-01-1.svg"><img class="star" src="/wp-content/uploads/2024/03/5-stars-01-1.svg"><img class="star" src="/wp-content/uploads/2024/03/5-stars-01-1.svg">';
        $output .= '<div class="rate">' . $review_data['average_rate'] . ' / 5</div>';
        // $output .= '<div class="star-rating" title="' . sprintf(esc_attr__('Rated %s out of 5', 'woocommerce'), $average) . '">';
        if ($review_data['review_count'] == 1) {
            $output .= '<div class="checker"><span class="check checkbelo"><img src="/wp-content/uploads/2025/05/checkGreen.svg"></span> ' . $review_data['review_count'] . ' ' . __('Verified reviews', 'nutrisslim-suiteV2') . '</div>';
        } elseif ($review_data['review_count'] == 2) {
            $output .= '<div class="checker"><span class="check checkbelo"><img src="/wp-content/uploads/2025/05/checkGreen.svg"></span> ' . $review_data['review_count'] . ' ' . __('Verified reviews', 'nutrisslim-suiteV2') . '</div>';
        } else {
            $output .= '<div class="checker"><span class="check checkbelo"><img src="/wp-content/uploads/2025/05/checkGreen.svg"></span> ' . $review_data['review_count'] . ' ' . _n('Verified reviews', 'Verified reviews', $review_data['review_count'], 'nutrisslim-suiteV2') . '</div>';
        }
        // $output .= '</div>';
        $output .= '</div>'; // Close .product-rating
    }


    if ($review_data['review_count'] > 0) {
        // $output .= '<span class="review-count">' . sprintf(_n('%s review', '%s reviews', $count, 'woocommerce'), $count) . '</span>';
    }

    return $output;
}
add_shortcode('product_rating', 'product_rating_shortcode');