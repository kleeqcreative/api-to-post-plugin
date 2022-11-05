
<?php 

//This Requires ACF to Work Correctly

//Register the Post Type
add_action('init', 'register_brewery_cpt');
function register_brewery_cpt() {
    register_post_type( 'brewery', [
        'label' => 'Breweries',
        'public' => true,
        'capability_type' => 'post'
    ]);
}

//Set Up the WP Cron to Run The Function Once a Day
if ( ! wp_next_scheduled( 'update_brewery_list')) {
    wp_schedule_event( time(), 'daily', 'get_breweries_from_api' );
}

//Ajax Function Calls
add_action( 'wp_ajax_nopriv_get_breweries_from_api', 'get_breweries_from_api');
add_action( 'wp_ajax_get_breweries_from_api', 'get_breweries_from_api');



//The Actual Function To Get the Data And Pagination
function get_breweries_from_api() {

    $current_page = ( ! empty($_POST['current_page']) ) ? $_POST['current_page'] : 1;
    $breweries = [];

    $results = wp_remote_retrieve_body(
        wp_remote_get( 'https://api.openbrewerydb.org/breweries/?page=' . $current_page . '&per_page=50')
    );

    $results = json_decode($results);

    if( ! is_array($results) || empty($results) ) {
        return false;
    }

    $breweries[] = $results;


    foreach($breweries[0] as $brewery) {
        $brewery_slug = sanitize_title( $brewery->name . "-" . $brewery->id );

        $existing_brewery = get_page_by_path( $brewery_slug, 'OBJECT', 'brewery' );

        if($existing_brewery === null) {

            $inserted_brewery = wp_insert_post( [
                'post_name' => $brewery_slug,
                'post_title' => $brewery_slug,
                'post_type' => 'brewery',
                'post_status' => 'publish'
            ]);

            if( is_wp_error($inserted_brewery) ) {
                continue;
            }

            $fillable = [
                'field_6365939e250aa' => 'name',
                'field_636593af250ab' => 'brewery_type',
                'field_636593c3250ac' => 'street',
                'field_636593cd250ad' => 'city',
                'field_636593d4250ae' => 'state',
                'field_636593db250af' => 'postal_code',
                'field_636593e3250b0' => 'country', 
                'field_636593e9250b1' => 'longitude',
                'field_636593f2250b2' => 'latitude',
                'field_636593fb250b3' => 'phone',
                'field_636593ff250b4' => 'website_url',
                'field_63659408250b5' => 'updated_at'
            ];

            foreach($fillable as $key => $name) {
                update_field($key, $brewery->$name, $inserted_brewery);
            }

        } else {

            $existing_brewery_id = $existing_brewery->ID;
            $existing_brewery_timestamp = get_field('updated_at', $existing_brewery_id);

            if($brewery->update_at >= $existing_brewery_timestamp) {
                
                $fillable = [
                    'field_6365939e250aa' => 'name',
                    'field_636593af250ab' => 'brewery_type',
                    'field_636593c3250ac' => 'street',
                    'field_636593cd250ad' => 'city',
                    'field_636593d4250ae' => 'state',
                    'field_636593db250af' => 'postal_code',
                    'field_636593e3250b0' => 'country', 
                    'field_636593e9250b1' => 'longitude',
                    'field_636593f2250b2' => 'latitude',
                    'field_636593fb250b3' => 'phone',
                    'field_636593ff250b4' => 'website_url',
                    'field_63659408250b5' => 'updated_at'
                ];
    
                foreach($fillable as $key => $name) {
                    update_field($key, $brewery->$name, $existing_brewery_id);
                }

            }
        }

    }

    $current_page = $current_page + 1;
    wp_remote_post( admin_url('admin-ajax.php?action=get_breweries_from_api'), [
        'blocking' => false,
        'sslverify' => false,
        'body' => [
            'current_page' => $current_page
        ] 
    ]);
}


if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_6365939e77f5e',
        'title' => 'Brewery',
        'fields' => array(
            array(
                'key' => 'field_6365939e250aa',
                'label' => 'Name',
                'name' => 'name',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_636593af250ab',
                'label' => 'Brewery Type',
                'name' => 'brewery_type',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_636593c3250ac',
                'label' => 'Street',
                'name' => 'street',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_636593cd250ad',
                'label' => 'City',
                'name' => 'city',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_636593d4250ae',
                'label' => 'State',
                'name' => 'state',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_636593db250af',
                'label' => 'Postal Code',
                'name' => 'postal_code',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_636593e3250b0',
                'label' => 'Country',
                'name' => 'country',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_636593e9250b1',
                'label' => 'Longitude',
                'name' => 'longitude',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_636593f2250b2',
                'label' => 'Latitude',
                'name' => 'latitude',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_636593fb250b3',
                'label' => 'Phone',
                'name' => 'phone',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_636593ff250b4',
                'label' => 'Website',
                'name' => 'website_url',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_63659408250b5',
                'label' => 'Updated At',
                'name' => 'updated_at',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'brewery',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 1,
    ));
    
    endif;		

