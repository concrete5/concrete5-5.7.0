// Import the CMS foundation in order to support editing, toolbar, panel functionality
import '@concretecms/bedrock/assets/cms/js/base';

// Import the frontend foundation for themes.
// Has to come after cms base because cms base registers the Vue Manager
import '@concretecms/bedrock/assets/bedrock/js/frontend';

// Import the CMS components and the backend components
// Note, this currently isn't technically necessary, but I'm putting here so we have some place to put components
// as we create them.
// ---
// import BoardInstanceRule from './components/Board/InstanceRule'
// ---
// Note, we actually have no components that are in backend that aren't in cms, becauase we're moving InstanceRule into
// the CMS namespace. But let's keep this here so we can remember it's an option.

Concrete.Vue.createContext('backend', {
//    BoardInstanceRule
}, 'cms')

// Desktops and waiting for me
import '@concretecms/bedrock/assets/desktop/js/frontend';

// Calendar
import '@concretecms/bedrock/assets/calendar/js/backend';

// Advanced search and search bars
import './search/advanced-search-launcher';
import './search/preset-selector';
import './search/results-table';
import './file-manager/file-manager';
import './page-search/page-search';
import './groups/group-manager';

// Custom UI for Pages
import './translator';

// Marketplace support
import './remote-marketplace';
import 'magnific-popup'
import '@concretecms/bedrock/assets/imagery/js/frontend/responsive-slides';
import components from "@concretecms/bedrock/assets/cms/components/index";

var setupResultMessages = function() {
    if ($('#ccm-dashboard-result-message').length > 0) {
        if ($('.ccm-pane').length > 0) {
            var pclass = $('.ccm-pane').parent().attr('class');
            var gpclass = $('.ccm-pane').parent().parent().attr('class');
            var html = $('#ccm-dashboard-result-message').html();
            $('#ccm-dashboard-result-message').html('<div class="' + gpclass + '"><div class="' + pclass + '">' + html + '</div></div>').fadeIn(400);
        }
    } else {
        $("#ccm-dashboard-result-message").fadeIn(200);
    }
};

var setupAdvancedSearchLinks = function() {
    $('a[data-launch-dialog=advanced-search]').concreteAdvancedSearchLauncher();
}

var setupFavorites = function() {
    var $addFavorite = $('a[data-bookmark-action=add-favorite]'),
        $removeFavorite = $('a[data-bookmark-action=remove-favorite]'),
        url = false,
        $link;

    if ($addFavorite.length) {
        url = CCM_DISPATCHER_FILENAME + '/ccm/system/panels/dashboard/add_favorite';
        $link = $addFavorite;
    } else if ($removeFavorite.length) {
        url = CCM_DISPATCHER_FILENAME + '/ccm/system/panels/dashboard/remove_favorite';
        $link = $removeFavorite;
    }

    if (url) {
        $link.on('click', function(e) {
            e.preventDefault();
            $.concreteAjax({
                dataType: 'json',
                type: 'GET',
                data: {'cID': $(this).attr('data-page-id'), 'ccm_token': $(this).attr('data-token')},
                url: url,
                success: function(r) {
                    if (r.action == 'remove') {
                        $link.attr('data-bookmark-action', 'add-favorite');
                        $link.find('.icon-bookmark').removeClass('bookmarked');
                    } else {
                        $link.attr('data-bookmark-action', 'remove-favorite');
                        $link.find('.icon-bookmark').addClass('bookmarked');
                    }
                    $link.off('click');
                    setupFavorites();
                }
            });
        });
    }
};

var setupDetailsURLs = function() {
    $('tr[data-details-url]').each(function() {
        $(this).hover(
            function() {
                $(this).addClass('ccm-search-select-hover');
            },
            function() {
                $(this).removeClass('ccm-search-select-hover');
            }
        )
            .on('click', function(e) {
                if ($(e.target).is('td')) {
                    window.location.href = $(this).data('details-url');
                }
            });
    });
    $('div.ccm-details-panel[data-details-url]').each(function() {
        $(this)
            .on('click', function() {
                window.location.href = $(this).data('details-url');
            });
    });
};

var setupTooltips = function() {
    if ($("#ccm-tooltip-holder").length == 0) {
        $('<div />').attr('id','ccm-tooltip-holder').attr('class', 'ccm-ui').prependTo(document.body);
    }
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('.launch-tooltip'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            container: '#ccm-tooltip-holder'
        })
    })
};

var setupDialogs = function() {
    $('.dialog-launch').dialog();

    $('div#ccm-dashboard-page').on('click', '[data-dialog]', function() {
        if ($(this).attr('disabled')) {
            return false;
        }

        var width = $(this).attr('data-dialog-width');
        if (!width) {
            width = 320;
        }
        var height = $(this).attr('data-dialog-height');
        if (!height) {
            height = 'auto';
        }
        var title;
        if ($(this).attr('data-dialog-title')) {
            title = $(this).attr('data-dialog-title');
        } else {
            title = $(this).text();
        }
        var element = 'div[data-dialog-wrapper=' + $(this).attr('data-dialog') + ']';
        jQuery.fn.dialog.open({
            element: element,
            modal: true,
            width: width,
            title: title,
            height: height
        });
    });

};

var setupPrivacyPolicy = function() {

    $('div#ccm-dashboard-page').on('click', 'button[data-action=agree-privacy-policy]', function() {
        $('div.ccm-dashboard-privacy-policy').hide();
        var url = CCM_DISPATCHER_FILENAME + '/ccm/system/accept_privacy_policy';
        $.concreteAjax({
            dataType: 'json',
            data: {'ccm_token': $(this).attr('data-token')},
            type: 'POST',
            url: url,
            success: function(r) {

            }
        });
    });
};

var setupHeaderMenu = function() {
    var $buttons = $('.ccm-dashboard-header-buttons'),
        $menu = $('header div.ccm-dashboard-header-menu');
    if ($buttons.length) {
        if ($buttons.parent().get(0).nodeName.toLowerCase() == 'form') {
            $menu.append($buttons.parent());
        } else {
            $menu.append($buttons);
        }
    }
};

var setupSiteListMenuItem = function() {
    $('select[data-select=ccm-header-site-list]').on('changed.bs.select', function() {
        window.location.href = $(this).val()
    })
};

window.ConcreteEvent.subscribe("ConcreteServerEventThumbnailGenerated", function(e, data) {
    e.preventDefault();

    var $el = $(".ccm-image-wrapper[data-file-id='" + data.fileId + "'][data-file-version-id='" + data.fileVersionId + "'][data-thumbnail-type-handle='" + data.thumbnailTypeHandle + "']");

    if ($el.length) {
        if (data.thumbnailUrl.substr(0, CCM_REL.length) !== CCM_REL) {
            // If the worker is executed in CLI mode the generated url doesn't contain the base path.
            // So we manually prepend this to the image urls. (required if concrete is running within a sub directory)
            data.thumbnailUrl = CCM_APPLICATION_URL + data.thumbnailUrl;
        }

        var $img = $("<img/>")
            .attr("src", data.thumbnailUrl)
            .attr("alt", data.fileName)
            .attr("class", $el.attr("class"))
            .removeClass("ccm-image-wrapper");

        $el.replaceWith($img);
    }

    return false;
});

setupTooltips();
setupResultMessages();
setupSiteListMenuItem();
setupDialogs();
setupDetailsURLs();
setupFavorites();
setupAdvancedSearchLinks();
setupHeaderMenu();
setupPrivacyPolicy();
