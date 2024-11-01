(function ($) {

    'use strict';

    var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver,
        loaded = false,
        ie11 = window.navigator.userAgent.indexOf('Trident/7.0') > 0;

    $.ultimate_email_validator = $.ultimate_email_validator || {};

    // Options Panel
    $.ultimate_email_validator.panel = $.ultimate_email_validator.panel || {};
    
    
    /**
     * FIRE UP
     * 
     * NOTES:
     * $.ultimate_email_validator.ajax_vars Included in {core.js}
     *
     */
    $(document).ready(function () {

        $.ultimate_email_validator.panel.tabs();

        $.ultimate_email_validator.panel.child_groups();
        
    });
    
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
    
})(jQuery);