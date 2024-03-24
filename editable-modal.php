<?php
/*
Plugin Name: Simple Modal
Description: Allows users to edit modal content and specify class of buttons to open the modal.
*/

function editable_modal_settings_page() {
    $confirmation_message = isset( $_GET['settings-updated'] ) ? 'Updated successfully.' : '';
    ?>
    <div class="wrap">
        <h2>Edit Modal Content</h2>
        <?php if ( $confirmation_message ) : ?>
            <div id="confirmation" style="font-size: 1.5em;">
                <span style="color: green;">&#10004;</span> <?php echo esc_html( $confirmation_message ); ?>
            </div>
        <?php endif; ?>
        <p>Specify the content and appearance of the modal popup.</p>
        <form id="modal-settings-form" method="post" action="options.php">
            <?php settings_fields( 'editable_modal_settings_group' ); ?>
            <?php do_settings_sections( 'editable_modal_settings_group' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Modal Title</th>
                    <td><input type="text" name="modal_title" value="<?php echo get_option( 'modal_title' ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Modal Content</th>
                    <td><textarea name="modal_content" rows="5" cols="50"><?php echo get_option( 'modal_content' ); ?></textarea>
                        <p class="description">You can use shortcodes here.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Button Class</th>
                    <td><input type="text" name="button_class" value="<?php echo get_option( 'button_class' ); ?>" /></td>
                </tr>
            </table>
            <?php submit_button( 'Save Changes', 'primary', 'submit', true, array(
                'onclick' => 'showConfirmation();'
            ) ); ?>
        </form>
    </div>
    <script>
        function showConfirmation() {
            document.getElementById('confirmation').style.display = 'block';
        }
    </script>
    <?php
}

function editable_modal_register_settings() {
    register_setting( 'editable_modal_settings_group', 'modal_title' );
    register_setting( 'editable_modal_settings_group', 'modal_content' );
    register_setting( 'editable_modal_settings_group', 'button_class' );
}
add_action( 'admin_init', 'editable_modal_register_settings' );

function editable_modal_add_menu() {
    add_menu_page( 'Editable Modal Settings', 'Modal Settings', 'manage_options', 'editable_modal_settings', 'editable_modal_settings_page' );
}
add_action( 'admin_menu', 'editable_modal_add_menu' );


function editable_modal_display_modal() {
    $modal_title = get_option( 'modal_title' );
    $modal_content = get_option( 'modal_content' );
    $button_class = get_option( 'button_class' );
    ?>
    <div id="Modal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.6);">
      <div class="modal-content" style="margin: 5% auto; padding: 20px; width: 80%;">
        <span class="close" style="float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h2><?php echo esc_html( $modal_title ); ?></h2>
        <div><?php echo do_shortcode( $modal_content ); ?></div>
      </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    jQuery(document).ready(function($) {
        var modal = $('#Modal');
        var btn = $('button.<?php echo esc_attr( $button_class ); ?>');
        var span = modal.find('.close');
        function toggleModal() {
            modal.css('display', modal.css('display') === 'none' ? 'block' : 'none');
        }
        btn.on('click', toggleModal);
        span.on('click', toggleModal);
        $(window).on('click', function(event) {
            if (event.target == modal[0]) {
                toggleModal();
            }
        });
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'editable_modal_display_modal' );
