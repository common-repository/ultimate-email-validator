(function ($) {

    'use strict';

    var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver,
        loaded = false,
        ie11 = window.navigator.userAgent.indexOf('Trident/7.0') > 0;

    $.ultimate_email_validator = $.ultimate_email_validator || {};

    // Admin Panel
    $.ultimate_email_validator.panel = $.ultimate_email_validator.panel || {};

    // Elements
    $.ultimate_email_validator.element = $.ultimate_email_validator.element || {};
    
    /*
     * Server Variables
     *
     * AJAX: ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN + _ajax
     *
     **/
    $.ultimate_email_validator.ajax_vars = {

        ajax_url: ultimate_email_validator_ajax.ajax_url,
        security: ultimate_email_validator_ajax.global_security, // security
        isRTL: ultimate_email_validator_ajax.is_rtl,

        fetalError: 'error',

        admin: {

            is_network_plugin: ultimate_email_validator_ajax.admin.is_network_plugin,
            is_network_plugin_only: ultimate_email_validator_ajax.admin.is_network_plugin_only,
            is_network_plugin_and_admin: ultimate_email_validator_ajax.admin.is_network_plugin_and_admin,

            plugin_name: ultimate_email_validator_ajax.admin.plugin_name,
            admin_page_ele_prefix: ultimate_email_validator_ajax.admin.admin_page_ele_prefix,

            action_prefix: ultimate_email_validator_ajax.admin.action_prefix,

            action_result: {
                save: {
                    success: ultimate_email_validator_ajax.admin.action_result.save.success,
                    fail: ultimate_email_validator_ajax.admin.action_result.save.fail
                },
                restore: {
                    success: ultimate_email_validator_ajax.admin.action_result.restore.success,
                    fail: ultimate_email_validator_ajax.admin.action_result.restore.fail
                },
                importing: {
                    success: ultimate_email_validator_ajax.admin.action_result.importing.success,
                    fail: ultimate_email_validator_ajax.admin.action_result.importing.fail
                },
            }

        },

        elements: {
            media: {
                single: {

                    header_title: ultimate_email_validator_ajax.elements.media.single.header_title

                }
            }
        },

        confirm: {
            delete_item: ultimate_email_validator_ajax.confirm.delete_item,
        },

        errors: {
            code_100: ultimate_email_validator_ajax.errors.code_100,
            code_200: ultimate_email_validator_ajax.errors.code_200,
        }

    };

    /**
     *
     * Global Variables ( DO NOT move inside document.ready )
     *
     */
    $.ultimate_email_validator.global_vars = {

        page_values_changed: false,

        icons_library_titles: '',
        icons_encoded: '',

    };

    /**
     * 
     * FIRE UP
     *
     */
    $(document).ready(function () {

        $.ultimate_email_validator.init();

        // $.ultimate_email_validator.panel.tabs();

        // $.ultimate_email_validator.panel.child_groups();


        // ColorPicker
        var colorPickerContainers = $('.srpset-element-wrapper.element-wpcolor .srpset-color-picker, .srpset-element-wrapper.element-background .srpset-color-picker');
        $.ultimate_email_validator.element.colorpicker(colorPickerContainers);

        $.ultimate_email_validator.element.colorpicker_actions();


        // Spinner
        var spinnerSelectors = $('.srpset-element-wrapper.element-spinner');
        $.ultimate_email_validator.element.spinner(spinnerSelectors);

        // Slider
        var sliderSelectors = $('.srpset-element-wrapper.element-slider');
        $.ultimate_email_validator.element.slider(sliderSelectors);

        // Range Slider
        var rangeSliderSelectors = $('.srpset-element-wrapper.element-range-slider');
        $.ultimate_email_validator.element.range_slider(rangeSliderSelectors);

        $.ultimate_email_validator.element.select2();

        $.ultimate_email_validator.element.radio_images();

        $.ultimate_email_validator.element.media();
        $.ultimate_email_validator.element.gallery();
        
        /* QTip */
        $.ultimate_email_validator.qtooltip();


    });


    /**
     * Initalize necessary actions
     * VI Note: Used in CSS
     *
     */
    $.ultimate_email_validator.init = function () {

        $('body').addClass('css-bodywrp-oxibug-uev');

    }

    /**
     * Collect the correct element name by tab key and element name
     * Otherwise return empty string
     *
     * @since 1.0
     *
     * */
    $.ultimate_email_validator.panel.getElementID = function (prefix, tab, element_name) {

        if (!_.isEmpty(tab) && !_.isEmpty(element_name)) {

            return prefix + '_' + tab + '_' + element_name;

        }

        return '';
    }


    /**
     * Collect the correct element name by tab key and element name
     * Otherwise return empty string
     *
     * @since 1.0
     *
     * */
    $.ultimate_email_validator.panel.getElementName = function (prefix, tab, element_name) {

        if (!_.isEmpty(tab) && !_.isEmpty(element_name)) {

            return prefix + '[' + tab + ']' + '[' + element_name + ']';

        }

        return '';
    }


    /**
     * Instantiate Save triggers
     *
     * @since 1.0
     *
     * */
    $.ultimate_email_validator.panel.SaveSettingsTriggers = function () {

        $(document.body).on('click', '.css-oxibug-uev-main-wrp .page-actions .save-settings a.button', function (e) {

            e.preventDefault();

            var formWrapper = $(this).closest('.css-oxibug-uev-main-wrp'),
                field_name_textarea_import = $.ultimate_email_validator.panel.getElementName($.ultimate_email_validator.ajax_vars.admin.admin_page_ele_prefix, 'import_export', 'text_import'),
                txt_import = formWrapper.find('.body-inner.import_export textarea[name="' + field_name_textarea_import + '"]').val();

            if (!_.isUndefined(txt_import) && !_.isEmpty(txt_import)) {

                $.ultimate_email_validator.panel.SaveSettings(formWrapper, 'import', '', '', txt_import);

            }
            else {

                var serializedUserInputs = formWrapper.find('.srpset-form-inner :input[name]').serialize();
                formWrapper.find('.srpset-form-inner :input[type=checkbox]').each(function () {
                    if (!this.checked) {
                        serializedUserInputs += '&' + this.name + '=0';
                    }
                });

                $.ultimate_email_validator.panel.SaveSettings(formWrapper, 'save', serializedUserInputs, '', '');

            }

            return false;

        });


        $(document.body).on('click', '.css-oxibug-uev-main-wrp .page-actions .reset-settings a.button', function (e) {

            e.preventDefault();

            var formWrapper = $(this).closest('.css-oxibug-uev-main-wrp');

            var base64_Defaults = formWrapper.find('#oxibug_page_settings_defaults').val();

            $.ultimate_email_validator.panel.SaveSettings(formWrapper, 'reset', '', '', base64_Defaults);

            return false;

        });

    }

    /**
     * Trigger {_SavePluginSettings} action from server side and perform the correct save action
     *
     * @since 1.0
     *
     * @param object formWrapper | The main settings container div
     *
     * @param string triggerTarget | The source of button click [save, reset, activate or deactivate] buttons
     *
     * @param array(Serialized) serializedUserSettings | The serialized array of user inputs to save in Db, NOTE: MUST BE Empty while using {licenseJSONData} and {importText}
     *
     * @param array(JSON) licenseJSONData | a JSON array returned from server with activating or deactivating appropriate array, NOTE: MUST BE Empty while using {importText}
     *
     * @param string importResetText | a Base64Encoded string returned from {Reset Defaults} or {Import Textarea} located in {import_export} tab, NOTE: MUST BE Empty while using {serializedUserSettings} and {licenseJSONData}
     *
     * */
    $.ultimate_email_validator.panel.SaveSettings = function (formWrapper, triggerTarget, serializedUserSettings, licenseJSONData, importResetText) {

        var msgSaved, msgFailed;

        switch (triggerTarget) {
            case 'save': {
                msgSaved = $.ultimate_email_validator.ajax_vars.admin.action_result.save.success;
                msgFailed = $.ultimate_email_validator.ajax_vars.admin.action_result.save.fail;
            } break;
            case 'reset': {
                msgSaved = $.ultimate_email_validator.ajax_vars.admin.action_result.restore.success;
                msgFailed = $.ultimate_email_validator.ajax_vars.admin.action_result.restore.fail;
            } break;
            case 'import': {
                msgSaved = $.ultimate_email_validator.ajax_vars.admin.action_result.importing.success;
                msgFailed = $.ultimate_email_validator.ajax_vars.admin.action_result.importing.fail;
            } break;
            default: {
                msgSaved = $.ultimate_email_validator.ajax_vars.admin.action_result.save.success;
                msgFailed = $.ultimate_email_validator.ajax_vars.admin.action_result.save.fail;
            } break;
        }

        formWrapper.find('form > .srpset-plugin-settings-loading').removeClass('hidden');

        var server_data = {
            action: $.ultimate_email_validator.ajax_vars.admin.action_prefix + '_SavePluginSettings',
            ajvar_ultimate_email_validator_security: $.ultimate_email_validator.ajax_vars.security,

            ajvar_ultimate_email_validator_is_network_plugin: $.ultimate_email_validator.ajax_vars.admin.is_network_plugin,
            ajvar_ultimate_email_validator_is_network_plugin_only: $.ultimate_email_validator.ajax_vars.admin.is_network_plugin_only,
            ajvar_ultimate_email_validator_is_network_plugin_and_admin: $.ultimate_email_validator.ajax_vars.admin.is_network_plugin_and_admin,

            ajvar_ultimate_email_validator_trigger_target: triggerTarget,

            ajvar_ultimate_email_validator_serialized_settings: serializedUserSettings,
            ajvar_ultimate_email_validator_license_json_data: licenseJSONData,
            ajvar_ultimate_email_validator_reset_import_text: importResetText
        };

        $.post($.ultimate_email_validator.ajax_vars.ajax_url, server_data)
            .done(function (response) {

                // Hide Loading to show action result 
                formWrapper.find('form > .srpset-plugin-settings-loading').addClass('hidden');

                // console.log(response);

                if (response !== 'x') {

                    formWrapper.find('form > .srpset-plugin-settings-result').find('.result-inner .sec-inner.sec-text p').text(msgSaved);
                    formWrapper.find('form > .srpset-plugin-settings-result').removeClass('hidden').addClass('success');

                    /*
                     * SetTimeout helps to clear browser cache before reload page, Even when use { reload(true) }
                     *
                     * */
                    setTimeout(function () {
                        formWrapper.find('form > .srpset-plugin-settings-result').addClass('hidden').removeClass('success');
                    }, 2000);

                    window.location.reload(true);

                    // console.log( response );

                }
                else {

                    formWrapper.find('form > .srpset-plugin-settings-result').find('.result-inner .sec-inner.sec-text p').text(msgFailed);
                    formWrapper.find('form > .srpset-plugin-settings-result').removeClass('hidden').addClass('fail');

                    setTimeout(function () {
                        formWrapper.find('form > .srpset-plugin-settings-result').addClass('hidden').removeClass('fail');
                    }, 1000);

                }

            })
            .fail(function () {

                // Hide Loading to show action result 
                formWrapper.find('form > .srpset-plugin-settings-loading').addClass('hidden');

                formWrapper.find('form > .srpset-plugin-settings-result').find('.result-inner .sec-inner.sec-text p').text(msgFailed);
                formWrapper.find('form > .srpset-plugin-settings-result').removeClass('hidden').addClass('fail');

                setTimeout(function () {
                    formWrapper.find('form > .srpset-plugin-settings-result').addClass('hidden').removeClass('fail');
                }, 1000);


            })
            .always(function () {

                formWrapper.find('form > .srpset-plugin-settings-loading').addClass('hidden');

            });

    }


    /**
     * Admin Panel tabs
     *
     */
    $.ultimate_email_validator.panel.tabs = function () {

        $(document.body).on('click', '.css-oxibug-uev-main-wrp table.form-table .srpset-form-inner.style-tabs .tabs-wrapper .parent-tabs-header ul > li.tab-trigger', function (e) {

            e.preventDefault();

            var btn_trigger = $(this),
                $main_form_inner = btn_trigger.closest('.srpset-form-inner.style-tabs'),
                $parent_tabs_header = btn_trigger.closest('.parent-tabs-header'),
                $parent_tabs_body = $main_form_inner.find('.parent-tabs-body'),

                active_tab = $main_form_inner.attr('data-active-tab'),
                trigger_key = btn_trigger.attr('data-tab');


            if (!_.isEmpty(trigger_key) && (trigger_key != active_tab)) {

                $parent_tabs_header.find('ul > li.tab-trigger').removeClass('active');
                $parent_tabs_body.find('.body-inner').removeClass('active');

                btn_trigger.addClass('active');
                $main_form_inner.attr('data-active-tab', trigger_key);

                $parent_tabs_body.find('[data-tab="' + trigger_key + '"]').addClass('active');

            }

            return false;

        });

    }


    /**
     * Child Groups
     *
     */
    $.ultimate_email_validator.panel.child_groups = function () {

        $(document.body).on('click', '.css-oxibug-uev-main-wrp table.form-table .srpset-form-inner .child-groups-wrapper .groups-header ul > li.group-trigger', function (e) {

            e.preventDefault();

            var btn_trigger = $(this),
                $main_form_inner = btn_trigger.closest('.child-groups-wrapper'),
                $parent_groups_header = btn_trigger.closest('.groups-header'),
                $parent_groups_body = $main_form_inner.find('.groups-body-container'),

                active_tab = $main_form_inner.attr('data-active-group'),
                trigger_key = btn_trigger.attr('data-group');


            if (!_.isEmpty(trigger_key) && (trigger_key != active_tab)) {

                $parent_groups_header.find('ul > li.group-trigger').removeClass('active');
                $parent_groups_body.find('.group-body').removeClass('active');

                btn_trigger.addClass('active');
                $main_form_inner.attr('data-active-group', trigger_key);

                $parent_groups_body.find('[data-group="' + trigger_key + '"]').addClass('active');

            }

            return false;

        });

    }


    /**
     * Update gallery images
     *
     **/
    $.ultimate_email_validator.update_multiple_images = function (imageContainer) {

        var ids = '',
            gallery_shortcode = '',
			arr_images = [],
			current_prev_image = $(imageContainer).find('.srp_media_preview'),
			shortcode_input = $(imageContainer).find('input.input_media'),
	        current_clear_button = $(imageContainer).find('.input-button.btn_media_clear');

        if (current_prev_image.find('span').length > 0) {

            current_prev_image.find('span').each(function (i) {

                arr_images[i] = $(this).data('id');

            });

        }
        else {

            shortcode_input.attr('value', '');
            current_clear_button.addClass('hidden');

        }


        if (arr_images.length > 0) {

            ids = arr_images.join(',');

        }

        if (!_.isEmpty(ids)) {

            gallery_shortcode = '[gallery ids="' + ids.toString() + '"]';

            shortcode_input.attr('value', gallery_shortcode);

        }

    }

    /*
     *====================================
     * START - Elements
     * ====================================
     * */

    /**
     * 
     * Element: Select2
     *
     **/
    $.ultimate_email_validator.element.select2 = function () {

        $('.srpset-element-wrapper.element-select .style-modern').select2({

            theme: "bootstrap4",
            dir: $.ultimate_email_validator.ajax_vars.isRTL ? 'rtl' : '',

        });
        
    }

    /**
     *
     * Perform all opertaions related to [Elements: Media Upload and Gallery]
     *
     */
    $.ultimate_email_validator.element.media = function () {

        var fr_image_upload = '',
            previewPanel = '';

        $(document.body).on('click', '.srpset-element-wrapper.element-upload-media .srpset-upload-media-container .input-button.btn_media_upload', function (e) {

            e.preventDefault();

            // Reset
            previewPanel = '';

            fr_image_upload = ''; // Return frame to null / Important

            var $btn_upload_current = $(this),
                $media_container = $btn_upload_current.closest('.srpset-upload-media-container'),
                cached_media_types = $(this).closest('.srpset-element-wrapper.element-upload-media').attr('data-media-types'),
                media_types = (!_.isEmpty(cached_media_types)) ? cached_media_types : '',
                enable_preview = ($(this).closest('.srpset-element-wrapper.element-upload-media').attr('data-enable-preview') == 'true') ? true : false;

            if (fr_image_upload) {
                fr_image_upload.open();
                return;
            }

            fr_image_upload = wp.media({

                title: $.ultimate_email_validator.ajax_vars.elements.media.single.header_title,

                library: {
                    type: media_types
                },

                multiple: false,

                close: false

            });


            fr_image_upload.on('select', function () {

                var files = fr_image_upload.state().get('selection').first(),
                    curMediaUrl = '',
                    curMediaType = '',
                    curMediaSubType = '';

                if (!_.isUndefined(files.attributes.url) && !_.isUndefined(files.attributes.sizes) && !_.isUndefined(files.attributes.sizes.medium) && !_.isUndefined(files.attributes.sizes.medium.url)) {

                    curMediaUrl = files.attributes.sizes.medium.url;

                }
                else {

                    curMediaUrl = files.attributes.url;

                }

                fr_image_upload.close();

                if (enable_preview && (!_.isUndefined(files.attributes.type))) {

                    curMediaType = files.attributes.type;

                    if (!_.isUndefined(files.attributes.subtype)) {
                        curMediaSubType = files.attributes.subtype;
                    }

                    switch (curMediaType) {

                        case 'image': {

                            $($media_container).find('.srp_media_preview').html('<img src="' + curMediaUrl + '" alt="" />').addClass('has-data');

                        } break;

                        case 'video': {

                            // supported Types: ['mp4', 'm4v', 'ogv', 'webm']

                            if (!_.isEmpty(curMediaSubType)) {

                                previewPanel = '<video width="400" height="300" controls><source src="' + curMediaUrl + '" type="';

                                switch (curMediaSubType) {
                                    case 'mp4':
                                    case 'm4v': {
                                        previewPanel += 'video/mp4';
                                    } break;

                                    case 'webm': {
                                        previewPanel += 'video/webm';
                                    } break;

                                    case 'ogv': {
                                        previewPanel += 'video/ogg';
                                    } break;

                                    case 'quicktime': {
                                        previewPanel += 'video/quicktime';
                                    } break;

                                }

                                previewPanel += '" /></video>';

                                $($media_container).find('.srp_media_preview').html(previewPanel).addClass('has-data');

                            }

                        } break;


                        case 'audio': {

                            // Supported Types: [ 'mp3', 'm4a', 'm4b', 'wav', 'ogg', 'oga' ]

                            if (!_.isEmpty(curMediaSubType)) {

                                previewPanel = '<audio controls><source src="' + curMediaUrl + '" type="';

                                switch (curMediaSubType) {
                                    case 'mp3':
                                    case 'm4a':
                                    case 'm4b': {
                                        previewPanel += 'audio/mpeg';
                                    } break;

                                    case 'wav': {
                                        previewPanel += 'audio/wav';
                                    } break;

                                    case 'ogg':
                                    case 'oga': {
                                        previewPanel += 'audio/ogg';
                                    } break;

                                }

                                previewPanel += '" /></audio>';

                                $($media_container).find('.srp_media_preview').html(previewPanel).addClass('has-data');

                            }


                        } break;


                        case 'application':
                        case 'text': {

                            var subTypeCssClass = 'file';

                            if (!_.isEmpty(curMediaSubType)) {

                                switch (curMediaSubType) {

                                    case 'tar':
                                    case 'zip':
                                    case 'gz':
                                    case 'gzip':
                                    case 'rar':
                                    case '7z': {
                                        subTypeCssClass = 'compressed';
                                    } break;

                                    case 'pdf': {
                                        subTypeCssClass = 'pdf';
                                    } break;

                                    case 'psd':
                                    case 'xcf': {
                                        subTypeCssClass = 'octet';
                                    } break;

                                    case 'wri':
                                    case 'doc':
                                    case 'docx':
                                    case 'docm':
                                    case 'dotx':
                                    case 'dotm': {
                                        subTypeCssClass = 'ms-word';
                                    } break;

                                    case 'xla':
                                    case 'xls':
                                    case 'xlt':
                                    case 'xlw':
                                    case 'xlsx':
                                    case 'xlsm':
                                    case 'xlsb':
                                    case 'xltm':
                                    case 'xlam': {
                                        subTypeCssClass = 'ms-excel';
                                    } break;

                                    case 'pot':
                                    case 'pps':
                                    case 'ppt':
                                    case 'pptm':
                                    case 'ppsm':
                                    case 'ppsx':
                                    case 'potm':
                                    case 'ppam':
                                    case 'sldx':
                                    case 'sldm': {
                                        subTypeCssClass = 'ms-powerpoint';
                                    } break;

                                    default:
                                        subTypeCssClass = 'file';
                                        break;

                                }

                                previewPanel = '<span class="type-application subtype-' + subTypeCssClass + '"><i class="srpset-trigger-icon"></i></span>';

                                $($media_container).find('.srp_media_preview').html(previewPanel).addClass('has-data');

                            }

                        } break;

                        default: {

                            $($media_container).find('.srp_media_preview').removeClass('has-data');

                        } break;

                    }

                }


                $($media_container).find('input.input_media').attr('value', curMediaUrl);
                $($media_container).find('input.input_type').attr('value', curMediaType);
                $($media_container).find('input.input_subtype').attr('value', curMediaSubType);

                $($media_container).find('.input-button.btn_media_remove').removeClass('hidden');


            }).open();

        });

        // Remove Media
        $(document.body).on('click', '.srpset-element-wrapper.element-upload-media .srpset-upload-media-container .input-button.btn_media_remove', function (e) {

            e.preventDefault();

            var $media_container = $(this).closest('.srpset-upload-media-container');

            $($media_container).find(".input-button.btn_media_remove").addClass('hidden');

            $($media_container).find(".srp_media_preview").empty().removeClass('has-data');

            $($media_container).find("input.input_media").attr('value', '');
            $($media_container).find("input.input_type").attr('value', '');
            $($media_container).find("input.input_subtype").attr('value', '');


        });

    }

    /**
     *
     * Perform all opertaions related to [Elements: Media Upload and Gallery]
     *
     */
    $.ultimate_email_validator.element.gallery = function () {

        var fr_image_upload = '',
            $galllery_container_sort = ''; // Sort Gallery

        // Add New Images
        $(document.body).on('click', '.srpset-element-wrapper.element-upload-gallery .srpset-upload-media-container .input-button.btn_media_upload', function (e) {

            e.preventDefault();

            fr_image_upload = ''; // Return frame to null / Important

            // hide gallery settings used for posts/pages
            wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({

                template: function (view) {
                    return;
                }

            });

            if (_.isUndefined(wp) || !wp.media || !wp.media.gallery) {
                return;
            }

            var btn_upload_current = $(this),
                gallery_container = btn_upload_current.closest('.srpset-upload-media-container'),
                gallery_preview = gallery_container.find('.srp_media_preview'),
                $shortcode_field = gallery_container.find('input.input_media'),
                clear_button = gallery_container.find('.input-button.btn_media_clear'),

                shortcode = $shortcode_field.val(),
                final_shortcode = '',
                $img_container_current_id = btn_upload_current.closest('.srpset-upload-media-container').attr('id');


            if (!_.isEmpty(shortcode)) {
                final_shortcode = shortcode;
            }
            else {
                final_shortcode = '[gallery ids="0"]';
            }


            fr_image_upload = wp.media.gallery.edit(final_shortcode);

            // On Edit
            fr_image_upload.state('gallery-edit').on('update', function (selection) {

                // gallery_preview.html('');

                var element, gallery_panel = '', img_preview;

                var ids = selection.models.map(function (e) {

                    element = e.toJSON();

                    img_preview = (!_.isUndefined(element.sizes) && !_.isUndefined(element.sizes.thumbnail)) ? element.sizes.thumbnail.url : element.url;

                    gallery_panel += '<span data-id="' + element.id + '" title="' + element.title + '"><img src="' + img_preview + '" alt="" /><i class="srpset-trigger-icon remove"></i></span>';

                    return e.id;

                });

                gallery_preview.empty().html(gallery_panel).addClass('has-data');

                final_shortcode = '[gallery ids="' + ids.join(',') + '"]';

                $shortcode_field.attr('value', final_shortcode);

                clear_button.removeClass('hidden');

            });

        });

        // Sort Gallery
        //$(".srpset-upload-media-container .srp_media_preview").sortable("destroy");

        $('.srpset-element-wrapper.element-upload-gallery .srpset-upload-media-container.gallery-sortable').unbind('sortable').sortable({

            placeholder: 'ui-state-highlight',
            items: '.srp_media_preview > span',
            cursor: 'move',
            //handle: ".block-header.repeater-block-title-header.handle-sorting .section-right .controls-container .btn-control.move-item .srp-btn",

            //containment: 'parent',

            //forceHelperSize: true,
            //forcePlaceholderSize: true,

            start: function (event, ui) {

                $galllery_container_sort = ui.item.closest('.srpset-upload-media-container');

            },

            stop: function () {

                $.ultimate_email_validator.update_multiple_images($galllery_container_sort);

            }

        }).disableSelection();


        // Remove one item
        $(document.body).on('click', '.srpset-element-wrapper.element-upload-gallery .srpset-upload-media-container .srp_media_preview span i.remove', function () {

            var $gallery_container = $(this).closest('.srpset-upload-media-container');

            $(this).parent('span').css('border-color', '#f03').fadeOut(300, function () {

                $(this).remove();
                $.ultimate_email_validator.update_multiple_images($gallery_container);

            });

        });


        // Element: Gallery - Clear Gallery Images
        $(document.body).on('click', '.srpset-element-wrapper.element-upload-gallery .srpset-upload-media-container .input-button.btn_media_clear', function (e) {

            e.preventDefault();

            var $gallery_container = $(this).closest('.srpset-upload-media-container');

            $(this).addClass('hidden');

            $($gallery_container).find('.srp_media_preview').empty().removeClass('has-data');

            $($gallery_container).find('input.input_media').attr('value', '');


        });

    }

    /**
     * 
     * Element: ColorPicker
     *
     **/
    $.ultimate_email_validator.element.colorpicker = function (selectors) {

        selectors.each(function () {

            var colorTrigger = $(this).find('.trigger'),
                is_alpha = colorTrigger.attr('data-alpha'),
                align = colorTrigger.attr('data-align'),
                default_color = colorTrigger.attr('data-default-color');


            colorTrigger.colorpicker({

                format: (is_alpha == 'true') ? 'rgba' : 'hex',
                align: (align == 'right') ? 'right' : 'left',
                colorSelectors: {
                    'default': default_color,
                    'clear': '#ffffff',
                    'red': '#FF0000',
                    'gray777': '#777777',
                    'primary': '#337ab7',
                    'success': '#5cb85c',
                    'info': '#5bc0de',
                    'warning': '#f0ad4e',
                    'danger': '#d9534f'
                },

                customClass: 'colorpicker-2x',

                sliders: {
                    saturation: {
                        maxLeft: 200,
                        maxTop: 200
                    },
                    hue: {
                        maxTop: 200
                    },
                    alpha: {
                        maxTop: 200
                    }
                },

            }).on('showPicker', function () {

                colorTrigger.closest('.srpset-color-picker').addClass('picker-opened');

            }).on('hidePicker', function () {

                colorTrigger.closest('.srpset-color-picker').removeClass('picker-opened');

            });

        });

    }

    /**
     * 
     * Element: ColorPicker - Helper
     *
     * Add Default and Clear buttons triggers
     *
     **/
    $.ultimate_email_validator.element.colorpicker_actions = function () {

        // Default & Clear
        $(document.body).on('click', '.srpset-element-wrapper.element-wpcolor .srpset-color-picker .trigger .color-result-container .color-actions .srpset-trigger-icon, .srpset-element-wrapper.element-background .srpset-color-picker .trigger .color-result-container .color-actions .srpset-trigger-icon', function (e) {

            e.preventDefault();

            var data_action = $(this).attr('data-action'),
                target_color = '';

            switch (data_action) {

                case 'default':

                    target_color = $(this).attr('data-color');

                    if (!_.isEmpty(target_color)) {

                        $(this).closest('.trigger').colorpicker('setValue', target_color);

                    }

                    $(this).closest('.trigger').colorpicker('hide');

                    break;

                case 'clear':

                    $(this).closest('.color-result-container').find('i.color-preview').css({
                        'background-color': ''
                    });

                    $(this).closest('.color-result-container').find('input.color-text').attr('value', '');

                    $(this).closest('.color-result-container').find('input.color-text').trigger('change');

                    $(this).closest('.trigger').colorpicker('hide');

                    break;

            }

        });

    }


    /**
     *
     * Element: Spinner
     *
     * */
    $.ultimate_email_validator.element.spinner = function (selectors) {

        selectors.each(function () {

            var minValue = $(this).attr('data-min-value'),
                maxValue = $(this).attr('data-max-value'),
                stepValue = $(this).attr('data-step-value'),
                newItem = $(this).attr('data-new-item'),
                numberFormat = $(this).attr('data-number-format'),
                defaultValue = $(this).attr('data-default-value'),
                currentValue = $(this).find('.srpset-spinner').val(),
                finalValue = (newItem == 'true') ? defaultValue : currentValue;


            $(this).find('.srpset-spinner').spinner({

                min: minValue,
                max: maxValue,
                step: stepValue,
                value: finalValue,
                numberFormat: numberFormat,

                spin: function (event, ui) {

                    if (ui.value < minValue) {

                        $(this).spinner('value', minValue);

                        return false;

                    } else if (ui.value > maxValue) {

                        $(this).spinner('value', maxValue);

                        return false;

                    }

                }

            });

            /* Fix: Initial Arrow created by jQuery UI JS */
            $(this).find('.ui-spinner .ui-icon').text('');

        });

    }


    /**
     * 
     * Element: Slider
     *
     **/
    $.ultimate_email_validator.element.slider = function (selectors) {

        selectors.each(function () {

            var sliderMainId = $(this).attr('data-slider-id'),
                sliderValueInputId = $(this).attr('data-slidervalueinput-id'),
                minValue = parseInt($(this).attr('data-min-value'), 10),
                maxValue = parseInt($(this).attr('data-max-value'), 10),
                stepValue = parseInt($(this).attr('data-step-value'), 10),
                newItem = $(this).attr('data-new-item'),
                valueModified = $(this).attr('data-modified'),
                defaultValue = parseInt($(this).attr('data-default-value'), 10),
                currentValue = parseInt($(this).find('.input_value').val(), 10),

                finalValue = ((newItem == 'true') && (valueModified == 'false')) ? defaultValue : currentValue;


            $(this).find('.input_slider').slider({
                range: "min",
                animate: true,
                min: minValue,
                max: maxValue,
                step: stepValue,
                value: finalValue,
                slide: function (event, ui) {

                    if (valueModified == 'false') {
                        $(this).closest('.srpset-element-wrapper.element-slider').attr('data-modified', 'true');
                    }

                    $(this).next('.input_value').attr('value', ui.value);

                }

            });

        });

    }


    /**
     * 
     * Element: Range Slider
     *
     **/
    $.ultimate_email_validator.element.range_slider = function (selectors) {

        selectors.each(function () {

            var sliderMainId = $(this).attr('data-slider-id');

            if (!(_.isEmpty(sliderMainId))) {

                var sliderValueInputId = $(this).attr('data-slidervalueinput-id'),
                    minValue = parseInt($(this).attr('data-min-value'), 10),
                    maxValue = parseInt($(this).attr('data-max-value'), 10),
                    stepValue = parseInt($(this).attr('data-step-value'), 10),
                    unit = $(this).attr('data-unit'),
                    unitPosition = $(this).attr('data-unit-position'),
                    newItem = $(this).attr('data-new-item'),
                    valueModified = $(this).attr('data-modified'),

                    defaultValueMin = $(this).attr('data-default-value-min'),
                    defaultValueMax = $(this).attr('data-default-value-max'),
                    currentValueMin = $(this).attr('data-current-value-min'),
                    currentValueMax = $(this).attr('data-current-value-max'),
                    finalValueMin = ((newItem == 'true') && (valueModified == 'false')) ? defaultValueMin : currentValueMin,
                    finalValueMax = ((newItem == 'true') && (valueModified == 'false')) ? defaultValueMax : currentValueMax,
                    finalUserValue = '',
                    finalDbValue = '';


                $(this).find('#' + sliderMainId).slider({
                    range: true,
                    animate: true,
                    min: minValue,
                    max: maxValue,
                    step: stepValue,
                    values: [finalValueMin, finalValueMax],
                    slide: function (event, ui) {

                        var elementWrapper = $(this).closest('.srpset-element-wrapper.element-range-slider');

                        if (valueModified == 'false') {
                            elementWrapper.attr('data-modified', 'true');
                        }

                        elementWrapper.attr('data-current-value-min', ui.values[0]);
                        elementWrapper.attr('data-current-value-max', ui.values[1]);

                        if (unitPosition == 'before') {
                            finalUserValue = unit + ui.values[0] + ' ' + unit + ui.values[1];
                        }
                        else {
                            unitPosition = 'after';
                            finalUserValue = ui.values[0] + unit + ' ' + ui.values[1] + unit;
                        }

                        // User Value (Not for DB)
                        elementWrapper.find('.element-inner .input_value_user').attr("value", finalUserValue);

                        // For DB
                        elementWrapper.find('#' + sliderValueInputId + '_unitposition').attr("value", unitPosition);
                        elementWrapper.find('#' + sliderValueInputId + '_unit').attr("value", unit);
                        elementWrapper.find('#' + sliderValueInputId + '_min').attr("value", ui.values[0]);
                        elementWrapper.find('#' + sliderValueInputId + '_max').attr("value", ui.values[1]);

                    }

                });

            }

        });

    }


    /**
     * 
     * Element: Radio Images
     *
     **/
    $.ultimate_email_validator.element.radio_images = function () {

        // On Load
        $(".srpset-element-wrapper.element-optionimages .images-list input.input_option_image:checked").parent().addClass("selected");

        // On Click
        $(document.body).on('click', '.optionimages-container .images-list .checkbox-select', function (e) {

            e.preventDefault();

            var this_check = $(this),
                container_id = this_check.closest('.images-list').attr('id');

            $("#" + container_id + " li").removeClass("selected");
            //$("#" + container_id + " li :radio").prop("checked", false);

            this_check.parent().addClass("selected");
            this_check.parent().find(":radio").prop("checked", "checked");

        });


        /* Special Switch - Radio Images On Click */
        $(document.body).on('click', '.srpset-element-wrapper.element-optionimages .images-list .checkbox-select', function (e) {

            e.preventDefault();

            var this_check = $(this);

            if (!this_check.parent().find(":radio").is(':checked')) {
                return;
            }

            var special_swfold_fold_group = '',
                special_swfold_value = this_check.parent().find(":radio").val(),
                special_swfold_container = '';

        });

    }

    
    /**
     * Get User-Defined titles for Repeaters and Mini-Repeaters controls on page load 
     *
     **/
    $.ultimate_email_validator.get_repeater_js_title = function (container, controlType, controlSource, userValue) {

        switch (controlType) {

            case 'select': {

                if (controlSource == 'mini_repeater') {
                    container.closest('.mini-rep-item.option-item.mini-repeater-item').find('.block-header.mini-rep-header .section-left .user-js-title').addClass('active');
                    container.closest('.mini-rep-item.option-item.mini-repeater-item').find('.block-header.mini-rep-header .section-left .user-js-title').text(userValue);
                }
                else if (controlSource == 'repeater') {
                    container.closest('.parent-rep-item.option-item.builder-item').find('.block-header.parent-rep-header .section-left .user-js-title').addClass('active');
                    container.closest('.parent-rep-item.option-item.builder-item').find('.block-header.parent-rep-header .section-left .user-js-title').text(userValue);
                }

            } break;

            case 'text': {

                if (controlSource == 'mini_repeater') {
                    container.closest('.mini-rep-item.option-item.mini-repeater-item').find('.block-header.mini-rep-header .section-left .user-js-title').addClass('active');
                    container.closest('.mini-rep-item.option-item.mini-repeater-item').find('.block-header.mini-rep-header .section-left .user-js-title').text(userValue);
                }
                else if (controlSource == 'repeater') {
                    container.closest('.parent-rep-item.option-item.builder-item').find('.block-header.parent-rep-header .section-left .user-js-title').addClass('active');
                    container.closest('.parent-rep-item.option-item.builder-item').find('.block-header.parent-rep-header .section-left .user-js-title').text(userValue);
                }

            } break;

            default:

        }

    }
    

    /**
     * All Elements - qTooltips
     * Support live change tip texts
     */
    $.ultimate_email_validator.qtooltip = function () {

        $(document.body).on('mouseenter', '[data-jqueryui-tooltip="true"]', function (event) {

            $(this).qtip({

                content: {
                    attr: 'title', // Tell qTip2 to look inside this attr for its content
                },

                position: {
                    at: 'top center',
                    my: 'bottom center'
                },

                overwrite: false, // Don't overwrite tooltips already bound

                show: {
                    event: event.type, // Use the same event type as above
                    ready: true // Show immediately - important!
                }

            }, event);

        });

    }


})(jQuery);