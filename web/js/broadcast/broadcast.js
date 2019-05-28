/**
 * Created by lsouai on 15/02/16.
 */

/*--------------------------------------------------------------------------------------------*/
/*                                      INITIALISE THE TARGET TREE
 /*--------------------------------------------------------------------------------------------*/
function initJsTree() {


    var temp = [];
    var selectedElmsIds = [];
    var count = 0;

    $("#jsTreeTargets div").each(function () {
        var id = "#" + $(this).attr("id");

        $(id).jstree({
            "core": {
                "themes": {
                    "name": "proton",
                    "dots": false,
                    "responsive": true,
                    "icons": false
                }
            },
            "types": {
                "valid_children": ["ml"],
                "types": {
                    "default": {
                        "valid_children": ["default"]
                    }
                }
            },
            "plugins": ["themes", "html_data", "ui", "checkbox", "types", "changed", "search"]

        }).on('changed.jstree ', function (e, data) {
            countSelectedTargets();

            //clear input targets (hidden)
            $("#broadcast_message_targets").val("");
            //get selected targets
            var selectedTargets = getSelectedTargets();
            //add to input field
            if (selectedTargets.length != 0){
                $("#broadcast_message_targets").val(selectedTargets);
            }

        });

    });




}

/*--------------------------------------------------------------------------------------------*/
/*                         OPEN A MODAL CONTAINING A TAB OF PREDEFINED BROADCAST
 /*--------------------------------------------------------------------------------------------*/
function getPredefinedBroadcast(url) {
    $.ajax(
        {
            type: 'POST',
            url: url,
            beforeSend: function () {
                $("#modalPredefined").modal();
                //when modal is opened show the loader
                $('#modalPredefined  #modal-body').html("<span class='loader-middle'></span>");

            },
            complete: function (data) {
                //show data and remove loader
                $('#modalPredefined  #modal-body').html(data.responseText).remove('.loader-middle');
                $("#modalPredefined").modal();
            },
            error: function (xhr) {
                //hide wheel and show error div
                $('#modalPredefined  #modal-body').html(xhr.status + " : " + xhr.responseText);

            }

        });
}

/*--------------------------------------------------------------------------------------------*/
/*                         INTERCEPT FORM EGI SUBMIT TO MAKE VERIFICATIONS ON JSTREE
/*                          AND SHOW BROADCAST MODAL CONFIRMATION
 */
 /*--------------------------------------------------------------------------------------------*/
function verifyBeforeSendBd(url) {

        var selectedTargets = getSelectedTargets();

        //test if there is at least 1 target selected before send
        if (selectedTargets.length == 0) {

            //show error message under jstree and color counter in red
            $("#counter").parents("span").removeClass("badge-primary").addClass("badge-danger");
            if ($("#targetError").length == 0) {
                $('#jsTreeTargets').after("<div id='targetError' class='card card-danger card-body'><p style='color:#a94442; font-weight:bold;'>You must selected at least one target to send your mail</p></div>");
            }
        } else if ($("#formContactEGI").valid()) {

            //get targets list
            $.ajax(
                {
                    type: 'POST',
                    dataType: 'json',
                    url: url,
                    data: {'targets': selectedTargets.join(",")},
                    beforeSend: function() {
                        $("#modalVerify #SummaryBdbody #targetsList").html("");
                    },
                    success: function (data) {
                        $.each(data.targets, function (key, target) {

                            //add target list to verify modal targets part
                            if ($.isArray(target.item)) {

                                //search if all targets have been selected
                                var found = false;
                                $.each(target.item, function(i, val) {
                                    if (val.indexOf("all") >= 0 || val.indexOf("global") >=0) {
                                        found = true;
                                        return false;
                                    }
                                });
                                $("#modalVerify #SummaryBdbody #targetsList").append("<p id='" + key.replace(/ /g, '').replace(/-/g,'') + "'><strong>[" + target.label + "]</strong>&nbsp;&nbsp;</p>");

                                //if all targets are selected in a node show "all"
                                if (found) {
                                    $("#modalVerify #SummaryBdbody #targetsList #" + key.replace(/ /g, '').replace(/-/g, '')).append("all");
                                //else show all targets selected
                                } else {
                                    $.each(target.item, function () {
                                        $("#modalVerify #SummaryBdbody #targetsList #" + key.replace(/ /g, '').replace(/-/g, '')).append(this + ", ");
                                    });
                                }

                            } else {
                                $("#modalVerify #SummaryBdbody #targetsList #" + key.replace(/ /g, '').replace(/-/g,'')).append(this);
                            }

                        });
                    },
                    error: function (xhr) {
                        //hide wheel and show error div
                        $('#modalVerify #SummaryBdbody #targetsList').text(xhr.status + " : " + xhr.responseText);

                    }

                });

            //add form elements to verify modal
            $("#modalVerify #SummaryBdbody #verifName").text($("#broadcast_message_author_cn").val());
            $("#modalVerify #SummaryBdbody #verifMail").text($("#broadcast_message_author_email").val());
            $("#modalVerify #SummaryBdbody #verifCC").text($("#broadcast_message_cc").val() == '' ? 'No CC' : $("#broadcast_message_cc").val());
            $("#modalVerify #SummaryBdbody #verifConfirm").text($("#broadcast_message_confirmation").val() == 1 ? 'yes' : 'no');
            $("#modalVerify #SummaryBdbody #verifPubType").text($("#broadcast_message_publication_type option:selected").text());
            $("#modalVerify #SummaryBdbody #verifSubject").text($("#broadcast_message_subject").val());
            $("#modalVerify #SummaryBdbody #verifBody").html($("#broadcast_message_body").val().replace(/[\n\r]/g, '<br>'));

            //display modal
            $("#modalVerify").modal('show');


        }

}

/*--------------------------------------------------------------------------------------------*/
/*                        SUBMIT FORM EGI
 /*--------------------------------------------------------------------------------------------*/
function sendMailContactEGICommunities() {
    $('.loader-wrapper').show();
    $('.loader-wrapper .loader').show();
    $('#formContactEGI').submit();
}

/*--------------------------------------------------------------------------------------------*/
/*                         ON CLICK ON "+" BUTTON IN TR BROADCAST
 /*--------------------------------------------------------------------------------------------*/
function showTrDetail(btn, type, elt) {
    var div = '# style="font-size: 24px;"_'+ type+"_"+elt;

    if ($(div).is(":visible")) {
        $(div).slideUp("slow");
        $(btn).find(".fa").removeClass("fa-minus").addClass("fa-plus");
    } else {
        $(div).slideDown("slow");
        $(btn).find(".fa").removeClass("fa-plus").addClass("fa-minus");

    }
}

/*--------------------------------------------------------------------------------------------*/
/*                         GET THE LIST OF SELECTED TARGET TO PUT IN TARGETS INPUT (HIDDEN)
 /*--------------------------------------------------------------------------------------------*/
function getSelectedTargets() {
    //get list of selected elements in jstree
    var selectedTargets = [];

    //var showSelectedTargets = [];

    //var targetsAll = [];

    $("#jsTreeTargets div").each(function () {
        var id = "#" + $(this).attr("id");
        var selectedElmsIds = $(id).jstree("get_selected");


        //remove father in list of selected targets
        if (selectedElmsIds.length != 0) {

            //var found = false;
            //$.each(selectedElmsIds, function(i, val) {
            //    if (val.indexOf("all") >= 0) {
            //        found = true;
            //        showSelectedTargets.push(val);
            //        return false;
            //    }
            //});
            //
            //if (!found) {
            //    showSelectedTargets.push(selectedElmsIds);
            //
            //}

            selectedElmsIds = selectedElmsIds.filter(function(s){

                if (!(~s.indexOf("all"))) {
                    return !(~s.indexOf("all"));
                } else {
                    return true;
                }
            });


            selectedTargets.push(selectedElmsIds);


        }

    });


    return selectedTargets;
}