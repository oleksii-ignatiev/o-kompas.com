<?php
get_header();

$end_registration = get_field('end_registration');
date_default_timezone_set('Europe/kiev');
$columns = get_field('entry_columns');
$group_number = 0;
$is_entry_closed = strtotime($end_registration) < time();

echo $is_entry_closed ? __('Registration is closed', TEXT_DOMAIN) : __('Registration will be closed on ', TEXT_DOMAIN). " {$end_registration} Kyyiv timezone";

_e('List of participants who entered the event', TEXT_DOMAIN);

if ($columns) : ?>
    <table class="entry-table"> <?php
        echo '<tr>';
        echo "<th>". __('Manage Entry', TEXT_DOMAIN) ."</th>";
        echo "<th>". __('User ID', TEXT_DOMAIN) ."</th>";
        foreach ($columns as $key => $column) :
            $title = $column->post_title;
            if ( $title == 'Группа' ) $group_number = $key + 3;
            echo "<th>{$title}</th>";    
        endforeach; 
        echo '</tr>';
        
        $entries = get_post_meta($post->ID, 'entry', true);
        
        if ( $entries ) :
            echo '<tr><td>';
            if ( user_can(wp_get_current_user()->ID,'edit_posts') && !$is_entry_closed ) echo get_edit_button($post->ID, $group_number);
            if ( user_can(wp_get_current_user()->ID,'delete_posts') && !$is_entry_closed ) echo get_delete_button($post->ID);
            echo '</td>';
            foreach ($entries as $entry) : 
                echo "<td>{$entry['id']}</td>";
                foreach ($columns as $column) :
                    if ( array_key_exists($column->post_title, $entry) ) :
                        echo "<td>{$entry[$column->post_title]}</td>";
                        else :
                            echo "<td>". get_user_entry_data($column->post_title). "</td>";
                        endif;
                    endforeach;
                endforeach;
                echo '</tr>';
            endif; ?>    
    </table>
<?php endif; ?>

<?php 

// echo $end_registration . '<br>' . strtotime($end_registration) . '<br>' . time(). '<br>' .date('Y-m-d H:i:s', time());
if ( !$is_entry_closed ) : ?>
    <form class="form-entry-myself" action="" method="POST">
        <input type="hidden" name="event_id" value="<?php echo $post->ID; ?>" />
        <input type="hidden" name="event_columns" value="<?php echo implode(',',array_map(function($column){return $column->post_title;}, $columns)); ?>" />
        <button type="submit" class="entry-myself">
            <?php _e('Individual Entry', TEXT_DOMAIN); ?>
        </button>
        <select name='group'>
            <?php
            // $current_category = '';
            $categories = get_field('event_categories');
            foreach ( $categories as $category ) {
                printf( '<option value="%1$s">%2$s</option>', $category, $category );
            }
            ?>
        </select>
        <div class="form-output"></div>
    </form> <?php _e('or', TEXT_DOMAIN); ?>
    <form action="" method="POST">
        <input type="hidden" name="event_id" value="<?php echo $post->ID; ?>" />
        <button type="submit" class="entry-club">
            <?php _e('Club Entry', TEXT_DOMAIN); ?>
        </button>    
    </form>
<?php endif; ?>
<?php
// echo do_shortcode('[contact-form-7 id="86" title="Registration"]');
get_footer();