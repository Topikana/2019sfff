/**
 * Created by frebault on 04/01/16.
 */


$(document).ready(function () {
    $(".loader").hide();
    $(".loader-wrapper").hide();

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $(function () {
        $('[data-toggle="popover"]').popover();
    });
/***********************************************************************************************************************
*
*                                                      Menu
*
************************************************************************************************************************/

    $(".dropdownNavBar").hover(
        function () {
            $('.dropdown-menu', this).stop(true, true).slideDown("fast");
            $(this).toggleClass('open');
        },
        function () {
            $('.dropdown-menu', this).stop(true, true).slideUp("fast");
            $(this).toggleClass('open');
        }
    );

/***********************************************************************************************************************
*
*                                                  End Menu
*
************************************************************************************************************************/

/***********************************************************************************************************************
*
*                                                  change glyphicon in collapse
*
************************************************************************************************************************/

    //change glyphicon on collapse
    $('.collapse').on('shown.bs.collapse', function () {
        $("#"+$(this).attr("aria-labelledby")).find(".fa-chevron-down").removeClass("fa-chevron-down").addClass("fa-chevron-up");
    });

    $('.collapse').on('hidden.bs.collapse', function () {
        $("#"+$(this).attr("aria-labelledby")).find(".fa-chevron-up").removeClass("fa-chevron-up").addClass("fa-chevron-down");
    });



/***********************************************************************************************************************
*
*                                                  End change glyphicon
*
************************************************************************************************************************/


/***********************************************************************************************************************
*
*                                                 add tooltip when there is help in form
*
************************************************************************************************************************/
    $(".help").find("*").each(function() {
        var attr= $(this).attr("help");

        if (typeof attr != typeof undefined && attr != false) {
            var title = $(this).parents(".help").children(".infoLabel").text();



            $(this).parents(".help").children("label").append('&nbsp;&nbsp;<a tabindex="0" role="button" data-toggle="popover" ' +
                'data-trigger="focus" title="'+title+'" data-content="'+attr+'" data-html="true"><span class="fa fa-info-circle"></span></a>');

        }


    });
});



function refreshHelp(form){
    $("#"+ form).find("*").each(function() {
        var attr= $(this).attr("help");

        if (typeof attr != typeof undefined && attr != false) {
            var title = $(this).parents(".help").children(".infoLabel").text();

            $(this).parents(".help").children("label").append('&nbsp;&nbsp;<a tabindex="0" role="button" data-toggle="popover" ' +
                'data-trigger="focus" title="'+title+'" data-content="'+attr+'" data-html="true"><span class="fa fa-info-circle"></span></a>');
        }


    });
}


/***********************************************************************************************************************
 *
 *                                                        validate textarea
 *
 ***********************************************************************************************************************/
function validateTextarea(elt) {
    var errorMsg = $(elt).attr('title');
    var textarea = elt;
    var pattern = new RegExp('^' + $(textarea).attr('pattern') + '$');
    // check each line of text
    $.each($(elt).val().split("\n"), function () {
        // check if the line matches the pattern
        var hasError = !this.match(pattern);
        if (typeof textarea.setCustomValidity === 'function') {
            textarea.setCustomValidity(hasError ? errorMsg : '');
        } else {
            // Not supported by the browser, fallback to manual error display...
            $(textarea).toggleClass('error', !!hasError);
            $(textarea).toggleClass('ok', !hasError);
            if (hasError) {
                $(textarea).attr('title', errorMsg);
            } else {
                $(textarea).removeAttr('title');
            }
        }
        return !hasError;
    });
}

