<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
  Plugin Name: WP Media Manager Lite
  Plugin URI:  https://accesspressthemes.com/wordpress-plugins/wp-media-manager-lite/
  Description: An easy way to organize your thousands of media images,files inside folders with drag and drop method | Numerous Hierarchical Folder Management
  Version:     1.1.4
  Author:      AccessPress Themes
  Author URI:  http://accesspressthemes.com
  License:     GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Domain Path: /languages
  Text Domain: wp-media-manager-lite
 */
global $wpdb;
defined( 'WPMManagerLite_VERSION' ) or define( 'WPMManagerLite_VERSION', '1.1.4' ); //plugin version
defined( 'WPMManagerLite_TITLE' ) or define( 'WPMManagerLite_TITLE', 'WP Media Manager Lite' ); //plugin version
defined( 'WPMManagerLite_TD' ) or define( 'WPMManagerLite_TD', 'wp-media-manager-lite' ); //plugin's text domain
defined( 'WPMManagerLite_Prefix' ) or define( 'WPMManagerLite_Prefix', 'wpmediamanager' ); //plugin's text domain
defined( 'WPMManagerLite_IMG_DIR' ) or define( 'WPMManagerLite_IMG_DIR', plugin_dir_url( __FILE__ ) . 'images' ); //plugin image directory
defined( 'WPMManagerLite_BACKEND_JS_DIR' ) or define( 'WPMManagerLite_BACKEND_JS_DIR', plugin_dir_url( __FILE__ ) . 'js/backend/' );  //plugin backend js directory
defined( 'WPMManagerLite_FRONTEND_JS_DIR' ) or define( 'WPMManagerLite_FRONTEND_JS_DIR', plugin_dir_url( __FILE__ ) . 'js/frontend/' );  //plugin frontend js directory
defined( 'WPMManagerLite_CSS_DIR' ) or define( 'WPMManagerLite_CSS_DIR', plugin_dir_url( __FILE__ ) . 'css/' ); // plugin css dir
defined( 'WPMManagerLite_PATH' ) or define( 'WPMManagerLite_PATH', plugin_dir_path( __FILE__ ) );
defined( 'WPMManagerLite_URL' ) or define( 'WPMManagerLite_URL', plugin_dir_url( __FILE__ ) ); //plugin directory url
defined( 'WPMManagerLite_FolderLists' ) or define( 'WPMManagerLite_FolderLists', $wpdb->prefix . 'media_folders_lists' );
defined( 'WPMManagerLite_FolderFileRelationship' ) or define( 'WPMManagerLite_FolderFileRelationship', $wpdb->prefix . 'media_folder_file_relationship' );
define('WPMManagerLite_PRE_GET_POSTS_PRIORITY', 9999999);
include(WPMManagerLite_PATH . 'inc/class/class-mobile-detect.php');

if(!class_exists('WPMManagerLite_MainClass')){
/**
* Main class
*/
class WPMManagerLite_MainClass{

    var $mylibrary;

    function __construct()
    {
            $this->wpmdia_manager_includes_files();
            $this->mylibrary = new WPMManagerLite_Libary();
            register_activation_hook( __FILE__, array( $this,'wpmmanagerLite_activation' ));
            add_action( 'admin_menu' , array($this ,  'wpmmmanager_menu_page') ); // add plugin menu
            add_filter( 'post_mime_types', array($this , 'wpmmmanager_modify_post_mime_types') ); // Add Filter Hook
            add_action('wp_enqueue_scripts', array($this, 'wpmdia_register_assets')); //registers scripts and styles for front end
            add_action( 'admin_init', array( $this, 'redirect_to_site' ), 1 );
            add_filter( 'plugin_row_meta', array( $this, 'wpmdialite_plugin_row_meta' ), 10, 2 );
            add_filter( 'admin_footer_text', array( $this, 'wpmdialite_admin_footer_text' ) );
    }

   function wpmdialite_admin_footer_text( $text ){
            if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wp-media-manager-lite' ) {
                $link = 'https://wordpress.org/support/plugin/wp-media-manager-lite/reviews/#new-post';
                $pro_link = 'https://accesspressthemes.com/wordpress-plugins/wp-media-manager/';
                $text = 'Enjoyed WP Media Manager Lite? <a href="' . $link . '" target="_blank">Please leave us a ★★★★★ rating</a> We really appreciate your support! | Try premium version of <a href="' . $pro_link . '" target="_blank">WP Media Manager</a> - more features, more power!';
                return $text;
            } else {
                return $text;
            }
        }

      function redirect_to_site(){
            if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wpmedialite-doclinks' ) {
                wp_redirect( 'https://accesspressthemes.com/documentation/wp-media-manager-lite/' );
                exit();
            }
            if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wpmedialite_premium' ) {
                wp_redirect( 'https://accesspressthemes.com/wordpress-plugins/wp-media-manager/' );
                exit();
            }
        }

      function wpmdialite_plugin_row_meta( $links, $file ){
            if ( strpos( $file, 'wp-media-manager-lite.php' ) !== false ) {
                $new_links = array(
                    'demo' => '<a href="http://demo.accesspressthemes.com/wordpress-plugins/wp-media-manager-lite" target="_blank"><span class="dashicons dashicons-welcome-view-site"></span>Live Demo</a>',
                    'doc' => '<a href="https://accesspressthemes.com/documentation/wp-media-manager-lite/" target="_blank"><span class="dashicons dashicons-media-document"></span>Documentation</a>',
                    'support' => '<a href="http://accesspressthemes.com/support" target="_blank"><span class="dashicons dashicons-admin-users"></span>Support</a>',
                    'pro' => '<a href="https://accesspressthemes.com/wordpress-plugins/wp-media-manager/" target="_blank"><span class="dashicons dashicons-cart"></span>Premium version</a>'
                );
                $links = array_merge( $links, $new_links );
            }
            return $links;
        }


    //register frontend assests
       public function wpmdia_register_assets() {
             $file = WPMManagerLite_PATH . 'css/frontend/custom-inline-style.css';
                if (@file_exists($file)) {
                    // get custom settings single file
                     $wpmdiam_settings =  get_option('wpmediamanager_settings');
                     $enable_wpmmanager = (isset($wpmdiam_settings['enable_wpmmanager']) && $wpmdiam_settings['enable_wpmmanager'] == 1)?'1':'0';

                  //pdf file
                  $show_size_amount        = (isset($wpmdiam_settings['show_size_amount']) && $wpmdiam_settings['show_size_amount'] == 1)?'1':'0';
                  $show_format_type        = (isset($wpmdiam_settings['show_format_type']) && $wpmdiam_settings['show_format_type'] == 1)?'1':'0';
                 //custom pdf layout
                  $pdffile_bg_color         = (isset($wpmdiam_settings['pdffile_bg_color']) && $wpmdiam_settings['pdffile_bg_color'] != '')?esc_attr($wpmdiam_settings['pdffile_bg_color']):'#6e7b9b';
                  $pdffile_bg_hcolor         = (isset($wpmdiam_settings['pdffile_bg_hcolor']) && $wpmdiam_settings['pdffile_bg_hcolor'] != '')?esc_attr($wpmdiam_settings['pdffile_bg_hcolor']):'#6e7b9b';
                  $pdffile_font_color       = (isset($wpmdiam_settings['pdffile_font_color']) && $wpmdiam_settings['pdffile_font_color'] != '')?esc_attr($wpmdiam_settings['pdffile_font_color']):'#ffffff';
                  $pdffile_font_hcolor         = (isset($wpmdiam_settings['pdffile_font_hcolor']) && $wpmdiam_settings['pdffile_font_hcolor'] != '')?esc_attr($wpmdiam_settings['pdffile_font_hcolor']):'#ffffff';
                  $pdffile_font_size         = (isset($wpmdiam_settings['pdffile_font_size']) && $wpmdiam_settings['pdffile_font_size'] != '')?esc_attr($wpmdiam_settings['pdffile_font_size']):'18';
                  $file_icon_color         = (isset($wpmdiam_settings['file_icon_color']) && $wpmdiam_settings['file_icon_color'] != '')?esc_attr($wpmdiam_settings['file_icon_color']):'#ffffff';
                  $file_icon_size        = (isset($wpmdiam_settings['file_icon_size']) && $wpmdiam_settings['file_icon_size'] != '')?esc_attr($wpmdiam_settings['file_icon_size']):'38';

                 if($show_size_amount == 1){
                    $check_size = "block";
                 }else{
                  $check_size = "none";
                 }
                 if($show_format_type == 1){
                    $check_format = "block";
                 }else{
                 $check_format = "none";
                 }
                  $fontsize =  intval($pdffile_font_size) - 3;

                    if($enable_wpmmanager  == 1){
                    // custom css by settings
                    $custom_css = "
                              .wpmdia_tot_size{
                                display:".$check_size.";
                                float: left;
                                margin-right: 12px;
                              }
                              .wpmdia_format_type{
                                display:".$check_format.";
                              }
                              .wpmedia_manager_ofile_wraper{
                                background: ".$pdffile_bg_color." !important;
                              }
                              .wpmedia_manager_ofile_wraper:hover{
                                 background: ".$pdffile_bg_hcolor." !important;
                              }
                              .wpmedia_manager_ofile_wraper a.wpdmdia-dwnload-ofile{
                                color: ".$pdffile_font_color." !important;
                              }
                              .wpmedia_manager_ofile_wraper a.wpdmdia-dwnload-ofile:hover{
                                color: ".$pdffile_font_hcolor." !important;
                              }
                              .wpmedia_manager_ofile_wraper .wpdmai-file-title{
                                font-size: ".$pdffile_font_size."px !important;
                              }
                              .wpmedia_manager_ofile_wraper .wpmdia_tot_size,
                              .wpmedia_manager_ofile_wraper .wpmdia_format_type{
                                font-size: ".$fontsize."px !important;
                              }
                              .wpmedia_manager_ofile_wraper .wpdmdia-content-ofile:before{
                                color: ".$file_icon_color." !important;
                                font-size: ".$file_icon_size ."px !important;
                              }
                            ";

                    // write custom css to file custom-inline-style.css
                    file_put_contents(
                            $file, $custom_css
                    );
                    }
                }
            wp_enqueue_style('wpmdia-custom-stylesheet', WPMManagerLite_CSS_DIR . 'frontend/custom-inline-style.css', false, WPMManagerLite_VERSION);
            wp_enqueue_style('wpmdia-frontend-style',WPMManagerLite_CSS_DIR.'frontend/frontend.css',false,WPMManagerLite_VERSION);
            wp_enqueue_style('wpmdia-fontawesome-style', WPMManagerLite_CSS_DIR . '/font-awesome/font-awesome.min.css', false, WPMManagerLite_VERSION);
            wp_enqueue_style('wpmmanagerLite-icomoon-style', WPMManagerLite_CSS_DIR . 'icomoon/icomoon.css', false, WPMManagerLite_VERSION);
        }

    /*
      * Includes All Wp Media Manager class
    */
    public function wpmdia_manager_includes_files(){
        /* library*/
        require_once WPMManagerLite_PATH . 'inc/common-model/common-libs.php';
        require_once WPMManagerLite_PATH . 'inc/class/core-class.php';
        require_once WPMManagerLite_PATH . 'inc/class/database-class.php';

        $wpmdiam_settings =  get_option('wpmediamanager_settings');
        $enable_customfilters = (isset($wpmdiam_settings['enable_customfilters']) && $wpmdiam_settings['enable_customfilters'] == 1)?'1':'0';
        $enable_gallery       = ((isset($wpmdiam_settings['enable_gallery_features']) && $wpmdiam_settings['enable_gallery_features'] == 1)?1:0);

        if (isset($enable_customfilters) && $enable_customfilters == 1){
              require_once WPMManagerLite_PATH . 'inc/class/filter-custom-class.php';
              new FilterCustomClass_Lite();
        }
          require_once WPMManagerLite_PATH . 'inc/class/wpmdia-gallery-metadata.php';
           new Gallery_Metadata_Lite;

    }


     /*
      * Plugin Activation Default Setup
     */
      public function wpmmanagerLite_activation(){
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
         if (is_plugin_active('wp-media-manager/wp-media-manager.php')) {
           wp_die( __( 'You need to deactivate WP Media Manager Premium Plugin in order to
            activate WP Media Manager Free plugin. Please deactivate premium one first. Your data will not be affected on deactivating.', APEXNB_TD ) );
         }

        include('inc/backend/activate.php');

        /*
        * Load Default Settings
        */
        if (!get_option('wpmediamanager_settings')) {
           $wpmediamanager_settings = $this->wpmediamanager_default_settings();
           update_option('wpmediamanager_settings', $wpmediamanager_settings);
        }
         if (!get_option('wpmediamanager_custom_dimension') || !get_option('wpmediamanager_s_filtersize')) {
            $wpmedia_dimension_size = array('400x300', '640x480', '800x600', '1024x768', '1600x1200');
            $wpmedia_size_jsondta = json_encode($wpmedia_dimension_size);
            update_option('wpmediamanager_s_filtersize', $wpmedia_size_jsondta);
            update_option('wpmediamanager_custom_dimension', $wpmedia_size_jsondta);
        }

        if (!get_option('wpmedia_weight_default') || !get_option('wpmedia_selected_wt_default')) {
             $wpmedia_weights = array(array('0-61440', 'kB'), array('61440-122880', 'kB'), array('122880-184320', 'kB'), array('184320-245760', 'kB'), array('245760-307200', 'kB'));
             $wpmedia_weights_jsondta = json_encode($wpmedia_weights);
             update_option('wpmedia_selected_wt_default', $wpmedia_weights_jsondta);
             update_option('wpmedia_weight_default',$wpmedia_weights_jsondta);
        }

             /**
             * Google font save
             * */
            $family = array('ABeeZee','Abel','Dosis','Abril Fatface','Aclonica','Acme','Actor','Adamina','Advent Pro','Aguafina Script','Akronim','Aladin','Aldrich','Alef','Alegreya','Alegreya SC','Alegreya Sans','Alegreya Sans SC','Alex Brush','Alfa Slab One','Alice','Alike','Alike Angular','Allan','Allerta','Allerta Stencil','Allura','Almendra','Almendra Display','Almendra SC','Amarante','Amaranth','Amatic SC','Amethysta','Amiri','Amita','Anaheim','Andada','Andika','Angkor','Annie Use Your Telescope','Anonymous Pro','Antic','Antic Didone','Antic Slab','Anton','Arapey','Arbutus','Arbutus Slab','Architects Daughter','Archivo Black','Archivo Narrow','Arimo','Arizonia','Armata','Artifika','Arvo','Arya','Asap','Asar','Asset','Astloch','Asul','Atomic Age','Aubrey','Audiowide','Autour One','Average','Average Sans','Averia Gruesa Libre','Averia Libre','Averia Sans Libre','Averia Serif Libre','Bad Script','Balthazar','Bangers','Basic','Battambang','Baumans','Bayon','Belgrano','Belleza','BenchNine','Bentham','Berkshire Swash','Bevan','Bigelow Rules','Bigshot One','Bilbo','Bilbo Swash Caps','Biryani','Bitter','Black Ops One','Bokor','Bonbon','Boogaloo','Bowlby One','Bowlby One SC','Brawler','Bree Serif','Bubblegum Sans','Bubbler One','Buda','Buenard','Butcherman','Butterfly Kids','Cabin','Cabin Condensed','Cabin Sketch','Caesar Dressing','Cagliostro','Calligraffitti','Cambay','Cambo','Candal','Cantarell','Cantata One','Cantora One','Capriola','Cardo','Carme','Carrois Gothic','Carrois Gothic SC','Carter One','Caudex','Cedarville Cursive','Ceviche One','Changa One','Chango','Chau Philomene One','Chela One','Chelsea Market','Chenla','Cherry Cream Soda','Cherry Swash','Chewy','Chicle','Chivo','Cinzel','Cinzel Decorative','Clicker Script','Coda','Coda Caption','Codystar','Combo','Comfortaa','Coming Soon','Concert One','Condiment','Content','Contrail One','Convergence','Cookie','Copse','Corben','Courgette','Cousine','Coustard','Covered By Your Grace','Crafty Girls','Creepster','Crete Round','Crimson Text','Croissant One','Crushed','Cuprum','Cutive','Cutive Mono','Damion','Dancing Script','Dangrek','Dawning of a New Day','Days One','Dekko','Delius','Delius Swash Caps','Delius Unicase','Della Respira','Denk One','Devonshire','Dhurjati','Didact Gothic','Diplomata','Diplomata SC','Domine','Donegal One','Doppio One','Dorsa','Dosis','Dr Sugiyama','Droid Sans','Droid Sans Mono','Droid Serif','Duru Sans','Dynalight','EB Garamond','Eagle Lake','Eater','Economica','Eczar','Ek Mukta','Electrolize','Elsie','Elsie Swash Caps','Emblema One','Emilys Candy','Engagement','Englebert','Enriqueta','Erica One','Esteban','Euphoria Script','Ewert','Exo','Exo 2','Expletus Sans','Fanwood Text','Fascinate','Fascinate Inline','Faster One','Fasthand','Fauna One','Federant','Federo','Felipa','Fenix','Finger Paint','Fira Mono','Fira Sans','Fjalla One','Fjord One','Flamenco','Flavors','Fondamento','Fontdiner Swanky','Forum','Francois One','Freckle Face','Fredericka the Great','Fredoka One','Freehand','Fresca','Frijole','Fruktur','Fugaz One','GFS Didot','GFS Neohellenic','Gabriela','Gafata','Galdeano','Galindo','Gentium Basic','Gentium Book Basic','Geo','Geostar','Geostar Fill','Germania One','Gidugu','Gilda Display','Give You Glory','Glass Antiqua','Glegoo','Gloria Hallelujah','Goblin One','Gochi Hand','Gorditas','Goudy Bookletter 1911','Graduate','Grand Hotel','Gravitas One','Great Vibes','Griffy','Gruppo','Gudea','Gurajada','Habibi','Halant','Hammersmith One','Hanalei','Hanalei Fill','Handlee','Hanuman','Happy Monkey','Headland One','Henny Penny','Herr Von Muellerhoff','Hind','Holtwood One SC','Homemade Apple','Homenaje','IM Fell DW Pica','IM Fell DW Pica SC','IM Fell Double Pica','IM Fell Double Pica SC','IM Fell English','IM Fell English SC','IM Fell French Canon','IM Fell French Canon SC','IM Fell Great Primer','IM Fell Great Primer SC','Iceberg','Iceland','Imprima','Inconsolata','Inder','Indie Flower','Inika','Inknut Antiqua','Irish Grover','Istok Web','Italiana','Italianno','Jacques Francois','Jacques Francois Shadow','Jaldi','Jim Nightshade','Jockey One','Jolly Lodger','Josefin Sans','Josefin Slab','Joti One','Judson','Julee','Julius Sans One','Junge','Jura','Just Another Hand','Just Me Again Down Here','Kadwa','Kalam','Kameron','Kantumruy','Karla','Karma','Kaushan Script','Kavoon','Kdam Thmor','Keania One','Kelly Slab','Kenia','Khand','Khmer','Khula','Kite One','Knewave','Kotta One','Koulen','Kranky','Kreon','Kristi','Krona One','Kurale','La Belle Aurore','Laila','Lakki Reddy','Lancelot','Lateef','Lato','League Script','Leckerli One','Ledger','Lekton','Lemon','Libre Baskerville','Life Savers','Lilita One','Lily Script One','Limelight','Linden Hill','Lobster','Lobster Two','Londrina Outline','Londrina Shadow','Londrina Sketch','Londrina Solid','Lora','Love Ya Like A Sister','Loved by the King','Lovers Quarrel','Luckiest Guy','Lusitana','Lustria','Macondo','Macondo Swash Caps','Magra','Maiden Orange','Mako','Mallanna','Mandali','Marcellus','Marcellus SC','Marck Script','Margarine','Marko One','Marmelad','Martel','Martel Sans','Marvel','Mate','Mate SC','Maven Pro','McLaren','Meddon','MedievalSharp','Medula One','Megrim','Meie Script','Merienda','Merienda One','Merriweather','Merriweather Sans','Metal','Metal Mania','Metamorphous','Metrophobic','Michroma','Milonga','Miltonian','Miltonian Tattoo','Miniver','Miss Fajardose','Modak','Modern Antiqua','Molengo','Molle','Monda','Monofett','Monoton','Monsieur La Doulaise','Montaga','Montez','Montserrat','Montserrat Alternates','Montserrat Subrayada','Moul','Moulpali','Mountains of Christmas','Mouse Memoirs','Mr Bedfort','Mr Dafoe','Mr De Haviland','Mrs Saint Delafield','Mrs Sheppards','Muli','Mystery Quest','NTR','Neucha','Neuton','New Rocker','News Cycle','Niconne','Nixie One','Nobile','Nokora','Norican','Nosifer','Nothing You Could Do','Noticia Text','Noto Sans','Noto Serif','Nova Cut','Nova Flat','Nova Mono','Nova Oval','Nova Round','Nova Script','Nova Slim','Nova Square','Numans','Nunito','Odor Mean Chey','Offside','Old Standard TT','Oldenburg','Oleo Script','Oleo Script Swash Caps','Open Sans','Open Sans Condensed','Oranienbaum','Orbitron','Oregano','Orienta','Original Surfer','Oswald','Over the Rainbow','Overlock','Overlock SC','Ovo','Oxygen','Oxygen Mono','PT Mono','PT Sans','PT Sans Caption','PT Sans Narrow','PT Serif','PT Serif Caption','Pacifico','Palanquin','Palanquin Dark','Paprika','Parisienne','Passero One','Passion One','Pathway Gothic One','Patrick Hand','Patrick Hand SC','Patua One','Paytone One','Peddana','Peralta','Permanent Marker','Petit Formal Script','Petrona','Philosopher','Piedra','Pinyon Script','Pirata One','Plaster','Play','Playball','Playfair Display','Playfair Display SC','Podkova','Poiret One','Poller One','Poly','Pompiere','Pontano Sans','Poppins','Port Lligat Sans','Port Lligat Slab','Pragati Narrow','Prata','Preahvihear','Press Start 2P','Princess Sofia','Prociono','Prosto One','Puritan','Purple Purse','Quando','Quantico','Quattrocento','Quattrocento Sans','Questrial','Quicksand','Quintessential','Qwigley','Racing Sans One','Radley','Rajdhani','Raleway','Raleway Dots','Ramabhadra','Ramaraja','Rambla','Rammetto One','Ranchers','Rancho','Ranga','Rationale','Ravi Prakash','Redressed','Reenie Beanie','Revalia','Rhodium Libre','Ribeye','Ribeye Marrow','Righteous','Risque','Roboto','Roboto Condensed','Roboto Mono','Roboto Slab','Rochester','Rock Salt','Rokkitt','Romanesco','Ropa Sans','Rosario','Rosarivo','Rouge Script','Rozha One','Rubik','Rubik Mono One','Rubik One','Ruda','Rufina','Ruge Boogie','Ruluko','Rum Raisin','Ruslan Display','Russo One','Ruthie','Rye','Sacramento','Sahitya','Sail','Salsa','Sanchez','Sancreek','Sansita One','Sarala','Sarina','Sarpanch','Satisfy','Scada','Scheherazade','Schoolbell','Seaweed Script','Sevillana','Seymour One','Shadows Into Light','Shadows Into Light Two','Shanti','Share','Share Tech','Share Tech Mono','Shojumaru','Short Stack','Siemreap','Sigmar One','Signika','Signika Negative','Simonetta','Sintony','Sirin Stencil','Six Caps','Skranji','Slabo 13px','Slabo 27px','Slackey','Smokum','Smythe','Sniglet','Snippet','Snowburst One','Sofadi One','Sofia','Sonsie One','Sorts Mill Goudy','Source Code Pro','Source Sans Pro','Source Serif Pro','Special Elite','Spicy Rice','Spinnaker','Spirax','Squada One','Sree Krushnadevaraya','Stalemate','Stalinist One','Stardos Stencil','Stint Ultra Condensed','Stint Ultra Expanded','Stoke','Strait','Sue Ellen Francisco','Sumana','Sunshiney','Supermercado One','Sura','Suranna','Suravaram','Suwannaphum','Swanky and Moo Moo','Syncopate','Tangerine','Taprom','Tauri','Teko','Telex','Tenali Ramakrishna','Tenor Sans','Text Me One','The Girl Next Door','Tienne','Tillana','Timmana','Tinos','Titan One','Titillium Web','Trade Winds','Trocchi','Trochut','Trykker','Tulpen One','Ubuntu','Ubuntu Condensed','Ubuntu Mono','Ultra','Uncial Antiqua','Underdog','Unica One','UnifrakturCook','UnifrakturMaguntia','Unkempt','Unlock','Unna','VT323','Vampiro One','Varela','Varela Round','Vast Shadow','Vesper Libre','Vibur','Vidaloka','Viga','Voces','Volkhov','Vollkorn','Voltaire','Waiting for the Sunrise','Wallpoet','Walter Turncoat','Warnes','Wellfleet','Wendy One','Wire One','Work Sans','Yanone Kaffeesatz','Yantramanav','Yellowtail','Yeseva One','Yesteryear','Zeyada');
            $wpmdia_font_family = get_option('wpmdia_font_family');
            if (empty($wpmdia_font_family)) {
                update_option('wpmdia_font_family', $family);
            }

       }

      /**
        * Returns Default Settings
       */
       public static function wpmediamanager_default_settings() {
             $default_gallery_imgsize = array
             (  '0' => 'thumbnail',
                '1' => 'large',
                '2' => 'medium',
                '3' => 'post-thumbnail');

            $wpmediamanager_settings = array(
                 'enable_wpmmanager'=> '1',
                 'enable_removeall'=> '0',
                 'display_medianum' => '0',
                 'enable_customfilters' => '1',
                 'show_duplicatefiles' => '1',
                 'enable_gallery_features' => '1',
                 'enable_gallery_sc' => '1',
                 'border_color' => '',
                 'grid_column_desktop' => '6',
                 'grid_column_tablet' => '3',
                 'grid_column_mobile' => '1',
                 'slider_speed' => '600',
                 'slider_pause' => '4000',
                 'gallery_image_size' => serialize($default_gallery_imgsize),
                 'enable_lightbox' => '1',
                 'choose_pp_theme' => 'pp_default',
                 'animation_speed'=> 'normal',
                 'slideshow_speed'=> '5000',
                 'dot_color'=> '',
                 'dot_active_color'=> '',
                 'pager_color'=> '',
                 'pager_active_color'=> '',
                 'arrow_bg_color'=> '',
                 'arrow_color'=> '',
                 'arrow_hover_bgcolor'=> '',
                 'arrow_hover_fontcolor'=> '',
                 'enable_pdf_file_design' => '1',
                 'show_size_amount' => '1',
                 'show_format_type' => '1',
                 'pdffile_bg_color' => '',
                 'pdffile_bg_hcolor' => '',
                 'pdffile_font_color' => '',
                 'pdffile_font_hcolor' => '',
                 'pdffile_font_size' => '12',
                 'file_icon_color' => '',
                 'file_icon_size' => '15'
            );
            return $wpmediamanager_settings;
        }

     /*
      * Admin Menu
     */
     public function wpmmmanager_menu_page(){
         add_menu_page(__(WPMManagerLite_TITLE,WPMManagerLite_TD), __(WPMManagerLite_TITLE,WPMManagerLite_TD), 'manage_options', 'wp-media-manager-lite', array( $this, 'wpmmanagerLite_main_page' ), 'dashicons-admin-media');
         add_submenu_page( 'wp-media-manager-lite', __( 'Documentation',WPMManagerLite_TD), __( 'Documentation', WPMManagerLite_TD ), 'manage_options', 'wpmedialite-doclinks', '__return_false', null, 9 );
         add_submenu_page( 'wp-media-manager-lite', __( 'Check Premium Version', WPMManagerLite_TD ), __( 'Check Premium Version', WPMManagerLite_TD ), 'manage_options', 'wpmedialite_premium', '__return_false', null, 9 );
     }

     public function wpmmanagerLite_main_page(){
       include(WPMManagerLite_PATH.'/inc/backend/folder-management/general_settings.php');
     }


     public function wpmmmanager_modify_post_mime_types( $post_mime_types ) {
        // select the mime type, here: 'application/pdf'
        // then we define an array with the label values
        $wpmediamanager_settings = get_option('wpmediamanager_settings');
        $enable_customfilters    = ((isset($wpmediamanager_settings['enable_customfilters']) && $wpmediamanager_settings['enable_customfilters'] == 1)?1:0);
      if($enable_customfilters){
        $post_mime_types['wpmdiamanager_pdf_type'] = array('PDFs', 'Manage PDFs Documents', _n_noop('PDFs Documents <span class="count">(%s)</span>', 'PDFs Documents <span class="count">(%s)</span>'));
        $post_mime_types['wpmdiamanager_docs_type'] = array('MS Office Documents', 'Manage MS Office Documents', _n_noop('MS Office Documents <span class="count">(%s)</span>', 'MS Office Documents <span class="count">(%s)</span>'));
        $post_mime_types['wpmdiamanager_ico_type'] = array('X-Icons', 'Manage X-Icons', _n_noop('X-Icons <span class="count">(%s)</span>', 'X-Icons <span class="count">(%s)</span>'));
        $post_mime_types['wpmdiamanager_zip_type'] = array('Zip Formats', 'Manage Zip Formats', _n_noop('Zip Formats<span class="count">(%s)</span>', 'Zip Formats<span class="count">(%s)</span>'));
        $post_mime_types['wpmdiamanager_text_type'] = array('Text Formats', 'Manage Text Formats', _n_noop('Text Formats <span class="count">(%s)</span>', 'Text Formats <span class="count">(%s)</span>'));
        $post_mime_types['wpmdiamanager_openoffice_type'] = array('OpenOffice formats', 'Manage OpenOffice formats', _n_noop('OpenOffice formats <span class="count">(%s)</span>', 'OpenOffice formats <span class="count">(%s)</span>'));
        // then we return the $post_mime_types variable
      }
        return $post_mime_types;
     }


           /**
         * Sanitizes Multi Dimensional Array
         * @param array $array
         * @param array $sanitize_rule
         * @return array
         *
         * @since 1.0.0
         */
        public static function sanitize_array($array = array(), $sanitize_rule = array()) {
            if (!is_array($array) || count($array) == 0) {
                return array();
            }

            foreach ($array as $k => $v) {

                if (!is_array($v)) {
                    $default_sanitize_rule = (is_numeric($k)) ? 'text' : 'html';
                    $sanitize_type = isset($sanitize_rule[$k]) ? $sanitize_rule[$k] : $default_sanitize_rule;
                    $array[$k] = WPMManagerLite_MainClass::sanitize_value($v, $sanitize_type);
                }
                if (is_array($v)) {
                    $array[$k] = WPMManagerLite_MainClass::sanitize_array($v, $sanitize_rule);
                }
            }

            return $array;
        }

        /**
         * Sanitizes Value
         *
         * @param type $value
         * @param type $sanitize_type
         * @return string
         *
         * @since 1.0.0
         */
        public static function sanitize_value($value = '', $sanitize_type = 'text') {
            switch ($sanitize_type) {
                case 'html':
                    $allowed_html = wp_kses_allowed_html('post');
                    return wp_kses($value, $allowed_html);
                    break;
                default:
                    return sanitize_text_field($value);
                    break;
            }
        }
  }
  $wpmmlite_menu = new WPMManagerLite_MainClass();
  $wppmlite_libraryobj = new WPMManagerLite_Libary();
}
