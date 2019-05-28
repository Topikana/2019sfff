/**
 * Created by frebault on 25/01/16.
 */

$(document).ready(function () {


    affMasqBtnPrep();
    $(".checkboxRegistration").change(function () {
        affMasqBtnPrep();
    });

    //remove error message on vo already used
    $("#vo_VoHeader_name").on("input", function() {
        $("#alreadyUsedName").remove();
    });



    $("#voForm").validate();

    // Submit voForm
    $("#voForm").submit(function (event) {

        //test if vo has already been used
        if ($("#vo_VoHeader_serial").length == 0) {
            $.ajax({
                type: 'POST',
                dataType: "json",
                data: {
                    name: $("#vo_VoHeader_name").val()
                },
                url: $("footer").text(),
                async: false,
                success: function (data) {
                    if (data.checkedName) {
                        $("html, body").animate({scrollTop: 0}, "slow");
                        if ($("#alreadyUsedName").length == 0) {
                            $("#vo_VoHeader_name").after('<label id="alreadyUsedName" style="color: #cd0a0a">This vo name has already been used, please enter another Vo name.</label>');
                        }
                        event.preventDefault();

                    } else {
                        $("#alreadyUsedName").remove();
                    }
                },
                error: function () {
                    event.preventDefault();
                }
            });
        }


        var type = $("#vo_VoHeader_aup_type").val();
        $("#vo_VoHeader_aup").val($("#vo_VoHeader_aup" + type).val());
        if (!$("#voForm").valid()) {
            event.preventDefault();
        }
        if ($("#listdisciplines :input").length < 1) {
            alert("please select at least one discipline");
            event.preventDefault();
        }

        if ($("#supportedServices :checked").length < 1) {
            alert("please select at least one Supported Service");
            event.preventDefault();
        }


        //check if there is cvmfs to save
        var endpointTab = [];
        var t = $('#tabcvmfs').DataTable();


        if (t.data().any() ) {

            $('#tabcvmfs tbody tr td:first-child').each(function () {
                endpointTab.push($(this).text());
            });
            $("#vo_VoRessources_cvmfs").val(endpointTab);
        } else {
            $("#vo_VoRessources_cvmfs").val(null);
        }



    });

    // init voHeader Values
    $("#vo_VoHeader_vo_scope").val($("#vo_VoHeader_scope_id").val());


    filterAupDisplay();

    // display vo Acknowledgment
    //if ($("#vo_VoAcknowledgmentStatements_grantid").val() != "") {
    //    $('#vo_VoAcknowledgmentStatements_as_need_1').attr('checked', 'checked');
    //    changeAcknowledgment();
    //}

    // display vo Relationship
    if ($("#vo_VoAcknowledgmentStatements_relationShip").val() != "") {
        $('#vo_VoAcknowledgmentStatements_use_relationShip').attr('checked', 'checked');
        changeRelationship();
    }
    $("#vo_VoAcknowledgmentStatements_suggested").text('This work benefited from services provided by the [VO] Virtual Organisation, supported by the national resource providers of the EGI Federation');

    // Discipline tree
    $('#disciplines').jstree({
        'plugins': ["wholerow", "checkbox", "search"],
        'core': {
            'themes': {
                'name': 'proton',
                'responsive': true,
                "icons": false,
            },
            'data': $('.viewDisciplines').val()
        },
        'checkbox': {
            three_state: false,
            cascade: ""
        }
    }).on("changed.jstree", function (e, data) { // Select or Unselect element
        // console.log(data.changed.selected);


        if (data.action == "select_node" ) {
            // case select

            // get parent attribut and test if exist
            var attr = data.node.parent;

            if (typeof attr !== typeof undefined && attr !== "#") {
                // Check if parent level is create
                if ($("#listdisciplines").find("#ul_" + attr).length) {
                    // add to parent list
                    $("#listdisciplines").find("#ul_" + attr).append("<li parent='" + attr + "' id='" + data.node.id + "'><input name='VoDiscipline[" + data.node.id + "]' type='hidden' value='" + data.node.text + "'/>" + data.node.text + "</li>" +
                        "<ul id='ul_" + data.node.id + "'></ul>")
                } else {
                    // select parent element and add
                    $('#disciplines').find("#" + attr).children("a").click();
                    $("#listdisciplines").find("#ul_" + attr).append("<li parent='" + attr + "' id='" + data.node.id + "'><input name='VoDiscipline[" + data.node.id + "]' type='hidden' value='" + data.node.text + "'/>" + data.node.text + "</li>" +
                        "<ul id='ul_" + data.node.id + "'></ul>")
                }
            } else {

                // list first level
                $("#listdisciplines").append("<li class='first' id='" + data.node.id + "'><input name='VoDiscipline[" + data.node.id + "]' type='hidden' value='" + data.node.text + "'/>" + data.node.text + "</li>" +
                    "<ul id='ul_" + data.node.id + "'></ul>")
            }

        }
        if (data.action == "deselect_node")
        {

            // case unselect

            $("#listdisciplines").find("#ul_" + data.node.id).find("li[parent=" + data.node.id + "]").each(function (index, element) {
                $('#disciplines').find("#" + $(element).attr('id')).children("a").click();
            });
            $("#listdisciplines").find("#" + data.node.id).remove();
            $("#listdisciplines").find("#ul_" + data.node.id).remove();
        }
        if (data.action == "ready")
        {

            // case load the tree : simulate selected boxes as a click
            $.each( data.instance._model.data, function( key1, value1 ) {


                    var nodeId=value1.id;
                    var nodeText=value1.text;
                    var attr=value1.parent;
                    var IsSelected=value1.state.selected;
                    
                    if (IsSelected )
                    {

                        if (typeof attr !== typeof undefined && attr !== "#") {
                            // Check if parent level is create
                            if ($("#listdisciplines").find("#ul_" + attr).length) {
                                // add to parent list
                                $("#listdisciplines").find("#ul_" + attr).append("<li parent='" + attr + "' id='" + nodeId+ "'><input name='VoDiscipline[" + nodeId+ "]' type='hidden' value='" + nodeText + "'/>" + nodeText + "</li>" +
                                    "<ul id='ul_" + nodeId+ "'></ul>")
                            } else {
                                // select parent element and add
                                $('#disciplines').find("#" + attr).children("a").click();
                                $("#listdisciplines").find("#ul_" + attr).append("<li parent='" + attr + "' id='" + nodeId+ "'><input name='VoDiscipline[" + nodeId+ "]' type='hidden' value='" + nodeText + "'/>" + nodeText + "</li>" +
                                    "<ul id='ul_" + nodeId+ "'></ul>")
                            }
                        } else {

                            // list first level
                            $("#listdisciplines").append("<li class='first' id='" + nodeId+ "'><input name='VoDiscipline[" + nodeId+ "]' type='hidden' value='" + nodeText + "'/>" + nodeText + "</li>" +
                                "<ul id='ul_" + nodeId+ "'></ul>")
                        }
                        
                    }
                   



            });
        }


    });

    // search discipline
    var to = false;
    $('#disciplines_q').keyup(function () {
        if (to) {
            clearTimeout(to);
        }
        to = setTimeout(function () {
            var v = $('#disciplines_q').val();
            $('#disciplines').jstree(true).search(v);
        }, 250);
    });

    // init discipline tree
    $('.disciplineId').each(function (index) {
        $('#disciplines').find("#" + $(this).val()).children("a").click();
    });

});

/*
 * validation checkbox
 */
function affMasqBtnPrep() {

    var n = $("input:checked[name='prep[]']").length;// Nombre de checkbox cochée
    if (n < 1) {
        $("#btnRegistration").addClass('disabled');// Bouton invisible
    } else {
        $("#btnRegistration").removeClass('disabled'); // Bouton visible
    }
}

/**
 * Hide registrationWelcome
 * Show registration
 */
function checkVoRegistration(){


    if ($('.checkboxRegistration').is(':checked')){
        $("#registrationWelcome").slideUp("slow");
        $("#registration").slideDown("slow");
    }
}


//function changeAcknowledgment() {
//    var value = $('#vo_VoAcknowledgmentStatements_as_need_1').is(':checked');
//    var tag = $('#as_form');
//    if ((value == true)) {
//        $(tag).show();
//        $(tag).children().removeAttr("disabled");
//    }
//    else {
//        $(tag).hide();
//        $(tag).children().attr("disabled", "disabled");
//    }
//}

function changeRelationship() {
    var value = $('.checkboxRegistration').is(':checked');
    var tag = $('#use_relationship');
    if ((value == true)) {
        $(tag).show();
        $(tag).children().removeAttr("disabled");
    }
    else {
        $(tag).hide();
        $(tag).children().attr("disabled", "disabled");
    }
}


function rejectionForm(serial,path) {

    if ($("#rejectionForm" + serial).valid()) {

        var cause = $("#rejectionForm" + serial).find("textarea#cause").val();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            data: {serial: serial, cause: cause},
            url: path,
            success: function () {
                $(".loader-wrapper").show();
                $(".loader-wrapper .loader").show();
            },
            complete: function (xhr) {
                window.location.reload();
            }

        });
        $('#modalRejection' + serial).modal('toggle');


    }
}


/*
 * Dynamic display of AUP form
 */

function filterAupDisplay() {

    var selIndex = $('#vo_VoHeader_aup_type').val();

    switch (selIndex) {
        case 'File':
            $('#aup_url_block').hide();
            $('#vo_VoHeader_aupUrl').removeAttr("required");

            $('#aup_text_block').hide();
            $('#vo_VoHeader_aupText').removeAttr("required");

            $('#aup_file_block').show();
            $('#vo_VoHeader_aupFile_name').attr("required","required");

            break;
        case 'Url':
            $('#aup_text_block').hide();
            $('#vo_VoHeader_aupText').removeAttr("required");

            $('#aup_file_block').hide();
            $('#vo_VoHeader_aupFile_name').removeAttr("required");

            $('#aup_url_block').show();
            $('#vo_VoHeader_aupUrl').attr("required","required");

            break;
        case 'Text':
            $('#aup_url_block').hide();
            $('#vo_VoHeader_aupUrl').removeAttr("required");

            $('#aup_file_block').hide();
            $('#vo_VoHeader_aupfile_name').removeAttr("required");

            $('#aup_text_block').show();
            $('#vo_VoHeader_aupText').attr("required","required");
            if ( $('#vo_VoHeader_aupText').text() == '') {
                $('#vo_VoHeader_aupText').text(
                    "This Acceptable Use Policy applies to all members of [VoName] Virtual Organisation, \n" +
                    "hereafter referred to as the [VO], with reference to use of the EGI Infrastructure. \n" +
                    "The [owner body] owns and gives authority to this policy. \n" +
                    "Goal and description of the [VONAME] VO \n" +
                    "--------------------------------------------------------------------- \n" +
                    "[TO be completed] \n" +
                    "Members and Managers of the [VO] agree to be bound by the Acceptable Usage Rules, [VO] Security Policy and other relevant EGI Policies, and to use the Infrastructure only in the furtherance of the stated goal of the [VO]."
                );
            }

            break;
    }
}

/*
 * Get vomsGroupPanel and show modal
 * url : vomsGroupForm
 */
function entityAction(url, serial, id, entityName) {
    $('#header').slideUp('slow');
    $.ajax({
        type: 'POST',
        data: {
            id: id,
            serial: serial
        },
        url: url,
        async: false,
        complete: function (data) {
            $("#divModal" + entityName).empty();
            $("#divModal" + entityName).append(data.responseText);

            //$("#modal" + entityName).find("h3").html(title);
            $("#modal" + entityName).modal({backdrop: 'static', keyboard: false});

        }
    });
}
/*
 * Get save and hide modal
 * url : vomsGroupForm
 */
function saveEntityAction(url, entity) {
    event.preventDefault();
    $.ajax({
        type: 'POST',
        dataType: 'html',
        data: $("#" + entity + "Form :input").serialize(),
        url: url,
        success: function (data, textStatus) {
            $("#panel" + entity).html(data)
        },
        complete: function () {
            $('#header').slideDown('slow');
        }
    });
    $("#modal" + entity).modal('toggle');
}


/*
 * Delete Voms Group Action
 * url : deleteVomsGroup
 */
function deleteEntityAction(url, serial, entityId, entity, panelID) {
    if (confirm('Do you really want to delete ' + entity + '?')) {
        $.ajax({
            type: 'get',
            dataType: 'html',
            data: {
                id: entityId,
                serial: serial
            },
            url: url,
            success: function (data, textStatus) {
                $('#' + panelID).html(data);
            }

        });
    } else {
        return false;
    }

}

/*
 * Delete Voms Group Action
 * url : deleteVomsGroup
 */
function deleteContactHasProfil(url, serial, entityId, entity, panelID, profile) {
    if (confirm('Do you really want to delete ' + entity + '?')) {
        var rowCount = $('#contactTable .vomanager').length;

        if (rowCount > 1 || profile != "VO MANAGER") {
            $.ajax({
                type: 'get',
                dataType: 'html',
                data: {
                    id: entityId,
                    serial: serial
                },
                url: url,
                success: function (data, textStatus) {
                    $('#' + panelID).html(data);
                }

            });
        } else {
            alert("You can not delete the last VO MANAGER");
        }
    } else {
        return false;
    }

}

//--------------------------------------------------------------//
// send mail to ask for perun for vo
//--------------------------------------------------------------//
function askForPerun(url, serial, panelID) {


    if (serial != null && serial != "" && $.isNumeric(serial)) {

        $.ajax({
            type: 'post',
            dataType: 'html',
            data: {
                serial: serial
            },
            url: url,
            beforeSend:function() {
                $("#askForPerun").attr("disabled", "disabled").html("<span class='loader-little'></span>");
            },
            success: function (data) {
                $('#' + panelID).html(data);
            },
            complete: function() {
                $("#askForPerun").removeAttr("disabled").html("Ask to use it");

            }

        });

    } else {
        $("#askForPerun").after("<span style='color:red'>No VO serial !</span>");
    }
}

/************* CVMFS ******************************/

function createCvmfs() {

    $("#modalCvmfsCreate").modal();
    var t = $('#tabcvmfs').DataTable();

    getCvmfs();

    var endpoint = $("#cvmfsCreate").val().replace(/ +?/g, '');
    if(endpoint !== "http://:"){

        t.row.add([
        endpoint,
        '<div class="d-flex justify-content-center dropdown btn-group text-light" role="group"><a id="dLabel" type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">Action <span class="caret"></span> </a><ul class="dropdown-menu" aria-labelledby="dLabel"><li><a href="#" onclick="editEndpoint(\'' + endpoint + '\', $(this).parents(\'tr\'))">Edit</a></li><li><a href="#" onclick="removeEndpoint($(this).parents(\'tr\'))">Delete</a></li></ul></div>'
    ]).draw();

        }


    $("#createCvmfsFormSubmit").on('click', function (event) {

        var mode;

        if ($("#vo_VoRessources_cvmfs").val().length !== 0) {

            $("#processCvmfs").html("Endpoint were successfully " + (mode == "create" ? "added" : "edited") + " to the tab");
        } else {
            $("#processCvmfs").html("Error on Endpoint " + (mode == "create" ? "addition" : "edition") + "...");
        }

        $("#processCvmfs").show().fadeTo(2000, 500).slideUp(500, function () {
            $("#processCvmfs > button").nextAll().remove();
            $("#processCvmfs").slideUp(500);
        });


        event.preventDefault();

    });


}

function getCvmfs() {

        var httpCreate = document.getElementById('httpCreate').value;
        var portCreate = document.getElementById('portCreate').value;



        var cvmfsCreate = 'http://' + httpCreate + ':' + portCreate;

        document.getElementById('cvmfsCreate').value = cvmfsCreate;

        var httpEdit = document.getElementById('httpEdit').value;
        var portEdit = document.getElementById('portEdit').value;

        var cvmfsEdit = 'http://' + httpEdit + ':' + portEdit;

        document.getElementById('cvmfsEdit').value = cvmfsEdit;

}



//--------------------------------------------------------------//
 // edit endpoint in cvmfs tab
 // @param endpoint
 // @param tr
//--------------------------------------------------------------//
function editEndpoint(endpoint) {

    console.log(endpoint);
    var url = endpoint.substring(endpoint.lastIndexOf("/") + 1,
        endpoint.lastIndexOf(":"));
    var port = endpoint.split(':')[2];
    $("#httpEdit").val(url);
    $("#portEdit").val(port);

    $('http').val(url);
    $('port').val(port);

    $("#modalCvmfsEdit").modal('show');

    $("#EditCvmfsFormSubmit").click( function () {

        var count = $('#tabcvmfs').find('tr').length;
        var table = document.querySelector('table');

        for (i = 0; i < count; i++) {

            var row = table.rows[i];
            var cell = row.cells[0].innerHTML;

            if(cell === endpoint){
                table.deleteRow(i);
                $("#modalCvmfsEdit").modal("hide");
                break;
            }
        }
        });
}

function submitEndpoint(){
    getCvmfs();
    var endpoint = $("#cvmfsEdit").val().replace(/ +?/g, '');
    var t = $('#tabcvmfs').DataTable();

    t.row.add([
        endpoint,
        '<div class="d-flex justify-content-center dropdown btn-group text-light" role="group"><a id="dLabel" type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">Action <span class="caret"></span> </a><ul class="dropdown-menu" aria-labelledby="dLabel"><li><a href="#" onclick="editEndpoint(\'' + endpoint + '\', $(this).parents(\'tr\'))">Edit</a></li><li><a href="#" onclick="removeEndpoint($(this).parents(\'tr\'))">Delete</a></li></ul></div>'
    ]).draw();

}





//--------------------------------------------------------------//
// delete an endpoint from cvmfs tab
// @param endpoint
//--------------------------------------------------------------//
function removeEndpoint(endpoint) {
    var t = $('#tabcvmfs').DataTable();
    t.row(endpoint).remove().draw();
    $('html,body').animate({scrollTop: ($("#cvmfsSection").offset().top - 200)}, 'slow');
}

// /**
//  * register if it is "create" or "edit" mode
//  */
// function addEditEndpoint(type, tr) {
//     mode = type;
//     if (tr != null) {
//         var t = $('#tabcvmfs').DataTable();
//         row = tr;
//     }
// }

//--------------------------------------------------------------//
// construct a modal containing VOMS detail for  VO collapse
//--------------------------------------------------------------//
//function getVomsDetail(clicked, voVomsDetailLink, host, voserial) {
//    var tr = $(clicked).closest('tr');
//    //if the detail is already visible, make it disappear
//    if ($(clicked).find(".glyphicon-minus").length) {
//        $("#vomsDetail" + voserial).remove();
//        $(clicked).find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
//    } else {
//        if ($("#vomsDetail" + voserial).length && $("#vomsDetail" + voserial).is(":visible")) {
//            $("#vomsDetail" + voserial).remove();
//            $("#vomsInformationTable").find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
//        }
//        $.ajax({
//            type: 'POST',
//            data: {
//                serial: voserial,
//                host: host
//            },
//            url: voVomsDetailLink,
//            async: false,
//            beforeSend: function () {
//                //show loader while ajax call is not finished
//                $(clicked).html("<span class='loader'></span>");
//            },
//            complete: function (data) {
//                //remove button loader
//                $(clicked).html("<span class='btn btn-xs btn-default'><span class='glyphicon glyphicon-minus'></span></span>")
//
//                //display detail in a tr after the clicked line
//                $(data.responseText).insertAfter(tr);
//
//                //animation to show the row
//                $("#vomsDetail" + voserial).slideDown();
//
//            },
//            //if fail, show error message
//            error: function (xhr) {
//                $("#errorDivVoms > div").html(xhr.status + " : " + xhr.responseText);
//                $("#errorDivVoms > div").show();
//
//            }
//
//        });
//    }
//
//}

function buildMemberListUrl(form, voform) {

    var template = "https://%%HOST%%:%%PORT%%/voms/%%VONAME%%/services/VOMSAdmin?method=listMembers";

    var host = $("#"+ form +"_hostname").val();
    var port = $("#"+ form +"_https_port").val();
    var voname = $("#"+ voform +"_name").val();
    template = template.replace("%%VONAME%%", voname);
    template = template.replace("%%HOST%%", host);
    template = template.replace("%%PORT%%", port);

    $("#"+ form +"_members_list_url").val(template);
}

