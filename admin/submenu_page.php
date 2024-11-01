<?php
/**
 * Creates the submenu page for the plugin.
 *
 * @package Custom_Admin_Settings
 */
 
/**
 * Creates the submenu page for the plugin.
 *
 * Provides the functionality necessary for rendering the page corresponding
 * to the submenu with which this page is associated.
 *
 * @package Custom_Admin_Settings
 */
class Submenu_Page {
 
        /**
     * This function renders the contents of the page associated with the Submenu
     * that invokes the render method. In the context of this plugin, this is the
     * Submenu class.
     */
    function username_markup() {
        ?>
        <input class = "admin-input" type="text" id="yesnology_username" name="yesnology_username" value="<?php echo esc_attr(get_option( 'yesnology_username' )) ?>">
        <?php
    }

    function password_markup() {
        ?>
        <input class = "admin-input" type="password" id="yesnology_password" name="yesnology_password" value="<?php echo esc_attr(get_option( 'yesnology_password' )) ?>">
        <?php
    }

    function clientId_markup() {
        ?>
        <input class = "admin-input"  type="hidden" id="yesnology_clientId" name="yesnology_clientId" value="yesnology.plugin.client">
        <?php
    }
    
    function secretKey_markup() {
        ?>
        <input class = "admin-input" type="password" id="yesnology_secretKey" name="yesnology_secretKey" value="<?php echo esc_attr(get_option( 'yesnology_secretKey' )) ?>">
        <?php
    }

    function companyId_markup() {
        ?>
        <input type="hidden" id="yesnology_companyId" name="yesnology_companyId" value="<?php echo esc_attr(get_option( 'yesnology_companyId' )) ?>">
        <?php
    }
    
    function tenatId_markup() {
        ?>
        <input type="hidden" id="yesnology_tenatId" name="yesnology_tenatId" value="<?php echo esc_attr(get_option( 'yesnology_tenatId' )) ?>">
        <?php
    }

    function confirmPage_markup() {
        if (get_option( 'yesnology_submit_confirm' )) echo wp_editor( wp_kses( get_option( 'yesnology_submit_confirm' ), 'post' ), 'yesnology_confirmDesign', array('textarea_name' => 'yesnology_submit_confirm')  );
            else echo wp_editor( 'Data sent successfully. Thank you!', 'yesnology_confirmDesign', array('textarea_name' => 'yesnology_submit_confirm')  );
    }

    function bodyColor_markup() {
        ?>
        <input type="color" id="yesnology_bodyColor" name="yesnology_bodyColor" value="<?php  if (get_option( 'yesnology_bodyColor' )) echo esc_attr(get_option( 'yesnology_bodyColor' )); else echo '#ffffff'; ?>">
        <button type="button" class="button button-primary" onclick="document.getElementById('yesnology_bodyColor').value = '#ffffff'">Reset</button>
        <?php
    }

    function textColor_markup() {
        ?>
        <input type="color" id="yesnology_textColor" name="yesnology_textColor" value="<?php if (get_option( 'yesnology_textColor' )) echo esc_attr(get_option( 'yesnology_textColor' )); else echo '#000000'; ?>">
        <button type="button" class="button button-primary" onclick="document.getElementById('yesnology_textColor').value = '#000000'">Reset</button>
        <?php
    }

    function buttonPosition_markup() {
        ?>
        <select id="yesnology_buttonPosition" name="yesnology_buttonPosition">
            <?php if (esc_attr(get_option( 'yesnology_buttonPosition' )) === "left") { ?>
                <option value="left" selected>Left</option>
            <?php }  else { ?>
                <option value="left">Left</option>
            <?php } ?>
            <?php if (esc_attr(get_option( 'yesnology_buttonPosition' )) === "center") { ?>
                <option value="center" selected>Center</option>
            <?php }  else { ?>
                <option value="center">Center</option>
            <?php } ?>
            <?php if (esc_attr(get_option( 'yesnology_buttonPosition' )) === "right") { ?>
                <option value="right" selected>Right</option>
            <?php }  else { ?>
                <option value="right">Right</option>
            <?php } ?>
        </select>
        <?php
    }

    function binder_markup($args) {
        ?>
        <select id="yesnology_binder" name="yesnology_binder">
            <option><?php _e( 'Select a binder', 'YesNology' ); ?></option>
          <?php 
          foreach ($args['option'] as $singleOption) {
        ?>
            <option value="<?php echo esc_attr($singleOption->binderId) ?>" ><?php echo esc_attr($singleOption->name) ?></option>
        <?php
          }
          ?>
          </select>
        <?php
    }

    function companySelect_markup($args) {
        ?>
        <select id="yesnology_companySelect" name="yesnology_companySelect">
            <option><?php _e( 'Select a company', 'YesNology' ); ?></option>
          <?php 
          foreach ($args['option'] as $key => $singleOption) {
            if (esc_attr($singleOption->name) === json_decode(get_option( 'yesnology_companySelect' ))->name) {
                ?>
                    <option value='<?php echo json_encode($singleOption) ?>' selected><?php echo esc_attr($singleOption->name) ?></option>
            <?php } else { ?>
                    <option value='<?php echo json_encode($singleOption) ?>'><?php echo esc_attr($singleOption->name) ?></option>
            <?php } ?>
        <?php
          }
          ?>
          </select>
        <?php
    }
    
    public function renderConfirmPage() {
        ?>
        <h1> <?php esc_html_e( 'Advanced settings', 'YesNology' ); ?> </h1>
        <form method="POST" action="options.php">
        <?php
        settings_fields( 'yesnology_design_submit' );
        do_settings_sections( 'yesnology_design_submit' );
        submit_button();
        ?>
        </form>
        <?php
        
    }

    public function render() {
        ?>
        <h1> <?php esc_html_e( 'Setup YesNology plugin', 'YesNology' ); ?> </h1>
        <form method="POST" action="options.php">
        <?php
        settings_fields( 'yesnology' );
        do_settings_sections( 'yesnology' );
        submit_button();
        ?>
        <div id="loginSuccess"></div>
        </form>
        <form method="POST" action="options.php">
        <?php
        settings_fields( 'yesnology_company' );
        do_settings_sections( 'yesnology_company' );
        submit_button();
        ?>
        </form>
        <?php
        settings_fields( 'yesnology_binder' );
        do_settings_sections( 'yesnology_binder' );
        ?>
        <div id="shortcode"></div>
        <?php
    }
}