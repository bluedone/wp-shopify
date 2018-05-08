<!--

Tab Content: Connect

-->
<div class="tab-content <?php echo $tab === false ? 'tab-content-active' : ''; ?> <?php echo $connected ? 'wps-connected' : ''; ?>" data-tab-content="tab-connect">

  <div class="wps-admin-section-heading-group wps-l-row wps-l-space-between">

    <h2 class="wps-admin-section-heading wps-l-box-2">
      <span class="dashicons dashicons-update"></span> <?php esc_html_e( 'Connect and Sync ', 'wp-shopify' ); ?>
    </h2>

    <h3 class="wps-status-heading wps-admin-section-heading wps-l-box-2"><?php esc_html_e( 'Status:', 'wp-shopify' ); ?>

      <?php if($connected) { ?>
        <span class="wps-status is-connected"><?php esc_html_e('Connected', 'wp-shopify' ); ?></span>
      <?php } else { ?>
        <span class="wps-status is-disconnected"><?php esc_html_e('Disconnected', 'wp-shopify' ); ?></span>
      <?php } ?>

    </h3>

  </div>

  <div class="wps-admin-section">

    <p><?php printf(__('Enter your Shopify private app API keys below. Need help finding these? Watch our <a href="%s" target="_blank"> video tutorial</a>!', 'wp-shopify'), esc_url('https://www.youtube.com/watch?v=lYm6G35e8sI'));  ?></p>

    <form method="post" name="cleanup_options" action="options.php" id="wps-connect" class="wps-admin-form">

      <?php

        settings_fields($this->config->settings_connection_option_name);
        do_settings_sections($this->config->settings_connection_option_name);

        // Grab all connection settings
        $connection = $this->config->wps_get_settings_connection();

      ?>

      <!--

      API Key

      -->
      <div class="wps-form-group">

        <h4><?php esc_attr_e('API Key', 'wp-shopify'); ?></h4>

        <input required <?php echo $connected ? 'disabled' : ''; ?> type="text" class="regular-text <?php echo $connected ? 'valid' : ''; ?>" id="<?php echo $this->config->settings_connection_option_name; ?>_api_key" name="api_key" value="<?php if(!empty($connection->api_key)) echo $connection->api_key; ?>" placeholder=""> <span class="wps-help-tip wps-help-tip-inline-no-position" title="<?php esc_attr_e( 'To generate an API key you must create a "Private App" within your Shopify account.', 'wp-shopify' ); ?>"></span><div class="wps-form-icon wps-animated"></div>

      </div>


      <!--

      Password

      -->
      <div class="wps-form-group">

        <h4><?php esc_attr_e('API Password', 'wp-shopify'); ?></h4>

        <input required <?php echo $connected ? 'disabled' : ''; ?> type="text" class="regular-text <?php echo $connected ? 'valid' : ''; ?>" id="<?php echo $this->config->settings_connection_option_name; ?>_password" name="password" autocomplete="off" value="<?php if(!empty($connection->password)) echo $connection->password; ?>" placeholder=""> <span class="wps-help-tip wps-help-tip-inline-no-position" title="<?php esc_attr_e( 'To generate an API Password you must create a "Private App" within your Shopify account.', 'wp-shopify' ); ?>"></span> <div class="wps-form-icon wps-animated"></div>

      </div>


      <!--

      Shared Secret

      -->
      <div class="wps-form-group">

        <h4><?php esc_attr_e('Shared Secret', 'wp-shopify'); ?></h4>

        <input required <?php echo $connected ? 'disabled' : ''; ?> type="text" class="regular-text <?php echo $connected ? 'valid' : ''; ?>" id="<?php echo $this->config->settings_connection_option_name; ?>_shared_secret" name="shared_secret" value="<?php if(!empty($connection->shared_secret)) echo $connection->shared_secret; ?>" placeholder=""> <span class="wps-help-tip wps-help-tip-inline-no-position" title="<?php esc_attr_e( 'To generate a Shared Secret you must create a "Private App" within your Shopify account. The Shared Secret is used to validate webhook requests and provide security for WP Shopify.', 'wp-shopify' ); ?>"></span> <div class="wps-form-icon wps-animated"></div>

      </div>


      <!--

      Storefront Access Token

      -->
      <div class="wps-form-group">

        <h4><?php esc_attr_e('Storefront Access Token', 'wp-shopify'); ?></h4>

        <input required <?php echo $connected ? 'disabled' : ''; ?> type="text" class="regular-text <?php echo $connected ? 'valid' : ''; ?>" id="<?php echo $this->config->settings_connection_option_name; ?>_js_access_token" name="js_access_token" value="<?php if(!empty($connection->js_access_token)) echo $connection->js_access_token; ?>" placeholder=""> <span class="wps-help-tip wps-help-tip-inline-no-position" title="<?php esc_attr_e( 'To generate a Storefront Access Token you must create a "Private App" within your Shopify account. The Storefront Access Token is used to create the front-end cart experience.', 'wp-shopify' ); ?>"></span><div class="wps-form-icon wps-animated"></div>

      </div>

      <!--

      My Shopify Domain

      -->
      <div class="wps-form-group">

        <h4><?php esc_attr_e( 'Domain', 'wp-shopify' ); ?></h4>
        <input required <?php echo $connected ? 'disabled' : ''; ?> type="text" class="regular-text <?php echo $connected ? 'valid' : ''; ?>" id="<?php echo $this->config->settings_connection_option_name; ?>_domain" name="domain" value="<?php if ( !empty($connection->domain) ) echo $connection->domain; ?>" placeholder="<?php esc_attr_e('shop.myshopify.com', 'wp-shopify' ); ?>" id="domain"> <span class="wps-help-tip wps-help-tip-inline" title="<?php esc_attr_e( 'example: yourshop.myshopify.com', 'wp-shopify' ); ?>"></span>
        <div class="wps-form-icon wps-animated"></div>

      </div>


      <!-- Nonce -->
      <input hidden type="text" class="regular-text" id="<?php echo $this->config->settings_connection_option_name; ?>_nonce_id" name="nonce" value="<?php echo wp_create_nonce( uniqid() ); ?>"/>

      <!-- App ID: -->
      <input hidden type="text" class="regular-text" id="<?php echo $this->config->settings_connection_option_name; ?>_app_id" name="app_id" value="6" />

      <!-- Webhook ID -->
      <input hidden type="text" class="regular-text" id="<?php echo $this->config->settings_connection_option_name; ?>_webhook_id" name="webhook_id" value="" />

      <!-- Shopify Access Token -->
      <input hidden type="text" class="regular-text" id="<?php echo $this->config->settings_connection_option_name; ?>_access_token" name="access_token" value="" />

      <!-- Submit -->
      <div class="wps-button-group button-group button-group-ajax">
        <?php if($connected) { ?>
          <?php submit_button(esc_html__('Disconnect your Shopify store', 'wp-shopify'), 'primary large', 'submitDisconnect', false, array()); ?>
        <?php } else { ?>
          <?php submit_button(esc_html__('Connect your Shopify store', 'wp-shopify'), 'primary large', 'submitConnect', false, array()); ?>
        <?php } ?>
        <div class="spinner"></div>

      </div>

    </form>

  </div>

</div>
