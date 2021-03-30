<?php
/*
Plugin Name: Mable Custom Search Listing Form
Description: Creates a Mable form widget and shortcode to filter by address.
Version:     1.0.0
Author:      Mitash
Author URI: https://www.mitash.com/
 */

define("CSLF_DIR_PATH", plugin_dir_path(__FILE__));

add_action('widgets_init', 'callback_cslf_ads_widget');

function callback_cslf_ads_widget()
{
    register_widget('cslf_search');
}

// Enqueue additional admin scripts
add_action('admin_enqueue_scripts', 'cslf_search_wdscript');

function cslf_search_wdscript()
{wp_enqueue_media();}

// Widget

class cslf_search extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(

            // Base ID of your widget
            'cslf_search',

            // Widget name will appear in UI
            __('Mable Custom Search Form', 'cslf_search'),

            // Widget description
            array('description' => __('Creates a Mable form widget and shortcode to filter by address.', 'cslf_search'))
        );
        add_shortcode('mable_search', array($this, 'cslf_shortcode'));
    }

    public function cslf_search()
    {
        $widget_ops = array('classname' => 'cslf-search');
        $this->WP_Widget('cslf-search-widget', 'Custom Search Form', $widget_ops);
    }

    public function cslf_shortcode($atts = [], $content = null)
    {

        extract(shortcode_atts(array(
            'title' => '',
            'utm' => 'SourceKidsSearch',
            'form_logo' => '',
            'form_bg_img' => '',
        ), $atts
        )
        );

        $instance = array(
            'form_utm' => $atts['utm'],
            'form_logo' => $atts['form_logo'],
            'form_bg_img' => $atts['form_bg_img'],
        );

        ob_start();
        the_widget('cslf_search', $instance, array(
            'widget_id' => '',
            'before_widget' => '<div id="%1$s" class="widget %2$s"',
            'after_widget' => '</div>',
            'before_title' => '',
            'after_title' => '',
        ));
        $output = ob_get_contents();
        ob_end_clean();
        return $output;

    }

    public function widget($args = [], $instance = [])
    {

        echo $args['before_widget'];

        ?>

<?php
$title = $instance['form_title'] ?? null;

        $logo = isset($instance['form_logo']) ? esc_url($instance['form_logo']) : null;
        $form_bg_img = isset($instance['form_bg_img']) ? $instance['form_bg_img'] : '';
        $utm = isset($instance['form_utm']) ? $instance['form_utm'] : '';
        $styleData = '';
        if ($form_bg_img != '') {
            $styleData = 'style="background-image: url(' . $form_bg_img . ')"';
        }

        $Content = '<div class="cslf_form_sec" ' . $styleData . '>';

        if ($logo) {
            $Content .= '<img class="form_logo" src="' . $logo . '" />';
        }
        if ($title) {
            $Content .= '<h3 class="form_heading">' . $title . '</h3>';
        }

        $Content .= '<form class="cslf_form" name="mable-form-1" onsubmit="return false" action="https://mable.com.au/mable-search-results/" method="post">';

        $Content .= '<label class="sr-only" for="keyword">Search  by address</label>';

        $Content .= '<i class="fas fa-search search-page-icon"></i>';

        $Content .= '<input class="cslf_keyword_input" autocomplete="off" title="Search" name="keyword"  class="form-control" placeholder="Enter suburb or postcode">';

        $Content .= '<input type="hidden" class="cslf_utm" name="UTM" value="' . $utm . '">';

        $Content .= '<input type="hidden" class="cslf_dataIndex" name="dataIndex" value="">';
        $Content .= '<div class="search_icon_sec"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="search" class="svg-inline--fa fa-search fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#fff" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg><span>Search</span></div>';
        $Content .= '<div class="cslf_mable_fetch"></div>';

        $Content .= '</form></div>';

        echo $Content;

        echo $args['after_widget'];

    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['form_title'] = strip_tags($new_instance['form_title']);
        $instance['form_logo'] = strip_tags($new_instance['form_logo']);
        $instance['form_bg_img'] = strip_tags($new_instance['form_bg_img']);
        $instance['form_utm'] = strip_tags($new_instance['form_utm']);

        return $instance;
    }

    public function form($instance)
    {

        ?>
    <p>
        <label for="<?php echo $this->get_field_id('form_title'); ?>">Form Title</label><br />
        <input type="text" name="<?php echo $this->get_field_name('form_title'); ?>" id="<?php echo $this->get_field_id('form_title'); ?>" value="<?php echo $instance['form_title']; ?>" class="widefat" />
    </p>

     <p>
        <label for="<?php echo $this->get_field_id('form_utm'); ?>">UTM</label><br />
        <input type="text" name="<?php echo $this->get_field_name('form_utm'); ?>" id="<?php echo $this->get_field_id('form_utm'); ?>" value="<?php echo $instance['form_utm']; ?>" class="widefat" />
    </p>

    <p>

        <label for="<?=$this->get_field_id('form_logo');?>">Form Logo</label>

        <img class="<?=$this->id?>_img" src="<?=(!empty($instance['form_logo'])) ? $instance['form_logo'] : '';?>" style="margin:0;padding:0;max-width:100%;display:block"/>

        <input type="text" class="widefat <?=$this->id?>_url" name="<?=$this->get_field_name('form_logo');?>" value="<?=$instance['form_logo'];?>" style="margin-top:5px;" />

        <input type="button" id="<?=$this->id?>" class="button button-primary js_custom_upload_media" value="Upload Image" style="margin-top:5px;" />

    </p>

      <p>

        <label for="<?=$this->get_field_id('form_bg_img');?>">Form Background Image</label>

        <img class="<?=$this->id?>-fbg_img" src="<?=(!empty($instance['form_bg_img'])) ? $instance['form_bg_img'] : '';?>" style="margin:0;padding:0;max-width:100%;display:block"/>

        <input type="text" class="widefat <?=$this->id?>-fbg_url" name="<?=$this->get_field_name('form_bg_img');?>" value="<?=$instance['form_bg_img'];?>" style="margin-top:5px;" />

        <input type="button" id="<?=$this->id?>-fbg" class="button button-primary js_custom_upload_media_fbg" value="Upload Image" style="margin-top:5px;" />

    </p>



<?php

    }

}

add_action('wp_head', 'callback_cslf_additional_style_data');

function callback_cslf_additional_style_data()
{

    ?>

        <style type="text/css">

            .cslf_form_sec{
                max-width: 500px;
                max-height: 500px;
                padding: 20px;
                border-radius: 10px;
box-shadow: 0 12px 17px 2px rgba(69, 43, 65, 0.07), 0 5px 22px 4px rgba(69, 43, 65, 0.06), 0 7px 8px -4px rgba(69, 43, 65, 0.1);
            }

            .cslf_form_sec .form_heading{
                margin: 0 0 0.25em 0;
                font-weight: bold;
            }

            .cslf_form{

                display: -webkit-box;

                display: -ms-flexbox;

                display: flex;

                -ms-flex-wrap: wrap;

                flex-wrap: wrap;

                position: relative;

            }

            .sr-only {

                position: absolute;

                width: 1px;

                height: 1px;

                padding: 0;

                margin: -1px;

                overflow: hidden;

                clip: rect(0, 0, 0, 0);

                border: 0;

            }



            .cslf_keyword_input{



                width: 100%;

                border-radius: 5px;

                border: 2px solid #CCCCCC;

                padding: 0 0 0 80px;

                color: #2F1D2C;

                font-size: 17px;

                height: 60px;

                -webkit-transition: all 0.5s;

                -o-transition: all 0.5s;

                transition: all 0.5s;

                background-position: top 20px right 15px;

            }



            .search-page-icon {

                display: none;

            }



            .search_icon_sec{
              padding: 9px 12px 9px 16px;
              cursor: pointer;
              white-space: nowrap;
              pointer-events: none;
              position: absolute;
              width: 63px;
              left: 7px;
              top: 10%;
              background: #DF720C;
              border: 2px solid #DF720C;
              border-radius: 5px;
              display: flex;
              justify-content: center;
              align-items: center;
              height: 80%;
            }



            .search_icon_sec .fa-search{
              height: 75%;
            }

            .search_icon_sec span{

                display:none;

            }



            .cslf_form_sec .cslf_mable_fetch {

                position: absolute;

                left: 0;

                top: 58px;

                right: 0;

                z-index: 9999;

            }

            .cslf_form_sec .mable-search-list{

                margin: 0;

                padding: 0;

                border: 2px solid #91949A;

                border-radius: 0 0 5px 5px;

                overflow: auto;

                background: #FFF;

                max-height: 297px;

                list-style-type: none;

            }

            .cslf_form_sec .mable-search-item{

                width: 100%;

                cursor: pointer;

            }

            .cslf_form_sec .mable-search-item a {

                width: 100%;

                padding: 17px 15px 16px 17px;

                font-size: 18px;

                line-height: 24px;

                color: #2F1D2C !important;

                letter-spacing: -0.35px;

                font-weight: normal !important;

                display: block;

                font-family: "Sofia Pro", Arial, sans-serif;

            }

            .loading {

                background-image: url(//mable.com.au/wp-content/themes/mable/src/assets/images/ajax-loader.gif);

                background-repeat: no-repeat;

            }

        </style>

    <?php

}

add_action('admin_footer', 'cslf_callback_image_script');

function cslf_callback_image_script()
{

    ?>

    <script>

    jQuery(document).ready(function ($) {

          function media_upload(button_selector) {

            var _custom_media = true,

                _orig_send_attachment = wp.media.editor.send.attachment;

            $('body').on('click', button_selector, function () {

              var button_id = $(this).attr('id');

              wp.media.editor.send.attachment = function (props, attachment) {

                if (_custom_media) {

                  $('.' + button_id + '_img').attr('src', attachment.url);

                  $('.' + button_id + '_url').val(attachment.url);

                } else {

                  return _orig_send_attachment.apply($('#' + button_id), [props, attachment]);

                }

              }

              wp.media.editor.open($('#' + button_id));

              return false;

            });

          }

          media_upload('.js_custom_upload_media');
          media_upload('.js_custom_upload_media_fbg');

    });

 </script>

<?php

}

add_action('wp_footer', 'ajax_fetch');

function ajax_fetch()
{

    ?>

  <script type="text/javascript">

    //http://davidwalsh.name/javascript-debounce-function

    function debounce(func, wait, immediate) {

      var timeout;

      return function() {

        var context = this,

          args = arguments;

        var later = function() {

          timeout = null;

          if (!immediate) func.apply(context, args);

        };

        var callNow = immediate && !timeout;

        clearTimeout(timeout);

        timeout = setTimeout(later, wait);

        if (callNow) func.apply(context, args);

      };

    };

    jQuery(document).keyup(function(e) {

      if (e.keyCode === 27) {

        jQuery('.cslf_mable_fetch').empty();

      }

    });

    jQuery(".cslf_keyword_input").keyup(function() {

      jQuery(this).addClass("loading");

    })

    jQuery(".cslf_keyword_input").keyup(debounce(function() {

        var thisdata = jQuery(this);

        fetch(thisdata);

    }, 200));



    function fetch($thisdata) {

      $thisdata.addClass("loading");

      var keySearchVal = $thisdata.val();

      if (keySearchVal.length == 0) {

        jQuery('.cslf_mable_fetch').empty();

        $thisdata.removeClass("loading");

      }

      if (keySearchVal.length < 2) return;



      jQuery.ajax({

        url: '<?php echo admin_url('admin-ajax.php'); ?>',

        type: 'post',

        data: {

          action: 'cslf_data_fetch',

          keyword: keySearchVal

        },

        success: function(data) {

          $thisdata.removeClass("loading");

          if (data == "No Record Found") {

            jQuery('.cslf_mable_fetch').empty();

          } else {

            jQuery('.cslf_mable_fetch').html(data);

          }

        }

      });

    }

  </script>

  <script type="text/javascript">

      jQuery(document).ready(function($) {

        $(document).on("click",".mable-search-list li",function() {

          var address = $(this).text();

          if (address == 'Search') {

            var address = jQuery('.cslf_mable_fetch').val();
            var addressStr = address.replace(/\s+/g, '-').toLowerCase();

          } else {

            var index = $(this).data("index");

            $(".cslf_dataIndex").val(index);

            $(".cslf_keyword_input").val(address);

            $(".cslf_mable_fetch").empty();

            //return false;

            //window.location.replace("https://mable.com.au/suburb/?text="+address+"");

            //return false;

            var addressStr = address.replace(/\s+/g, '-').toLowerCase();
            var ret = addressStr.split("-");
            var retCount = ret.length;
            var postKey = retCount - 1;
            var suburb = ret[0];
            var state = '';

            if (retCount > 3) {
              state = ret[1] + ' ' + ret[2];
            } else {
              state = ret[1];
            }

            var postcode = ret[postKey];

          }

          var cslf_utm = jQuery('.cslf_utm').val();

          // console.log(encodeURIComponent(address));
          var urlNew = "https://mable.com.au/mable-search-results/?search=" + address.replace(/\s+/g, '-').toLowerCase() + "&suburb=" + encodeURIComponent(address);

          if(cslf_utm !=''){
              urlNew +="&utm="+cslf_utm;
          }

          // window.location.replace(urlNew); // open new window for better UX
          window.open(urlNew);
          return false;

        });

      });

    </script>
  <?php

}

// The Mable Live Ajax Function
add_action('wp_ajax_cslf_data_fetch', 'cslf_data_fetch');
add_action('wp_ajax_nopriv_cslf_data_fetch', 'cslf_data_fetch');

function cslf_data_fetch()
{

    $keyword = $_POST['keyword'];
    $apiUrl = "https://app.mable.com.au/search/suburb_search/" . $keyword . ".json";
    $get_file = file_get_contents($apiUrl);

    $response = json_decode($get_file, true);

    if (empty($response)) {
        echo 'No Record Found';
    } else {

        echo '<ul class="mable-search-list">';
        $recdi = '1';
        foreach ($response as $data) {
            //index
            $index = $data['index'];
            $address = $data['address'];
            if ($recdi <= '10') {
                echo '<li data-index = ' . $index . ' class="mable-search-item"><a href="#">' . $address . '</a></li>';
            }
            $recdi++;
        }
        echo '</ul>';?>
<?php

    }
    die();

}
