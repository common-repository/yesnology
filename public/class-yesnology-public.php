<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://yesnology.com/
 * @since      1.0.0
 *
 * @package    Yesnology
 * @subpackage Yesnology/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the public-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
 *
 * @package    Yesnology
 * @subpackage Yesnology/public
 * @author     yesnology <zavaroni@yesnology.com>
 */
class Yesnology_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	private $plugin_prefix;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name      The name of the plugin.
	 * @param      string $plugin_prefix          The unique prefix of this plugin.
	 * @param      string $version          The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version ) {

		$this->plugin_name   = $plugin_name;
		$this->plugin_prefix = $plugin_prefix;
		$this->version = $version;
		$this->baseUrl = 'https://isp.yesnology.com';
		$this->baseUrlApi = 'https://api.yesnology.com';
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/yesnology-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . 'select2',  plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . 'toggle-switchy', plugin_dir_url( __FILE__ ) . 'css/toggle-switchy.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . 'intlTelInput', plugin_dir_url( __FILE__ ) . 'css/intlTelInput.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/yesnology-public.js', array('jquery'), $this->version, true );
		wp_enqueue_script( 'pdf-script',  plugin_dir_url( __FILE__ ) . 'js/pdf.min.js' );
		wp_enqueue_script( 'select-2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js' );
		wp_enqueue_script( 'intlTelInputJs', plugin_dir_url( __FILE__ ) . 'js/intlTelInput.min.js');
		if (wp_kses(get_option( 'yesnology_submit_confirm' ), 'strip')) wp_localize_script( $this->plugin_name, $this->plugin_name . 'object', array('ajaxurl' => get_site_url(), 'scegliRegione' => __('Choose a region...','YesNology'), 'scegliProvincia' => __('Choose a province...','YesNology'), 'country_id' => substr(get_bloginfo("language"), -2), 'blogLanguage' => substr(get_bloginfo("language"), 0, 2), 'languageId' => $this->getLanguage($this->getToken()), 'yesnologyConfirmPage' => wp_kses(get_option( 'yesnology_submit_confirm' ), 'post')) );
			else wp_localize_script( $this->plugin_name, $this->plugin_name . 'object', array('scegliRegione' => __('Choose a region...','YesNology'), 'scegliProvincia' => __('Choose a province...','YesNology'), 'ajaxurl' => get_site_url(), 'country_id' => substr(get_bloginfo("language"), -2), 'blogLanguage' => substr(get_bloginfo("language"), 0, 2), 'languageId' => $this->getLanguage($this->getToken()), 'yesnologyConfirmPage' => __('Data sent successfully. Thank you!', 'YesNology')) );
	}
	public function getToken(){
		$response = wp_remote_get( $this->baseUrl . "/connect/token",
		array(
			'method'      => 'POST',
			'headers'     => Array('Content-Type' => 'application/x-www-form-urlencoded'),
			'body'		  => "client_id=yesnology.plugin.client&client_secret="  . wp_kses(get_option( 'yesnology_secretkey' ), 'strip') . "&grant_type=password&username=" . wp_kses(get_option( 'yesnology_username' ), 'strip') . "&password=" . wp_kses(get_option( 'yesnology_password' ), 'strip') . "&scope=api_plugins_access"
		));
		return json_decode($response['body'])->access_token;
	}
	
	public function apiRoute() {
		register_rest_route( $this->plugin_name, '/region/(?P<id>\d+)', array(
		    'methods' => 'GET',
		    'callback' => function ($args) {
			$response = wp_remote_get( $this->baseUrlApi . '/api/plugins/countries/regions?countryId=' . $args['id'] . '&languageId='  . $this->getLanguage($this->getToken()),
				array(
					'method'      => 'GET',
					'headers'     => Array('Accept' => 'application/json', 'x-TenantId' => wp_kses(get_option( 'yesnology_tenatId' ), 'strip'), 'Authorization' => 'Bearer ' . $this->getToken()),
				));
			return json_decode($response['body']);
			}
		) );
		register_rest_route( $this->plugin_name, '/prov/(?P<countryid>\d+)/(?P<regionid>[a-zA-Z0-9-]+)', array(
			'methods' => 'GET',
			'callback' => function ($args) {
		    $response = wp_remote_get( $this->baseUrlApi . '/api/plugins/countries/regions/provinces?countryId=' . $args['countryid'] . '&regionCode=' . $args['regionid'] . '&languageId='  . $this->getLanguage($this->getToken()),
				array(
					'method'      => 'GET',
					'headers'     => Array('Accept' => 'application/json', 'x-TenantId' => wp_kses(get_option( 'yesnology_tenatId' ), 'strip'), 'Authorization' => 'Bearer ' . $this->getToken()),
				));
			  return json_decode($response['body']);
			  }
		  ) );
		  register_rest_route( $this->plugin_name, '/send_data', array(
			'methods' => 'POST',
			'callback' => function (WP_REST_Request $response) {
			foreach ($_POST['binderAnswerFieldDtos'] as $key => $singlePost) {
				if ($singlePost['binderFieldId'] !== "binderId") {
						$singleObj = new StdClass;
						$singleObj->binderFieldId = $singlePost['binderFieldId'];
						if (gettype($singlePost['Value']) === "array") {
							$singleObj->Value = '["';
							$singleObj->Value = $singleObj->Value . implode('","', $singlePost['Value']); 
							$singleObj->Value = $singleObj->Value . '"]';
						} else if ($singlePost['Value'] !== "") $singleObj->Value = $singlePost['Value'];
							else $singleObj->Value = null;
						$dataPost[] = $singleObj;
				}
			}
			$payload = json_encode( array( "languageId"=> 1, "binderAnswerFieldDtos" => $dataPost) );
			$response = wp_remote_get( $this->baseUrlApi . "/api/plugins/companies/" . wp_kses(get_option( 'yesnology_companyId' ), 'strip') . "/binders" . "/" . wp_kses($_POST['binderAnswerFieldDtos'][0]['Value'], 'strip') . "/fields",
				array(
					'method'      => 'POST',
					'headers'     => Array('Content-Type' => 'application/json', 'x-TenantId' => wp_kses(get_option( 'yesnology_tenatId' ), 'strip'), 'Authorization' => 'Bearer ' . $this->getToken()),
					'body'		  => $payload
				));	
			return $response['body'];
			}
		  ) );
	  }
	public function header_scripts(){
		?>
		<script>
	  function loadPdf(pdfData) {
		  pdfjsLib.GlobalWorkerOptions.workerSrc = '<?php echo plugin_dir_url( __FILE__ ) . 'js/pdf.worker.min.js'; ?>';

		  // Using DocumentInitParameters object to load binary data.
		  var loadingTask = pdfjsLib.getDocument({data: pdfData});
		  loadingTask.promise.then(function(pdf) {
			for (let index = 1; index <= pdf.numPages; index++) {
			  pdf.getPage(index).then(function(page) {
			  
			  var scale = 1.5;
			  var viewport = page.getViewport({scale: scale});

			  var canvas = document.createElement("canvas");
			  canvas.style.display = "block";
			  document.getElementById('pdf-privacy').appendChild(canvas);
			  var context = canvas.getContext('2d');
			  canvas.height = viewport.height;
			  canvas.width = viewport.width;

			  var renderContext = {
					canvasContext: context,
					viewport: viewport
			  };
			  var renderTask = page.render(renderContext);
			  renderTask.promise.then(function () {
					console.log('Page rendered');
			  });
				});

			}
	  }, function (reason) {
// PDF loading error
			console.error(reason);
	  });}
	  </script>
		<?php
	}

	private function getLanguage ($token) {
		if (isset($_GET['language'])) return $_GET['language'];
		$response = wp_remote_get( $this->baseUrlApi . '/api/plugins/languages?SearchString=' . substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2),
			array(
				'method'      => 'GET',
				'headers'     => Array('Accept' => 'application/json', 'x-TenantId' => wp_kses(get_option( 'yesnology_tenatId' ), 'strip'), 'Authorization' => 'Bearer ' . $token),
			));
		
		return json_decode($response['body'])[0]->languageId;
	}

	private function getNations($token) {
		$response = wp_remote_get( $this->baseUrlApi . '/api/plugins/countries?languageId=' . $this->getLanguage($token),
			array(
				'method'      => 'GET',
				'headers'     => Array('Accept' => 'application/json', 'x-TenantId' => wp_kses(get_option( 'yesnology_tenatId' ), 'strip'), 'Authorization' => 'Bearer ' . $token),
			));
		return json_decode($response['body']);
	}
	
	/**
	 * Example of Shortcode processing function.
	 *
	 * Shortcode can take attributes like [yesnology-shortcode attribute='123']
	 * Shortcodes can be enclosing content [yesnology-shortcode attribute='123']custom content[/yesnology-shortcode].
	 *
	 * @see https://developer.wordpress.org/plugins/shortcodes/enclosing-shortcodes/
	 *
	 * @since    1.0.0
	 * @param    array  $atts    ShortCode Attributes.
	 * @param    mixed  $content ShortCode enclosed content.
	 * @param    string $tag    The Shortcode tag.
	 */
	public function ynlgy_shortcode_func( $atts, $content = null, $tag ) {

		/**
		 * Combine user attributes with known attributes.
		 *
		 * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
		 *
		 * Pass third paramter $shortcode to enable ShortCode Attribute Filtering.
		 * @see https://developer.wordpress.org/reference/hooks/shortcode_atts_shortcode/
		 */
		
		$atts = shortcode_atts(
			array(
				'binder' => null,
			),
			$atts,
			$this->plugin_prefix . 'binder'
		);
		$token = $this->getToken();
		$response = wp_remote_get( $this->baseUrlApi . "/api/plugins/companies/" . wp_kses(get_option( 'yesnology_companyId' ), 'strip') . "/binders" . "/" . wp_kses($atts['binder'], 'strip') . "/" . "fields/" . $this->getLanguage($token),
			array(
				'method'      => 'GET',
				'headers'     => Array('Accept' => 'application/json', 'x-TenantId' => wp_kses(get_option( 'yesnology_tenatId' ), 'strip'), 'Authorization' => 'Bearer ' . $token),
			));
		$binders = $response['body'];
		/**
		 * Build our ShortCode output.
		 * Remember to wp_kses all user input.
		 *
		 * @see https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/
		*/ 
		
		$input = "<div class='yesnology-form-page' id='yesnology-form-page' style='";
		if (esc_attr(get_option( 'yesnology_bodyColor' ))) $input = $input . "background-color:" . esc_attr(get_option( 'yesnology_bodyColor' )) . "; ";
		if (esc_attr(get_option( 'yesnology_textColor' ))) $input = $input . "color:" . esc_attr(get_option( 'yesnology_textColor' )) . "; ";
		$input = $input . "'>";
		$input = $input . "<div class='yesnology-language-container'>";
		$input = $input . "<select class='selectLanguage' onchange='if (!isNaN(parseInt(this.value))) { const languageUrl = window.location.href.split(\"?\")[0] + \"?language=\" + parseInt(this.value); window.location = languageUrl; }'>";
		$input = $input . "<option>" . __('Select language...','YesNology') . "</option>";
		foreach (json_decode($binders)->availableLanguages as $singleLanguage) $input = $input . "<option value='" . wp_kses($singleLanguage->languageId, 'strip') . "'>" . wp_kses($singleLanguage->name, 'strip') . "</option>";
		$input = $input . "</select>";
		$input = $input . "</div>";
		$input = $input . "<h3 id='privacy-policy-title' class='privacy-policy-title'>" . wp_kses(json_decode($binders)->binderLanguage->description, 'strip') . "</h3>";
		$input = $input . wp_kses_post(json_decode($binders)->binderLanguage->privacyPolicy, 'strip');
		if (json_decode($binders)->binderLanguage->attachment) {
			$input = $input . '<script>var pdf = atob("' . json_decode($binders)->binderLanguage->attachment . '"); loadPdf(pdf); </script>';
			$input = $input . '<div id="pdf-privacy" class="pdfBorder pdfContent"></div>';
		}
		$input = $input . '<form class="yesnology-form" id="yesnology-form" method="post">';
		$input = $input . "<input name='binderId' type='hidden' id='binderId' value='" . wp_kses($atts['binder'], 'strip') . "'>";
		foreach (json_decode($binders)->binderFields as $key => $singleField) {
			switch ($singleField->templateFieldType) {
				case "1":
					$input = $input . "<p>";
					$input = $input . "<label ";
					if ($singleField->inputType === 14) $input = $input . "class='yesnology-label-color' ";
						else $input = $input . "class='yesnology-label' ";
					if ($singleField->title) $input = $input . "for='" . wp_kses($singleField->binderFieldId, 'strip') . "'>" . wp_kses($singleField->title, 'strip') . "</label>";
						else $input = $input = $input . "for='" . wp_kses($singleField->binderFieldId, 'strip') . "'>" . wp_kses($singleField->name, 'strip') . "</label>";
					if ($singleField->inputType == 16) {
						$args = array(
							'media_buttons' => false,
							'textarea_name' => wp_kses($singleField->binderFieldId),
							'textarea_rows' => '5'
						);
						ob_start();
  						wp_editor( '', 'recipecontenteditor', $args );
  						$input = $input . ob_get_clean();
					}
					else {
					if ($singleField->inputType == 15) $input = $input . "<textarea placeholder='" . wp_kses($singleField->placeHolder, 'strip') . "' rows='5'";
						else $input = $input . "<input id='" . wp_kses($singleField->binderFieldId, 'strip') . "' placeholder='" . wp_kses($singleField->placeHolder, 'strip') . "'";
					if ($singleField->inputType === 14) $input = $input . "class='yesnology-input form-control-color'";
						else if ($singleField->inputType === 13) $input = $input . "class='yesnology-input tel'";
							else $input = $input . "class='yesnology-input'";
					$input = $input . " name='" . wp_kses($singleField->binderFieldId, 'strip') . "'";
					if ($singleField->regex_Validator) $input = $input . " pattern='" . esc_attr($singleField->regex_Validator) . "'";
					if ($singleField->validationErrorText) $input = $input . ' title="' . wp_kses($singleField->validationErrorText, 'strip') . '"';
					if ($singleField->isRequired) $input = $input . " required ";
					if ($singleField->readOnly) $input = $input . " readonly ";
					if ($singleField->disabled) $input = $input . " disabled ";
					if ($singleField->width) $input = $input . " width='" . esc_attr($singleField->width) . "' ";
					if ($singleField->default_Value) $input = $input . " value='" . wp_kses($singleField->default_Value, 'strip') . "' ";
					if ($singleField->inputType)
						switch ($singleField->inputType) {
							case 1:
								$input = $input . " type='text' data-type='text'";
								break;
							case 2:
								$input = $input . " type='number' data-type='number'";
								break;
							case 3:
								$input = $input . " type='date' data-type='date'";
								break;
							case 5:
								$input = $input . " type='datetime-local' data-type='datetime-local'";
								break;
							case 6:
								$input = $input . " type='time' data-type='time'";
								break;
							case 7:
								$input = $input . " type='month' data-type='month'";
								break;
							case 8:
								$input = $input . " type='week' data-type='week'";
								break;
							case 9:
								$input = $input . " type='email' data-type='email'";
								break;
							case 10:
								$input = $input . " type='url' data-type='url'";
								break;
							case 11:
								$input = $input . " type='password' data-type='password'";
								break;
							case 12:
								$input = $input . " type='range' data-type='range'";
								if ($singleField->min) $input = $input . " min='" . esc_attr($singleField->min) . "' ";
								if ($singleField->max) $input = $input . " max='" . esc_attr($singleField->max) . "' ";
								if ($singleField->step) $input = $input . " step='" . esc_attr($singleField->step) . "' ";
								break;
							case 13:
								$input = $input . " type='tel' data-type='tel'";
								break;
							case 14:
								$input = $input . " type='color' data-type='color'";
								break;
						}
					$input = $input . ">";
					if ($singleField->inputType == 15) $input = $input . "</textarea>";
					}
					$input = $input . "<span class='yesnology-description'>" . wp_kses($singleField->description, 'strip') . "</span>";
					$input = $input . "</p>";
					break;
				case "2":
					$input = $input . "<p";
					if ($singleField->isRequired) $input = $input . " class='checkbox-group required' ";
					$input = $input . "><label class='yesnology-label' ";
					if ($singleField->title) $input = $input . "for='" . wp_kses($singleField->binderFieldId, 'strip') . "'>" . $singleField->title . "</label>";
						else $input = $input . "for='" . wp_kses($singleField->binderFieldId, 'strip') . "'>" . $singleField->name . "</label><br>";
					if (is_array(json_decode($singleField->jsonValues)))
					foreach (json_decode($singleField->jsonValues) as $key => $singlevalue) {
						$input = $input . "<input name='" . wp_kses($singleField->binderFieldId, 'strip') . "' class='yesnology-checkbox' type='checkbox' data-type='checkbox' id='" . wp_kses($singlevalue->v, 'strip') . "' value='" . wp_kses($singlevalue->v, 'strip') . "'";
						if ($singleField->default_Value_For_Boolean === 1) $input = $input . " checked ";
						$input = $input . ">";
						$input = $input . "<span class='yesnology-description'>" . wp_kses($singlevalue->d, 'strip') . "</span><br/>";
					}
					$input = $input . "<span class='checkbox-group-error'>" . __('Select at least one item!','YesNology') . "</span>";
					$input = $input . "</p>";
					break;
				case "3":
					$input = $input . "<p><label class='yesnology-label' ";
					if ($singleField->title) $input = $input . "for='" . wp_kses($singleField->binderFieldId, 'strip') . "'>" . wp_kses($singleField->title, 'strip') . "</label>";
						else $input = $input . "for='" . wp_kses($singleField->binderFieldId, 'strip') . "'>" . wp_kses($singleField->name, 'strip') . "</label><br>";
					if (is_array(json_decode($singleField->jsonValues)))
					foreach (json_decode($singleField->jsonValues) as $key => $singlevalue) {
						$input = $input . "<input name='" . wp_kses($singleField->binderFieldId, 'strip') . "' class='yesnology-radio' type='radio' data-type='radio' id='" . $singlevalue->v . "' value='" . $singlevalue->v . "'";
						if ($singleField->default_Value === $singlevalue->v) $input = $input . " checked ";
						if ($singleField->isRequired) $input = $input . " required ";
						$input = $input . ">";
						$input = $input . "<label class='yesnology-description' for='" . wp_kses($singlevalue->v, 'strip') . "'>" . wp_kses($singlevalue->d, 'strip') . "</label><br/>";
					}
					$input = $input . "</p>";
					break;
				case "4":
					if ($singleField->templateFieldId === 341) $input = $input . "<p class='regions' style='display: block'>";
						elseif ($singleField->templateFieldId === 342) $input = $input . "<p class='prov' style='display: block'>";
							else $input = $input . "<p>";
					if ($singleField->title) $input = $input . "<label class='yesnology-label' for='" . wp_kses($singleField->binderFieldId) . "'>" . wp_kses_title($singleField->title) . "</label>";
						else $input = $input . "<label class='yesnology-label' for='" . wp_kses($singleField->binderFieldId, 'strip') . "'>" . wp_kses($singleField->name, 'strip') . "</label>";
					if ($singleField->templateFieldId === 340)
						$input = $input . "<select name='" . wp_kses($singleField->binderFieldId, 'strip') . "' class='yesnology-select-nations' id='" . wp_kses($singleField->binderFieldId, 'strip') . "'";
						elseif ($singleField->templateFieldId === 341)
							$input = $input . "<select name='" . wp_kses($singleField->binderFieldId, 'strip') . "' class='yesnology-select-regions' id='" . wp_kses($singleField->binderFieldId, 'strip') . "'";
							elseif ($singleField->templateFieldId === 342) $input = $input . "<select name='" . wp_kses($singleField->binderFieldId, 'strip') . "' class='yesnology-select-prov' id='" . wp_kses($singleField->binderFieldId, 'strip') . "'";
								else $input = $input . "<br><select name='" . wp_kses($singleField->binderFieldId, 'strip') . "' id='" . wp_kses($singleField->binderFieldId, 'strip') . "'";
					if ($singleField->isRequired && $singleField->templateFieldId !== 341 && $singleField->templateFieldId !== 342) $input = $input . " required ";
					if ($singleField->width) $input = $input . " width='" . esc_attr($singleField->width) . "' "; 
					$input = $input . ">";
					if ($singleField->templateFieldId === 340) {
						$input = $input . "<option value=''>" . __('Choose the country...','YesNology') . "</option>";
						foreach ($this->getNations($token) as $key => $singlenation)
							$input = $input . "<option id='" . wp_kses($singlenation->name, 'strip') . "' value='" . wp_kses($singlenation->countryId, 'strip') . "'>" . wp_kses($singlenation->name, 'strip') . "</option>";
					}
					if (json_decode($singleField->jsonValues) !== null)
						foreach (json_decode($singleField->jsonValues) as $key => $singlevalue) {
							if ($singlevalue->v === null) $input = $input . "<option value=''>" . __('Choose an option...','YesNology') . "</option>";
								else $input = $input . "<option id='" . wp_kses($singlevalue->v, 'strip') . "' value='" . wp_kses($singlevalue->v, 'strip') . "'";
								if ($singleField->default_Value === $singlevalue->v) $input = $input . " selected='selected' ";
								$input = $input . "'>" . wp_kses($singlevalue->d, 'strip') . "</option>";
						}
					$input = $input . "</select>";
					if ($singleField->templateFieldId === 341) $input = $input . '<div class="loader" id="loader-regions"></div>';
					if ($singleField->templateFieldId === 342) $input = $input . '<div class="loader" id="loader-prov"></div>';
					$input = $input . "<span class='yesnology-description'>" . wp_kses($singleField->description, 'strip') . "</span>";
					$input = $input . "</p>";
					break;
				case "5":
					if ($singleField->default_Value_For_Boolean === 2) {
						$input = $input . '<p>
						<label class="toggle-switchy" id="toggle-switchy' . wp_kses($singleField->binderFieldId, 'strip') . '" for="switch' . wp_kses($singleField->binderFieldId, 'strip') . '" data-size="xl" style="display: none">
							<input class="toggle-switchy-input" name="' . wp_kses($singleField->binderFieldId, 'strip') . '" type="checkbox" id="switch' . wp_kses($singleField->binderFieldId, 'strip') . '" value=true>
							<span class="toggle" id="toggle' . wp_kses($singleField->binderFieldId, 'strip') . '" data-content-on="' . wp_kses($singleField->labelTrue, 'strip') . '" data-content-off="' . wp_kses($singleField->labelFalse, 'strip') . '">
								<span class="switch" id="switch' . wp_kses($singleField->binderFieldId, 'strip') . '"></span>
							</span>
						</label>';
						$input = $input . "<span class='yesnology-select-toggle-container' id='yesnology-select-toggle-container" . wp_kses($singleField->binderFieldId, 'strip') . "'>";
						$input = $input . "<select class='yesnology-select-toggle' id='" . wp_kses($singleField->binderFieldId, 'strip') . "'";
						if ($singleField->isRequired) $input = $input . " required ";
						if ($singleField->width) $input = $input . " width='" . esc_attr($singleField->width) . "' "; 
						$input = $input . ">";
						$input = $input . "<option value=''> ... </option>";
						$input = $input . "<option id='" . wp_kses($singleField->valueTrue, 'strip') . "' value='1' data-true='" . wp_kses($singleField->valueTrue, 'strip') . "'>" . wp_kses($singleField->labelTrue, 'strip') . "</option>";
						$input = $input . "<option id='" . wp_kses($singleField->valueFalse, 'strip') . "'  data-false='" . wp_kses($singleField->valueFalse, 'strip') . "' value='2'>" . wp_kses($singleField->labelFalse, 'strip') . "</option>";
						$input = $input . "</select>";
						$input = $input . "</span>";
						$input = $input . "<label class='yesnology-label-toggle' for='" . wp_kses($singleField->binderFieldId, 'strip') . "'>" . wp_kses($singleField->title, 'strip') . "</label>";
						$input = $input . "<br>";
						$input = $input . "<span class='yesnology-description'>" . wp_kses($singleField->description, 'strip') . "</span>";
						$input = $input . "</p>";
					}
					else if (($singleField->templateFieldId > 10 && $singleField->templateFieldId < 69) || ($singleField->templateFieldId == 1131)) {
						$input = $input . '<p><label class="toggle-switchy" id="toggle-switchy' . wp_kses($singleField->binderFieldId, 'strip') . '" for="switch' . wp_kses($singleField->binderFieldId, 'strip') . '" data-size="xl" >
						<input class="toggle-switchy-input" name="' . wp_kses($singleField->binderFieldId, 'strip') . '" type="checkbox" id="switch' . wp_kses($singleField->binderFieldId, 'strip') . '" value=true';
						if ($singleField->default_Value_For_Boolean === 1) $input = $input . " checked ";
						$input = $input . '>';
						$input = $input . '<span class="toggle" id="toggle' . wp_kses($singleField->binderFieldId, 'strip') . '" data-content-on="' . wp_kses($singleField->labelTrue, 'strip') . '" data-content-off="' . wp_kses($singleField->labelFalse, 'strip') . '">
							<span class="switch" id="switch' . wp_kses($singleField->binderFieldId, 'strip') . '"></span>
						</span>
						</label></p>';
					} else {
						$input = $input . '<p><input class="yesnology-checkbox-single" name="' . wp_kses($singleField->binderFieldId, 'strip') . '" type="checkbox" id="switch' . wp_kses($singleField->binderFieldId, 'strip') . '" ';
						if ($singleField->isRequired && $singleField->templateFieldId !== 1151 && $singleField->templateFieldId !== 1161) $input = $input . ' required ';
						$input = $input . ' value=true ';
						$input = $input . ' data-template="' . wp_kses($singleField->templateFieldId, 'strip') . '"';
						if ($singleField->default_Value_For_Boolean === 1) $input = $input . " checked ";
						$input = $input . '>';
						if ($singleField->title) $input = $input . wp_kses($singleField->title, 'strip') . '</p>';
							else $input = $input . wp_kses($singleField->name, 'strip') . '</p>';
						$input = $input . "<p  style='display: none;' class='checkbox-error checkbox-error-" . wp_kses($singleField->templateFieldId, 'strip') . "'>" . wp_kses($singleField->validationErrorText, 'strip') . "</p>";
					}
					break;
			}
		}
		$input = $input . '<div class="loader" id="loader-submit"></div>';
		$input = $input . "<div style='text-align: " . wp_kses(get_option( 'yesnology_buttonPosition' ), 'strip') . "' class='button-submit-container'><button class='button-submit'>" . __('Send','YesNology') . "</button></div></form></div>";
		// ShortCodes are filters and should always return, never echo.
		return $input;
	}

}
