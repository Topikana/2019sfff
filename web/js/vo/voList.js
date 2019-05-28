/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                          GLOBAL VARIABLES
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

var waitingVOListTable;
var myVoListTable;
var otherVoListTable;
var leavingVoListTable;
var badVoListTable;
var securityListTable;
var rbCertListTable;

var voserial;


var tabErrorDiv = {
    'waitingVOListTable': 'errorDivWaiting',
    'myVoListTable': 'errorDivMyVO',
    'otherVoListTable': 'errorDivOther',
    'leavingVoListTable': 'errorDivLeaving',
    'badVoListTable': 'errorDivBadVo',
    'securityListTable': 'errorDivSecurity',
    'rbCertListTable': "errorDivRbCert"
};


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                          GLOBAL
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$(document).ready(function () {
//adjust the datatables to the container
  // $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();

    $('.panel-collapse').on('shown.bs.collapse', function (e) {
        var tableIdToUpdate = $(e.currentTarget).find('.card-body').find('.table')[0].id;
        $('#' + tableIdToUpdate).DataTable().columns.adjust().responsive.recalc();
    });
});

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                          DATATABLES METHODS
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//--------------------------------------------------------------//
//Formatting function for row details - modify as you need
//--------------------------------------------------------------//

    function format(d) {
        // `d` is the original data object for the row
        return '<div class="card">' +
            '<h3>' + d.name + '</h3>' +
            '<h3>General Information</h3>' +
            '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
            '<td>VO name: </td>' +
            '<td>' + d.name + '</td>' +
            '</tr>' +
            '</table>' +
            '</div>';
    }

//--------------------------------------------------------------//
// Setup - add a text input to each header cell of datatable
//--------------------------------------------------------------//
    function setUpFilters() {
        console.log("setUpFilters");
        $('.filters .search').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        });
    }

//--------------------------------------------------------------//
// Setup - initialisation of AUP
//--------------------------------------------------------------//
function setUpAup() {

    $("#vo_VoHeader_aupFile_aupFile").change(function () {
        var new_file = $('#vo_VoHeader_aupFile_aupFile').val();
        if (new_file) {
            var vo_name = $('#vo_VoHeader_name').val();
            var date = new Date();
            var date_chunck = date.getUTCFullYear() + pad2(date.getMonth() + 1) + pad2(date.getDate());
            var extension_chunck = getExtensionFile(new_file);
            $('#vo_VoHeader_aupFile_name').val(vo_name + "-AcceptableUsePolicy-" + date_chunck + "-" + date.getTime() + "." + extension_chunck);
        }
    });

    if ($("#fileError").find("ul").length == 0) {
        $("#fileError").hide();
    }


    $("#aup_text_block").on("click", function (event) {
        $(this).addClass('d-block');

    });
}

//--------------------------------------------------------------//
// build VO Tables with DataTables plugin (VoList page)
//--------------------------------------------------------------//
    function buildVOTables() {


        //build waiting vo table
        if ($("#waitingVOListTable").length != 0) {
            waitingVOListTable = $("#waitingVOListTable").DataTable({

                "columns": [
                    {
                        "className": 'details-control',
                        "orderable": false
                    },
                    {"data": "Name"},
                    {"data": "Status"},
                    {"data": "Creation"},
                    {"data": "Scope"},
                    {"data": "Discipline", "orderable": false},
                    {"data": "Need VOMS Support (0))"},
                    {"data": "VOMS (1)"},
                    {"data": "VO (2)"},
                    {"data": "VO SU (3)"},
                    {"data": "Action", "orderable": false, "searchable": false},
                    {"data": "serial", "visible": false, "orderable": false, "searchable": false}

                ],
                "order": [[3, 'desc']],
                dom:
                // "<'row d-flex ml-2'<'col-md-2 .offset-1'fr mr-3>>" +
                // "<'row d-flex justify-content-center'<'col-sm-10't>>" +
                // "<'row d-flex justify-content-center'<'col-md-2 d-flex justify-content-center'p>>",
                    '<"top"B><"pull-right"l><"bottom"rtip><"clear">',
                "searchable": false,
                buttons: [
                    {
                        extend: 'colvis',
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],

                    },
                    {
                        className: 'exportButton',
                        extend: 'collection',
                        text: 'Export',
                        buttons: [
                            'copy',
                            'excel',
                            'csv',
                            'pdf',
                            'print'
                        ]
                    }
                ]
            }).columns.adjust().draw();
        }


        if ($("#leavingVoListTable").length != 0) {

            //build leaving VO Table
            leavingVoListTable = $("#leavingVoListTable").DataTable({
                "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                "columns": [
                    {
                        "className": 'details-control',
                        "orderable": false
                    },
                    {"data": "name",},
                    {"data": "status"},
                    {"data": "discipline", "orderable": false},
                    {"data": "scope"},
                    {"data": "middleware(s)", "orderable": false},
                    {"data": "Active Users"},
                    {"data": "Total Users"},
                    {"data": "Actions", "orderable": false, "searchable": false},
                    {"data": "serial", "orderable": false, "searchable": false},


                ],
                "order": [[1, 'asc']],
                dom: '<"top"B><"pull-right"l><"bottom"rtip><"clear">',
                "searchable": false,
                buttons: [
                    {
                        extend: 'colvis',
                        columns: [0, 1, 2, 3, 4, 5, 6],

                    }, {
                        className: 'exportButtonOther',
                        extend: 'collection',
                        text: 'Export',
                        buttons: [
                            'copy',
                            'excel',
                            'csv',
                            'pdf',
                            'print'
                        ]
                    }
                ]
            }).columns.adjust().draw();
        }

        if ($("#myVoListTable").length != 0) {

            //build my vo table
            myVoListTable = $("#myVoListTable").DataTable({

                "columns": [
                    {
                        "className": 'details-control',
                        "orderable": false
                    },
                    {"data": "VO"},
                    {"data": "Last update"},
                    {"data": "Last validation date"},
                    {"data": "Last email sending"},
                    {"data": "validation"},
                    {"data": "Action", "orderable": false, "searchable": false},
                    {"data": "serial", "orderable": false, "searchable": false},
                    {"data": "status", "orderable": false, "searchable": false}


                ],
                "order": [[1, 'asc']],
                dom: '<"top"B><"pull-right"l><"bottom"rtip><"clear">',
                // dom :  "<'row d-flex ml-2'<'col-md-2 .offset-1'fr mr-3>>" +
                // "<'row d-flex justify-content-center'<'col-sm-10't>>" +
                // "<'row d-flex justify-content-center'<'col-md-2 d-flex justify-content-center'p>>",
                "searchable": false,
                buttons: [
                    {
                        extend: 'colvis',
                        columns: [0, 1, 2, 3, 4, 5, 6],

                    },
                    {
                        className: 'exportButton',
                        extend: 'collection',
                        text: 'Export',
                        buttons: [
                            'copy',
                            'excel',
                            'csv',
                            'pdf',
                            'print',
                        ]
                    }
                ]

            }).columns.adjust().draw();
        }

        if ($("#otherVoListTable").length != 0) {

            //build Other VO Table
            otherVoListTable = $("#otherVoListTable").DataTable({
                // "autoWidth" : false,
                "columnDefs": [
                    {
                        "type": "num", "targets": [5, 6],


                    },
                    // { targets: 0, orderable: false },
                    // { targets: [1, 2, 3, ,4 ,5 ,6], visible: false }
                    {"width": 100, "targets": 0},
                ],


                "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                "columns": [
                    {
                        "className": 'details-control',
                        "orderable": false,
                    },
                    {"data": "name"},
                    {"data": "discipline", "orderable": false},
                    {"data": "scope"},
                    {"data": "middleware(s)", "orderable": false},
                    {"data": "Active Users"},
                    {"data": "Total Users"},
                    {"data": "Actions", "orderable": false, "searchable": false},
                    {"data": "serial", "orderable": false, "searchable": false},
                    {"data": "status", "orderable": false, "searchable": false},

                ],

                "order": [[1, 'asc']],

                dom: '<"top pb-2"B><"pb-2 float-right"fl><"bottom"rtip><"clear">',
                stateSave: true,

                "searchable": true,

                buttons: [
                    {
                        extend: 'colvis',
                        columnDefs: [ {className: 'text-info',targets:   1}],
                        // text: '<button type="button" value="Search" id="btnSearch" class="btn btn-primary btn-xs">Search</button>',
                        className: 'bg-primary',
                        columns: [0, 1, 2, 3, 4, 5, 6, 7],
                        


                    }, {

                        extend: 'collection',
                        text: 'Export',
                        className: 'exportButtonOther  bg-primary',
                        buttons: [
                            'copy',
                            'excel',
                            'csv',
                            'pdf',
                            'print'
                        ]
                    }
                ],
            }).columns.adjust().draw();




        }

    }




// $('#otherVoListTable').DataTable({
//     dom: 'C<"clear">lfrtip',
//     colVis: {
//         stateChange: function(iColumn, bVisible) {
//             // Set searchable option based on whether column is visible
//             this.s.dt.aoColumns[iColumn].bSearchable = bVisible;
//
//             var api = new $.fn.dataTable.Api(this.s.dt);
//             api.rows().invalidate().draw();
//         }
//     }
// });

//--------------------------------------------------------------//
// build VO Table with DataTables plugin (Invalid VO page)
//--------------------------------------------------------------//
    function buildInvalidVOTable() {
        badVoListTable = $("#badVoListTable").DataTable({
            "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            "pageLength": 50,
            "columns": [
                {"data": "Vo name"},
                {"data": "serial"},
                {"data": "Admins"},
                {"data": "AUP"},
                {"data": "Description"},
                {"data": "Homepage URL"},
                {"data": "User Support"},
                {"data": "Nb Voms Server"},
                {"data": "Voms Users"},
                {"data": "Score(%)"},
                {"data": "Details", 'orderable': false},
                {"data": "Last report"},
                {"data": "Submit report", 'orderable': false}


            ],
            "order": [[1, 'asc']],
            // dom:  "<'row d-flex ml-2'<'col-md-2 .offset-1'fr mr-3>>" +
            // "<'row d-flex justify-content-center'<'col-sm-10't>>" +
            // "<'row d-flex justify-content-center'<'col-md-2 d-flex justify-content-center'p>>",
            dom: '<"top"B><"pull-right"l><"bottom"rtip><"clear">',
            "searchable": false,
            buttons: [
                'colvis',
                {
                    className: 'exportButtonOther',
                    extend: 'collection',
                    text: 'Export',
                    buttons: [
                        'copy',
                        'excel',
                        'csv',
                        'pdf',
                        'print'
                    ]
                }
            ]
        });
    }

//--------------------------------------------------------------//
// build VO Table with DataTables plugin (Security page)
//--------------------------------------------------------------//
    function buildSecurityTable() {
        securityListTable = $("#securityListTable").DataTable({
            "pageLength": 50,
            "columns": [
                {"data": "Vo Name"},
                {"data": "Security Contacts"},
                {"data": "Contacts Managers"},
                {"data": "Action", 'orderable': false, 'searchable': false},
                {"data": "serial", 'orderable': false, 'searchable': false},


            ],
            "order": [[0, 'asc']],
            // dom:  "<'row d-flex ml-2'<'col-md-2 .offset-1'fr mr-3>>" +
            // "<'row d-flex justify-content-center'<'col-sm-10't>>" +
            // "<'row d-flex justify-content-center'<'col-md-2 d-flex justify-content-center'p>>",
            dom: '<"top"B>fl<"bottom"rtip><"clear">',
            "searchable": false,
            buttons: [
                {
                    extend: 'colvis',
                    columns: ':not(:last-child)'
                }, {
                    className: 'exportButtonOther',
                    extend: 'collection',
                    text: 'Export',
                    buttons: [
                        'copy',
                        'excel',
                        'csv',
                        'pdf',
                        'print'
                    ]
                }
            ]
        });
    }

//--------------------------------------------------------------//
// Apply the search by header
//--------------------------------------------------------------//
    function applySearch() {

        //My VOServiceCertificate
        if ($("#myVoListTable").length != 0) {

            myVoListTable.columns().eq(0).each(function () {
                var that = this;
                $('#myVoListTable .filters th  input', $('.myVoListTable thead')[1]).on('keyup change', function () {
                    that.search(this.value).draw();

                });
            });
        }
        //leaving VO
        if ($("#leavingVoListTable").length != 0) {

            if (leavingVoListTable.columns().eq(0) != null) {
                leavingVoListTable.columns().eq(0).each(function () {
                    var that = this;
                    $('#leavingVoListTable .filters th  input', $('.leavingVoListTable thead')[1]).on('keyup change', function () {
                        that.search(this.value).draw();

                    });
                });
            }
        }
        //Other VO
        if ($("#otherVoListTable").length != 0) {

            otherVoListTable.columns().eq(0).each(function () {
                var that = this;
                $('#otherVoListTable .filters th  input', $('.otherVoListTable thead')[1]).on('keyup change', function () {
                    that.search(this.value).draw();

                });
            });
        }

    }


//--------------------------------------------------------------//
// Add XML exports button to Vo Other List Tab
//--------------------------------------------------------------//
    function constructExportButtons(voFullLink, voOtherLink) {
        $(".exportButtonOther").click(function () {
            if ($(".buttons-xml-list").length == 0) {
                $(".dt-button-collection").append("<a class='dt-button dropdown-item buttons-xml-list buttons-html5' tabindex='0' aria-controls='voListOtherTable' href='" + voOtherLink + "' title='download the list of the VO in a xml format'>Vo List XML</a>" +
                    "<a class='dt-button dropdown-item  buttons-xml-full buttons-html5' tabindex='0' aria-controls='voListOtherTable' href='" + voFullLink + "' title='download the VO ID cards information in a xml format'>Vo Full XML</a>");
            }
        });
    }


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                          DETAILS METHODS
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//--------------------------------------------------------------//
// construct a modal containing VO detail for VO collapse
//--------------------------------------------------------------//
    function getVODetails(clicked, voDetailLink, typeTable) {

        //get the entire row
        var tr = $(clicked).closest('tr');

        var row;

        //get row data depending on table
        if (typeTable.indexOf("other") != -1) {
            row = otherVoListTable.row(tr);
        } else if (typeTable.indexOf("myVo") != -1) {
            row = myVoListTable.row(tr);
        } else if (typeTable.indexOf("leaving") != -1) {
            row = leavingVoListTable.row(tr);
        } else if (typeTable.indexOf("bad") != -1) {
            row = badVoListTable.row(tr);
        } else if (typeTable.indexOf("security") != -1) {
            row = securityListTable.row(tr);
        } else {
            row = waitingVOListTable.row(tr);
        }


        //get the serial
        voserial = row.data().serial;

        $.ajax(
            {
                type: 'POST',
                data: {
                    serial: voserial
                },
                url: voDetailLink,
                async: false,
                beforeSend: function () {
                    //waiting wheel
                    $(clicked).html("<span class='loader-little'></span>");

                },
                complete: function (data) {
                    //hide wheel and show glyphicon
                    $(clicked).html("<span class='btn btn-sm btn-light border border-secondary'><span class='fa fa-search-plus'></span></span>");

                    //append modal with content result
                    $("body").append(data.responseText);
                    $("#modalVo" + row.data().serial).modal();


                },
                error: function (xhr) {
                    //hide wheel and show error div
                    $(clicked).html("<span class='btn btn-sm btn-light border border-secondary'><span class='fa fa-search-plus'></span></span>");
                    $("#" + tabErrorDiv[typeTable]).html(xhr.status + " : " + xhr.responseText);
                    $("#" + tabErrorDiv[typeTable]).show();

                }

            });

    }

//--------------------------------------------------------------//
// construct a modal containing VOMS detail for  VO collapse
//--------------------------------------------------------------//
    function getVomsDetail(clicked, voVomsDetailLink, host, colspan) {
        var tr = $(clicked).closest('tr');
        var hostFormated = host.replace(/[^a-z0-9\s]/gi, '');

        if (voserial == null) {
            voserial = $("#vo_VoHeader_serial").val();
        }

        //if the detail is already visible, make it disappear
        if ($("#vomsDetail" + voserial + hostFormated).length && $("#vomsDetail" + voserial + hostFormated).is(":visible")) {
            $("#vomsDetail" + voserial + hostFormated).slideUp();
            $(clicked).html("<span class='btn btn-sm btn-light'><span class='fa fa-plus'></span></span>")

            //else call to method
        } else {
            $("#vomsDetailError").remove();
            $.ajax({
                type: 'POST',
                data: {
                    serial: voserial,
                    host: host,
                    colspan: colspan
                },
                url: voVomsDetailLink,
                async: false,
                beforeSend: function () {
                    //show loader while ajax call is not finished
                    $(clicked).html("<span class='loader-little'></span>");
                },
                complete: function (data) {
                    //remove button loader
                    $(clicked).html("<span class='btn btn-sm btn-light'><span class='fa fa-minus'></span></span>")

                    //display detail in a tr after the clicked line
                    $(data.responseText).insertAfter(tr);

                    //animation to show the row
                    if ($("#vomsDetail" + voserial + hostFormated).length != 0) {
                        $("#vomsDetail" + voserial + hostFormated).slideDown();
                    } else {
                        $("#vomsDetailError").slideDown();
                    }

                },
                //if fail, show error message
                error: function (xhr) {
                    $("#errorDivVoms > div").html(xhr.status + " : " + xhr.responseText);
                    $("#errorDivVoms > div").show();

                }

            });
        }
    }

//--------------------------------------------------------------//
// set VO last validation date
// (TODO send validation email)
// show validation message
//--------------------------------------------------------------//
    function validateVO(voId, validateLink) {
        $.ajax({
            type: 'POST',
            data: {
                serial: voId
            },
            dataType: "json",
            url: validateLink,
            async: false,
            beforeSend: function () {
                //replace text by loader while ajax call is not finished
                $("#modalVoConfirm .modal-body").html("<span class='loader-middle'></span>");
            },
            success: function (data) {
                //if call was successfull display ok message and reload page
                if (data.isSuccess == 1) {

                    $("#modalVoConfirm .modal-body").html(data.res);
                    setTimeout(function () {// wait for 10 secs(2)
                        location.reload(); // then reload the page.(3)
                    }, 10000);

                    //if call failed show error message and hide modal
                } else {
                    $("#modalVoConfirm").modal("hide");

                    $("#errorDivMyVO > div").html(data.res);
                    $("#errorDivMyVO > div").show();

                }

            },
            //if call failed show error message and hide modal
            error: function (xhr) {
                $("#modalVoConfirm").modal("hide");

                $("#errorDivMyVO > div").html(xhr.status + " : " + xhr.responseText);
                $("#errorDivMyVO > div").show();

            }

        });
    }


//--------------------------------------------------------------//
// update scope of a waiting VO
//--------------------------------------------------------------//
    function updateScope(changed, udpateScopeUrl, voserial) {

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                serial: voserial,
                scopeId: $(changed).val()
            },
            url: udpateScopeUrl,
            async: false,
            beforeSend: function () {
                //show loader while ajax call is not finished
                $(changed).parents("td").append("&nbsp;<span class='pull-right loader-little'></span>");
            },
            success: function (data) {
                //remove button loader
                $(changed).parents("td").find(".loader-little").remove();

                //add notification under select input
                $(changed).parents("td").children("span").remove();
                $(changed).parents("td").append("<span style='margin-top:2%' class='badge badge-success pull-left'>" + data.status + "</span>");
            },
            //if fail, show error message
            error: function (data) {
                //remove button loader
                $(changed).parents("td").find(".loader-little").remove();
                //add notification under select input
                $(changed).parents("td").children("span").remove();
                $(changed).parents("td").append("<span style='margin-top:2%' class='badge badge-danger pull-left'>" + data.status + "</span>");

            }

        });
    }


//--------------------------------------------------------------//
// show page loader on click on action button in tab
//--------------------------------------------------------------//
    function showLoaderOnVoAction() {
        $(".voAction").on("click", function () {
            $(".loader-wrapper").show();
            $(".loader-wrapper .loader").show();
        });
    }


//--------------------------------------------------------------//
// show vo validity synoptic tab on click on collapse tab
//--------------------------------------------------------------//
    function loadSynoptic(url, clicked) {


        $('#collapseVoValidity').on('shown.bs.collapse', function () {

            if ($("#badVoListTable").length == 0) {
                $.ajax({
                    type: "POST",
                    url: url,
                    async: false,
                    beforeSend: function () {
                        $("#voValidityBody").append("<span class='loader-middle'></span>")
                    },
                    error: function (data) {
                        $("#errorDivValidity").html('<div class="alert alert-danger"> ' + data.responseText + '</div>');
                    },
                    success: function (data) {
                        $("#voValidityBody").children(".loader-middle").remove();
                        $("#errorDivValidity").empty();

                    },
                    complete: function (data) {
                        $("#voValidityBody").append(data.responseText);

                        buildInvalidVOTable();
                    }
                });
            }
        });
    }

//--------------------------------------------------------------//
// user tracking : find all dn related to user entry in form
//--------------------------------------------------------------//
    function searchDN(url) {
//    urlpath= "http://" + location.host + location.pathname + "/../../vo/AjaxDnTracking";


        if (!$("#user_tracking_DN")[0].checkValidity()) {
            $("#errorDN").remove();
            $("#user_tracking_DN").parents(".form-group").append("<p id='errorDN' style='color:#cd0a0a; font-weight: bold'>DN field is empty</p>")

        } else {
            $("#errorDN").remove();
            $("#modalDN").modal("show");
            var dataLink = $("#user_tracking_DN").val();
            var value = "";
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: url,
                data: {"q": dataLink},
                beforeSend: function () {
                    $("#modalDN .modal-body .loader-middle").show();
                },
                error: function (data) {
                    $("#modalDN .modal-body").html('<div class="alert alert-danger"> ' + data.responseText + '</div>');
                },
                success: function (data) {

                    $.each(data, function (key, val) {
                        value += '<label class="radio">';
                        value += "<input type='radio' name='optionsRadios' id='" + val + "' value='" + val + "' />";
                        value += val;
                        value += '</label>'
                    });
                    $("#modalDN .modal-body").fadeIn(1000).html(value);

                }
            });
        }
    }

//--------------------------------------------------------------//
// user tracking : choose a DN in modal and add it to form
//--------------------------------------------------------------//
    function selectDN(url) {
        checkVo($('input[type=radio]:checked').val());

        $("#user_tracking_email").removeAttr("disabled");
        $("#user_tracking_subject").removeAttr("disabled");
        $("#user_tracking_body").removeAttr("disabled");
        $("#user_tracking_send").removeAttr("disabled");

        $("#email_securityOfficier").removeClass('alert-info').removeClass('alert-danger').html("");
        var DN = $("#user_tracking_DN").val();
        var VO = $("#user_tracking_vo").val();

        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: {
                "DN": DN,
                "VO": VO
            },
            error: function (data) {
                $("#email_securityOfficier").show().removeClass('alert-info').addClass('alert-danger').html(data.responseText);
            },
            success: function (data) {
                $("#email_securityOfficier").show().removeClass('alert-danger').addClass('alert-info').html(data);
            }
        })

    }

//--------------------------------------------------------------//
// user tracking : check the vo
//--------------------------------------------------------------//
    function checkVo(val) {
        var index = val.indexOf('VO');
        var vo = val.substring(index + 4);
        $("#user_tracking_vo").val(vo);

        var index = val.indexOf('CN:');
        var tdn_cleaned = val.substring(0, index);
        $("#user_tracking_DN").val(tdn_cleaned);

    }

//--------------------------------------------------------------//
// show or hide information about use sub proxies
//--------------------------------------------------------------//
    function showHideUseSubProxiesMessage(clicked) {

        if ($(clicked).is(":checked")) {
            $("#subProxiesInfo").show();
        } else {
            $("#subProxiesInfo").hide();

        }
    }

//--------------------------------------------------------------//
// reset form in RbCertModal
//--------------------------------------------------------------//
    function clearRbCertModal() {
        var str = $("#modalVoAddCert .modal-title span:nth-child(2)").text();
        if (str.indexOf("Edit") >= 0) {
            $("#modalVoAddCert .modal-title span:nth-child(2)").text("Add Robot Certificate");
            $("#modalVoAddCert #formContactEGI")[0].reset();
            $("#subProxiesInfo").hide();
        }
    }



