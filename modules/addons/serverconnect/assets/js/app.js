/**
 * ServerConnect for WHMCS
 *
 * @package    WHMCS
 * @author     Lee Mahoney <lee@leemahoney.dev>
 * @copyright  Copyright (c) Lee Mahoney 2022
 * @license    MIT License
 * @version    0.0.1
 * @link       https://leemahoney.dev
 */

$(document).ready(function () {

    $.extend($.expr[":"], {
        'caseInsensitiveContains': function(elem, i, match) {
            return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || '').toLowerCase()) >= 0;
        }
    });

    $('.search').keyup(function() {
        
        var searchTerm = $(this).val();

        $('.server-group').hide();
        $('.server-list .server-item').hide()
            .removeHighlight()
            .filter('.server-item')
            .filter(':caseInsensitiveContains("' + searchTerm + '")')
            .highlight(searchTerm)
            .show()
            .parent().parent().show();

            if (searchTerm.length > 0) {
                $('.clear-button').fadeIn();
            } else {
                $('.clear-button').fadeOut();
            }
        
    });

    $('.clear-button').click(function() {
        clearFilter();
    });

    $(document).keyup(function(e) {
        if (e.keyCode == 27) {
            clearFilter();
        }
    });

    function clearFilter() {
        $('.search').val('').focus();
        $('.server-group').show();
        $('.server-list .server-item').removeHighlight().show();
        $('.clear-button').fadeOut();
    }

    $('.server-list .server-item').click(function() {
        redirectToServer($(this).attr('data-server-id'));
        return false;
    });

    $('.server-list .server-item').bind('contextmenu',function(e){
        redirectToServerNewTab($(this).attr('data-server-id'));
        return false;
    }); 

});