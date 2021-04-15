<?php
/**
 * ResponsiveImage class.
 *
 * @category   Class
 * @package    ResponsiveImageForElementor
 * @subpackage WordPress
 * @author     Samuel Goldenbaum
 * @copyright  2021 Samuel Goldenbaum
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 * @link       https://github.com/samuelgoldenbaum/responsive-image-for-elementor/
 * @since      1.0.0
 * php version 7.3.9
 */

namespace ResponsiveImageForElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Core\Responsive\Responsive;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || die();

require_once(__DIR__ . '/../constants.php');

/**
 * ResponsiveImage widget class.
 *
 * @since 1.0.0
 */
class ResponsiveImage extends Widget_Base
{
    /**
     * Class constructor.
     *
     * @param array $data Widget data.
     * @param array $args Widget arguments.
     */
    public function __construct($data = array(), $args = null) {
        parent::__construct($data, $args);

        // not really needed, lets save the http request
        // wp_register_style('responsive-image-for-elementor', plugins_url('/assets/css/responsive-image-for-elementor.css', RESPONSIVE_IMAGE_FOR_ELEMENTOR_FILE), array(), '1.0.0');
    }

    /**
     * Retrieve the widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     *
     * @access public
     *
     */
    public function get_name() {
        return 'responsive-image';
    }

    /**
     * Retrieve the widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     *
     * @access public
     *
     */
    public function get_title() {
        return __('Responsive Image', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD);
    }

    /**
     * Retrieve the widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     *
     * @access public
     *
     */
    public function get_icon() {
        return 'eicon-image';
    }

    /**
     * Retrieve the list of categories the widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * Note that currently Elementor supports only one category.
     * When multiple categories passed, Elementor uses the first one.
     *
     * @return array Widget categories.
     * @since 1.0.0
     *
     * @access public
     *
     */
    public function get_categories() {
        return array('basic');
    }

    /**
     * Enqueue styles.
     */
    public function get_style_depends() {
        return array('responsive-image-for-elementor');
    }

    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function _register_controls() {
        $this->start_controls_section(
            'section_shared',
            array(
                'label' => __('Settings', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
            )
        );

        $this->add_control(
            'title',
            array(
                'label' => __('Title', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::TEXT,
                'default' => __('', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'dynamic' => [
                    'active' => true,
                ],
            )
        );

        $this->add_control(
            'alt',
            array(
                'label' => __('Alt Text', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::TEXT,
                'default' => __('', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'dynamic' => [
                    'active' => true,
                ],
            )
        );

        $this->add_control(
            'loading',
            [
                'label' => __('Loading', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'lazy',
                'options' => [
                    'lazy' => __('Lazy (recommended)', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'eager' => __('Eager (browser default)', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                ],
            ]
        );

        $this->add_control(
            'tag',
            [
                'label' => __('Tag', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'img',
                'options' => [
                    'img' => __('img', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'picture' => __('picture', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                ],
            ]
        );

        $this->add_control(
            'separator_shared',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __('Alignment', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'caption_source',
            [
                'label' => __('Caption', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'none' => __('None', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'custom' => __('Custom Caption', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                ],
                'default' => 'none',
                'condition' => [
                    'tag' => 'picture',
                ],
            ]
        );

        $this->add_control(
            'caption',
            [
                'label' => __('Custom Caption', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Enter your image caption', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'condition' => [
                    'tag' => 'picture',
                    'caption_source' => 'custom',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'link_to',
            [
                'label' => __('Link', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'file' => __('Media File', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'custom' => __('Custom URL', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                ],
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __('Link', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-link.com', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'condition' => [
                    'link_to' => 'custom',
                ],
                'show_label' => false,
            ]
        );

        $this->add_control(
            'open_lightbox',
            [
                'label' => __('Lightbox', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'yes' => __('Yes', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'no' => __('No', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                ],
                'condition' => [
                    'link_to' => 'file',
                ],
            ]
        );

        $this->add_control(
            'separator_images',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );

//        $this->add_control(
//            'mobile_image_required',
//            [
//                'label' => __('* Mobile Image is required', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
//                'type' => Controls_Manager::RAW_HTML,
//                'raw' => '',
//            ]
//        );

        $this->add_control(
            'mobile_image',
            [
                'label' => __('Mobile Image', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'description' => __('* Mobile Image is required', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'mobile_orientation',
            [
                'label' => __('Mobile Image Orientation', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'exclude',
                'options' => [
                    'exclude' => __('Exclude', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'landscape' => __('Landscape', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'portrait' => __('Portrait', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                ],
                'condition' => [
                    'tag' => 'picture',
                ],
            ]
        );

        $this->add_control(
            'separator_tablet',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
                'condition' => [
                    'mobile_image[id]!' => '',
                ],
            ]
        );

        $this->add_control(
            'tablet_image',
            [
                'label' => __('Tablet Image', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'mobile_image[id]!' => '',
                ],
            ]
        );

        $this->add_control(
            'tablet_orientation',
            [
                'label' => __('Tablet Image Orientation', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'exclude',
                'options' => [
                    'exclude' => __('Exclude', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'landscape' => __('Landscape', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'portrait' => __('Portrait', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                ],
                'condition' => [
                    'mobile_image[id]!' => '',
                    'tag' => 'picture',
                ],
            ]
        );

        $this->add_control(
            'separator_desktop',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
                'condition' => [
                    'mobile_image[id]!' => '',
                ],
            ]
        );

        $this->add_control(
            'desktop_image',
            [
                'label' => __('Desktop Image', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'mobile_image[id]!' => '',
                ],
            ]
        );

        $this->add_control(
            'desktop_orientation',
            [
                'label' => __('Desktop Image Orientation', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SELECT,
                'default' => 'exclude',
                'options' => [
                    'exclude' => __('Exclude', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'landscape' => __('Landscape', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'portrait' => __('Portrait', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                ],
                'condition' => [
                    'mobile_image[id]!' => '',
                    'tag' => 'picture',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_info',
            [
                'label' => __('Help', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
            ]
        );

        $this->add_control(
            'plugin_info',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => '<a href="https://github.com/samuelgoldenbaum/responsive-image-for-elementor/wiki" target="_blank">Plugin Docs</a>',
            ]
        );

        $this->end_controls_section();

        // style tab
        $this->start_controls_section(
            'section_style_image',
            [
                'label' => __('Image', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => __('Width', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px', 'vw'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'space',
            [
                'label' => __('Max Width', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px', 'vw'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label' => __('Height', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'size_units' => ['px', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // https://github.com/elementor/elementor/issues/13799
        $this->add_responsive_control(
            'object-fit',
            [
                'label' => __('Object Fit', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SELECT,
                'condition' => [
                    'height[size]!' => '',
                ],
                'options' => [
                    '' => __('Default', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'fill' => __('Fill', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'cover' => __('Cover', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                    'contain' => __('Contain', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'separator_panel_style',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );

        $this->start_controls_tabs('image_effects');

        $this->start_controls_tab('normal',
            [
                'label' => __('Normal', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
            ]
        );

        $this->add_control(
            'opacity',
            [
                'label' => __('Opacity', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .elementor-image img',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab('hover',
            [
                'label' => __('Hover', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
            ]
        );

        $this->add_control(
            'opacity_hover',
            [
                'label' => __('Opacity', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image:hover img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}} .elementor-image:hover img',
            ]
        );

        $this->add_control(
            'background_hover_transition',
            [
                'label' => __('Transition Duration', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => __('Hover Animation', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .elementor-image img',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => __('Border Radius', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .elementor-image img',
            ]
        );

        $this->end_controls_section();

        // caption
        $this->start_controls_section(
            'section_style_caption',
            [
                'label' => __('Caption', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'caption_source!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'caption_align',
            [
                'label' => __('Alignment', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};',
                ],
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
            ]
        );

        $this->add_control(
            'caption_background_color',
            [
                'label' => __('Background Color', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'caption_typography',
                'selector' => '{{WRAPPER}} .widget-image-caption',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'caption_text_shadow',
                'selector' => '{{WRAPPER}} .widget-image-caption',
            ]
        );

        $this->add_responsive_control(
            'caption_space',
            [
                'label' => __('Spacing', RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Check if the current widget has caption
     *
     * @access private
     * @param array $settings
     *
     * @return boolean
     * @since 2.3.0
     *
     */
    private function has_caption($settings) {
        return (!empty($settings['caption_source']) && 'none' !== $settings['caption_source']);
    }

    /**
     * Get the caption for current widget.
     *
     * @access private
     * @param $settings
     *
     * @return string
     * @since 2.3.0
     */
    private function get_caption($settings) {
        $caption = '';
        if (!empty($settings['caption_source'])) {
            switch ($settings['caption_source']) {
                case 'attachment':
                    $caption = wp_get_attachment_caption($settings['image']['id']);
                    break;
                case 'custom':
                    $caption = !Utils::is_empty($settings['caption']) ? $settings['caption'] : '';
            }
        }
        return $caption;
    }

    /**
     * Retrieve viewbox dimensions for svg attachments
     *
     * @param string $url
     *
     * @return array An array containing the width and height
     * @since 1.0.0
     * @access private
     *
     */
    private function get_svg_dimensions($url) {
        $xml = simplexml_load_file($url);
        $attr = $xml->attributes();
        $viewbox = explode(' ', $attr->viewBox);
        $meta['width'] = isset($attr->width) && preg_match('/\d+/', $attr->width, $value) ? (int)$value[0] : (count($viewbox) == 4 ? (int)$viewbox[2] : null);
        $meta['height'] = isset($attr->height) && preg_match('/\d+/', $attr->height, $value) ? (int)$value[0] : (count($viewbox) == 4 ? (int)$viewbox[3] : null);

        return $meta;
    }

    /**
     * Render the widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $breakpoints = Responsive::get_breakpoints();
        $mime_type = '';
        $responsive = false;
        $src = '';

        $this->add_render_attribute('wrapper', 'class', 'elementor-image');

        $this->add_render_attribute('image', 'alt', $settings['alt']);
        $this->add_render_attribute('image', 'title', $settings['title']);
        $this->add_render_attribute('image', 'loading', $settings['loading']);

        $image_html = '';

        if ($settings['tag'] === 'img') {
            $srcset = '';
            $sizes = '';

            if (!empty($settings['desktop_image']['id'])) {
                $desktop_image_meta = wp_get_attachment_metadata($settings['desktop_image']['id']);
                $desktop_image_meta_width_adjusted_for_retina = $desktop_image_meta['width'] / 2;

                $srcset = "{$settings['desktop_image']['url']} {$desktop_image_meta['width']}w";
                $sizes = "(min-width: {$breakpoints['lg']}px) {$desktop_image_meta_width_adjusted_for_retina}px";
                $src = $settings['desktop_image']['url'];
                $responsive = true;
            }

            if (!empty($settings['tablet_image']['id'])) {
                $tablet_image_meta = wp_get_attachment_metadata($settings['tablet_image']['id']);
                $tablet_image_meta_width_adjusted_for_retina = $tablet_image_meta['width'] / 2;

                if ($srcset !== '') {
                    $srcset .= ', ';
                    $sizes .= ', ';
                }

                $srcset .= "{$settings['tablet_image']['url']} {$tablet_image_meta['width']}w";
                $sizes .= ", (min-width: {$breakpoints['md']}px) {$tablet_image_meta_width_adjusted_for_retina}px";
                $src = $settings['tablet_image']['url'];
                $responsive = true;
            }

            if (!empty($settings['mobile_image']['id'])) {
                $mobile_image_meta = wp_get_attachment_metadata($settings['mobile_image']['id']);
                if (empty($mobile_image_meta['width'])) {
//                    $mime_type = get_post_mime_type($settings['mobile_image']['id']);
//                    if ($mime_type === 'image/svg+xml' && !$responsive) {
//                        $svg_meta = $this->get_svg_dimensions($settings['mobile_image']['url']);
//                        $svg_width = $svg_meta['width'];
//                        $svg_height = $svg_meta['height'];
//
//                        $this->add_render_attribute('image', 'width', $svg_width);
//                        $this->add_render_attribute('image', 'height', $svg_height);
//                    }
                } else {
                    $mobile_image_meta_width_adjusted_for_retina = $mobile_image_meta['width'] / 2;

                    if ($srcset !== '') {
                        $srcset .= ', ';
                        $sizes .= ', ';
                    }

                    $srcset .= "{$settings['mobile_image']['url']} {$mobile_image_meta['width']}w";
                    $sizes .= "{$mobile_image_meta_width_adjusted_for_retina}px";
                }

                $src = $settings['mobile_image']['url'];
            }

            if ($responsive) {
                $this->add_render_attribute('image', 'srcset', $srcset);
                $this->add_render_attribute('image', 'sizes', $sizes);
            }

            $this->add_render_attribute('image', 'src', $src);
            $image_html = '<img ' . $this->get_render_attribute_string('image') . '/>';
        } else {
            $image_html .= '<picture>';

            if (!empty($settings['desktop_image']['id'])) {
                $desktop_orientation = '';
                if ($settings['desktop_orientation'] !== 'exclude') {
                    $desktop_orientation = "(orientation: {$settings['desktop_orientation']}) and ";
                }
                $image_html .= "<source srcset=\"{$settings['desktop_image']['url']}\" media=\"{$desktop_orientation}(min-width: {$breakpoints['lg']}px)\">";
                $src = $settings['desktop_image']['url'];
                $responsive = true;
            }

            if (!empty($settings['tablet_image']['id'])) {
                $tablet_orientation = '';
                if ($settings['tablet_orientation'] !== 'exclude') {
                    $tablet_orientation = "(orientation: {$settings['tablet_orientation']}) and ";
                }
                $image_html .= "<source srcset=\"{$settings['tablet_image']['url']}\" media=\"{$tablet_orientation}(min-width: {$breakpoints['md']}px)\">";
                $src = $settings['tablet_image']['url'];
                $responsive = true;
            }

            if (!empty($settings['mobile_image']['id'])) {
                $mobile_image_meta = wp_get_attachment_metadata($settings['mobile_image']['id']);
                if (empty($mobile_image_meta['width'])) {
//                    $mime_type = get_post_mime_type($settings['mobile_image']['id']);
//                    if ($mime_type === 'image/svg+xml' && !$responsive) {
//                        $svg_meta = $this->get_svg_dimensions($settings['mobile_image']['url']);
//                        $svg_width = $svg_meta['width'];
//                        $svg_height = $svg_meta['height'];
//
//                        $this->add_render_attribute('image', 'width', $svg_width);
//                        $this->add_render_attribute('image', 'height', $svg_height);
//                    }
                } else {
                    $mobile_orientation = '';
                    if ($settings['mobile_orientation'] !== 'exclude') {
                        $mobile_orientation = "media=\"(orientation: {$settings['mobile_orientation']})\"";
                    }
                    $image_html .= "<source srcset=\"{$settings['mobile_image']['url']}\" {$mobile_orientation}>";
                }

                $src = $settings['mobile_image']['url'];
            }

            $this->add_render_attribute('image', 'src', $src);
            $image_html .= '<img ' . $this->get_render_attribute_string('image') . '>';
            $image_html .= '</picture>';
        }

        $link = $this->get_link_url($settings);
        if ($link) {
            $this->add_link_attributes('link', $link);

            if (Plugin::$instance->editor->is_edit_mode()) {
                $this->add_render_attribute('link', [
                    'class' => 'elementor-clickable',
                ]);
            }

            if ('custom' !== $settings['link_to']) {
                $this->add_lightbox_data_attributes('link', $settings['image']['id'], $settings['open_lightbox']);
            }

            $link_html = '<a class="responsive-image-for-elementor-link" ' . $this->get_render_attribute_string('link');
            if (isset($settings['title']) && trim($settings['title']) !== '') {
                $link_html .= ' title="' . $settings['title'] . '"';
            }
            $link_html .= '>';
            $image_html = $link_html . $image_html . '</a>';
        }

        // if we have a caption
        $has_caption = $this->has_caption($settings);
        if ($has_caption) {
            $image_html = '<figure class="wp-caption">' . $image_html . '<figcaption class="widget-image-caption wp-caption-text">' . $this->get_caption($settings) . '</figcaption></figure>';
        }

        echo '<div ' . $this->get_render_attribute_string('wrapper') . '>' . $image_html . '</div>';

        ?>
        <?php
    }

    /**
     * Render the widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function _content_template() {
        ?>
        <#
        const breakpoints = <?php echo json_encode(Responsive::get_breakpoints()); ?>;

        let responsive = false;

        const hasCaption = () => {
            return settings.tag === 'picture' && settings.caption_source === 'custom';
        }

        const preloadAttachments = async (attachment_ids) => {
            const these = attachment_ids.filter((attachment_id) => {
                return 'undefined' === typeof wp.media.attachment(attachment_id).get('url');
            });

            if (these.length === 0) {
                return;
            }

            const attachments = these.map((id) => {
                return wp.media.attachment(id).fetch();
            });

            await Promise.all(attachments);

            view.render();
        }

        let linkUrl;
        if ( 'custom' === settings.link_to ) {
            linkUrl = settings.link.url;
        } else if ( 'file' === settings.link_to ) {
            linkUrl = settings.image.url;
        }

        let imgClass = '';
        if ( '' !== settings.hover_animation ) {
            imgClass = 'elementor-animation-' + settings.hover_animation;
        }
        #>
        <div class="elementor-image{{ settings.shape ? ' elementor-image-shape-' + settings.shape : '' }}">
            <# if ( hasCaption() ) { #>
            <figure class="wp-caption">
                <# } #>

                <# if ( linkUrl ) { #>
                <a title="{{ settings.title }}" class="responsive-image-for-elementor-link elementor-clickable" data-elementor-open-lightbox="{{ settings.open_lightbox }}" href="{{ linkUrl }}">
                    <# } #>

                    <# if ( settings.tag === 'picture' ) {
                        let src = '';
                    #>
                    <picture>
                        <# if (settings.desktop_image.id) {
                        const orientation = (settings.desktop_orientation !== 'exclude') ? `(orientation: ${settings.desktop_orientation}) and ` : null;
                        src = settings.desktop_image.url;
                        #>
                        <source srcset="{{settings.desktop_image.url}}" media="{{orientation}}(min-width: {{breakpoints.lg}}px)">
                        <# } #>

                        <# if (settings.tablet_image.id) {
                        const orientation = (settings.tablet_orientation !== 'exclude') ? `(orientation: ${settings.tablet_orientation}) and ` : null;
                        src = settings.tablet_image.url;
                        #>
                        <source srcset="{{settings.tablet_image.url}}" media="{{orientation}}(min-width: {{breakpoints.md}}px)">
                        <# } #>
                        <# if (settings.mobile_image.id) {
                        const orientation = (settings.mobile_orientation !== 'exclude') ? `(orientation: ${settings.mobile_orientation})` : null;
                        src = settings.mobile_image.url;
                        #>
                        <source srcset="{{settings.mobile_image.url}}" media="{{orientation}}">
                        <# } #>
                        <img src="{{ src }}" class="{{ imgClass }}" loading="{{ settings.loading }}" alt="{{ settings.alt }}">
                    </picture>
                    <# }
                    else {
                        let ids = [];
                        if (settings.desktop_image.id) {
                            ids.push(settings.desktop_image.id);
                            responsive = true;
                        }

                        if (settings.tablet_image.id) {
                            ids.push(settings.tablet_image.id);
                            responsive = true;
                        }

                        if (settings.mobile_image.id) {
                            ids.push(settings.mobile_image.id);
                        }

                        preloadAttachments(ids);

                        let srcSet = '';
                        let sizes = '';
                        let src = '';

                        if (settings.desktop_image.id) {
                            const imageAttachment = wp.media.attachment(settings.desktop_image.id).attributes;

                            srcSet = `${settings.desktop_image.url} ${imageAttachment.width}w`;
                            sizes = `(min-width: ${breakpoints['lg']}px) ${Math.ceil(imageAttachment.width / 2)}px`;
                            src = settings.desktop_image.url;
                        }

                        if (settings.tablet_image.id) {
                            const imageAttachment = wp.media.attachment(settings.tablet_image.id).attributes;

                            if (srcSet.length > 0) {
                                srcSet += `, `;
                                sizes += `, `;
                            }

                            if (imageAttachment.width) {
                                srcSet += `${settings.tablet_image.url} ${imageAttachment.width}w`;
                                sizes += `(min-width: ${breakpoints['md']}px) ${Math.ceil(imageAttachment.width / 2)}px`;
                            }

                            src = settings.tablet_image.url;
                        }

                        if (settings.mobile_image.id) {
                            const imageAttachment = wp.media.attachment(settings.mobile_image.id).attributes;

                            if (imageAttachment.media !== 'image/svg+xml') {
                                if (srcSet.length > 0) {
                                    srcSet += `, `;
                                    sizes += `, `;
                                }

                                srcSet += `${settings.mobile_image.url} ${imageAttachment.width}w`;
                                sizes += `${Math.ceil(imageAttachment.width / 2)}px`;
                            }

                            src = settings.mobile_image.url;
                        }

                        if (responsive) {
                    #>
                        <img id="responsive-image-for-elementor" src="{{ src }}" class="{{ imgClass }}" loading="{{ settings.loading }}" alt="{{ settings.alt }}" srcset="{{ srcSet }}" sizes="{{ sizes }}"/>
                    <#
                        } else {
                    #>
                        <img id="responsive-image-for-elementor" src="{{ src }}" class="{{ imgClass }}" loading="{{ settings.loading }}" alt="{{ settings.alt }}"/>
                    <#
                        }
                    }
                    if ( linkUrl ) { #>
                </a>
                <# } #>
                <# if ( hasCaption() ) { #>
                <figcaption class="widget-image-caption wp-caption-text">{{{ settings.caption }}}</figcaption>
            </figure>
            <# } #>
        </div>
        <?php
    }

    /**
     * Retrieve image widget link URL.
     *
     * @param array $settings
     *
     * @return array|string|false An array/string containing the link URL, or false if no link.
     * @since 1.0.0
     * @access private
     *
     */
    private function get_link_url($settings) {
        if ('none' === $settings['link_to']) {
            return false;
        }

        if ('custom' === $settings['link_to']) {
            if (empty($settings['link']['url'])) {
                return false;
            }

            return $settings['link'];
        }

        return [
            'url' => $settings['image']['url'],
        ];
    }
}
