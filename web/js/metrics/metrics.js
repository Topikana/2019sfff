/**
 * Created by lsouai on 17/03/16.
 */

var rbCertListTable;

//--------------------------------------------------------------//
// build VO Table with DataTables plugin (metrics reports list)
//--------------------------------------------------------------//
function buildUsersMetricsTable() {

    var table = $('#usersMetricsTable').DataTable( {
        "pageLength": 50,
        lengthChange: false,
        "searchable": false,
        buttons: [ 'copy', 'excel', 'pdf' ]
    } );

    table.buttons().container()
        .appendTo( '#usersMetricsTable_wrapper .col-md-6:eq(0)' );

}

//--------------------------------------------------------------//
// build VO Table with DataTables plugin (metrics reports list)
//--------------------------------------------------------------//
function buildInternationalUsersMetricsTable() {
    var table=$("#internationalUsersMetricsTable").DataTable({
        "pageLength": 50,
        "searchable": false,
        "columns": [
            {"data": "Period", 'orderable': false, 'searchable': false},
            {"data": "New VO -Total"},
            {"data": "New International VO"},
            {"data": " 	New National VO"},
            {"data": "Details", 'orderable': false, 'searchable': false},
        ],
        buttons: [ 'copy', 'excel', 'pdf' ]
    } );

    table.buttons().container().appendTo( '#internationalUsersMetricsTable_wrapper .col-md-6:eq(0)' );

}

//--------------------------------------------------------------//
// build VO Table with DataTables plugin (metrics reports list)
//--------------------------------------------------------------//
function buildVoActivitiesMetricsTable() {
    var table = $("#voActivitiesMetricsTable").DataTable({
        "pageLength": 50,
        "searchable": false,
        "columns": [
            {"data": "VO"},
            {"data": "Status"},
            {"data": "Scope"},
            {"data": "CPU Consumption"},
            {"data": "AppDBChanges"},
            {"data": "Diff"},
            {"data": "Month 1"},
            {"data": "Month 2"},

            //{"data": "Vo Users", 'orderable': false, 'searchable': false},
        ],
        buttons: [ 'copy', 'excel', 'pdf' ]
    });

    table.buttons().container().appendTo( '#voActivitiesMetricsTable_wrapper .col-md-6:eq(0)' );
}

//--------------------------------------------------------------//
// build VO Table with DataTables plugin (robot certificate page)
//--------------------------------------------------------------//
function buildRbCertificateTable() {
    var rbCertListTable = $("#rbCertListTable").DataTable({
        "pageLength": 25,
        "columns": [
            {"data": "Vo"},
            {"data": "Robot DN"},
            {"data": "Contact Email"},
            {"data": "use per-user sub-proxies"},
            {"data": "Service Name"},
            {"data": "Service Url"},
            {"data": "Validation date"},
            {"data": "Action", 'orderable': false, 'searchable': false},
            {"data": "Id", 'orderable': false, 'searchable': false},

        ],
        "order": [[6, 'asc']],
        "searchable": false,
        buttons: [ 'copy', 'excel', 'pdf' , 'colvis' ]

    });
    rbCertListTable.buttons().container().appendTo( '#rbCertListTable_wrapper .col-md-6:eq(0)' );
}

//-------------------------------------------------------------//
// BUILD USER METRICS LIST CHART
//-------------------------------------------------------------//
function buildUserMetricsChart(title) {
    var chartDiv = $('#UsersMetricschart_div')[0];


    var csvData = $('div.dataCSV').text();


    var lines = csvData.split("\n");


    var data = new google.visualization.DataTable();
    data.addColumn('date', "Month");
    var dataRows = [];

    var arrayLabel = [];

    $.each(lines, function (lineNo, line) {
        var arrayNb = [];
        $.each(line.split(","), function (key, val) {
            if ($.trim(val)) {
                if (val.match(/^\d{4}-((0\d)|(1[012]))$/)) {
                    arrayNb.push(new Date($.trim(val)));
                } else {
                    if ($.isNumeric(val)) {
                        arrayNb.push(parseFloat($.trim(val)));
                    } else {
                        if ($.inArray($.trim(val), arrayLabel) === -1) {
                            arrayLabel.push($.trim(val));
                        }

                    }
                }

            }
        });
        dataRows.push(arrayNb);
    });

    $.each(arrayLabel, function (key, val) {
        data.addColumn("number", val);
    });


    data.addRows(dataRows);

    var columns = [];
    for (var i = 0; i < data.getNumberOfColumns(); i++) {
        columns.push(i);
    }

    //-------------------------------------------------------------------------------//
    //construction for line chart

    var colors = ['#00CC33', '#3333FF', '#FF0000', '#FF9933', '#660099', "#FF00FF",
        '#660099', '#FFFF33', '#FF99FF', '#00FF33', '#0099FF', '#606060', '#003366', '#999933',
        '#996600', '#CCCCFF', '#333300', '#CC99CC', '#FF0099', '#999999', '#CC3333', '#00FFFF'];
    var previousColors = [];

    //material display
    var options = {
        title: title,
        height: 500,
        width: 1700,
        hAxis: {
            title: 'Month',
        },
        vAxis: {
            title: 'Number',

        },
        chartArea: {left: '200', width: '60%'},
        legend: {textStyle: {fontName: 'Arial', fontSize: 12, bold: false}},
        colors: colors

    };


    //draw graph material
    function drawMaterialChart() {
        var materialChart = new google.visualization.LineChart(chartDiv);

        // Wait for the chart to finish drawing before calling the getImageURI() method.
        google.visualization.events.addListener(materialChart, 'ready', function () {
            document.getElementById('chart_div_png').outerHTML = '<a class="btn btn-default" target="_blank" href="' + materialChart.getImageURI() + '">PNG printable version</a>';
        });


        materialChart.draw(data, options);

        //toggle line visibilty
        google.visualization.events.addListener(materialChart, 'select', function () {
            var sel = materialChart.getSelection();

            // if selection length is 0, we deselected an element
            if (sel.length > 0) {
                // if row is undefined, we clicked on the legend
                if (sel[0].row === null) {
                    var col = sel[0].column;
                    if (columns[col] == col) {
                        // hide the data series
                        columns[col] = {
                            label: data.getColumnLabel(col),
                            type: data.getColumnType(col),
                            calc: function () {
                                return null;
                            }
                        };

                        // grey out the legend entry
                        previousColors[col - 1] = colors[col - 1];
                        colors[col - 1] = '#CCCCCC';
                    }
                    else {
                        // show the data series
                        columns[col] = col;
                        colors[col - 1] = previousColors[col - 1];

                    }
                    var view = new google.visualization.DataView(data);
                    view.setColumns(columns);
                    materialChart.draw(view, options);
                }
            }
        });
    }

    drawMaterialChart();


    //end of line chart
    //--------------------------------------------------------------------------------//

}


//-------------------------------------------------------------//
// BUILD USER PER DISCIPLINE METRICS LIST COLUMN & AREA CHART
//-------------------------------------------------------------//
function buildUserMetricsAreaBarColumn(tab, option) {

    var tabArea = $.parseJSON(tab);


    var options = {
        height: 200,
        width: 700,
        explorer: {},
        legend: {position: 'top'},
    };

    var chartDiv;

    $.each(tabArea, function (name, numbers) {
        var data = new google.visualization.DataTable();
        if (option == "area") {
            $("#UsersMetricschart_Area").append("<div id='areaChart_" + name.replace(/ /g, '') + "'></div><br><br>");
            chartDiv = document.getElementById("areaChart_" + name.replace(/ /g, ''));

        } else {
            $("#UsersMetricschart_Bar").append("<div id='barChart_" + name.replace(/ /g, '') + "'></div><br><br>");
            chartDiv = document.getElementById("barChart_" + name.replace(/ /g, ''));

        }
        data.addColumn("string", "Month");
        data.addColumn("number", name);
        if (option == "bar") {
            data.addColumn({type: 'string', role: 'style'});
        }

        $.each(numbers, function (date, number) {
            var arrayNb = [];

            if ($.isNumeric(number)) {

                arrayNb.push(date);
                arrayNb.push(parseFloat($.trim(number)));

                if (option == "bar") {
                    if (parseFloat($.trim(number)) < 0) {
                        arrayNb.push('color: red');
                    } else {
                        arrayNb.push('color: blue');

                    }
                }
            }

            if (arrayNb.length != 0) {
                data.addRows([arrayNb]);
            }

        });


        if (option == "area") {
            var areaChart = new google.visualization.AreaChart(chartDiv);

            // Wait for the chart to finish drawing before calling the getImageURI() method.
            areaChart.draw(data, options);
        } else {

            var columnChart = new google.visualization.ColumnChart(chartDiv);

            // Wait for the chart to finish drawing before calling the getImageURI() method.
            columnChart.draw(data, options);
        }

    });
}

//-------------------------------------------------------------//
// BUILD USER PER VO HISTORY METRICS CHART
//-------------------------------------------------------------//
function buildUserPerMetricsHistoryChart(csvData, name) {

    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawAreaChart);

    //draw graph material
    function drawAreaChart() {

        var chartDiv = document.getElementById('chart_div');

        var lines = csvData.split("\n");


        var data = new google.visualization.DataTable();
        data.addColumn('date', "Month");
        data.addColumn('number', name);
        var dataRows = [];


        var test;
        var arrayMinMax = [];


        $.each(lines, function (lineNo, line) {
            if (line) {
                var arrayNb = [];
                $.each(line.split(","), function (key, val) {
                    if ($.trim(val)) {
                        if (val.indexOf("-") >= 0) {
                            arrayNb.push(new Date($.trim(val)));
                        } else {
                            if ($.isNumeric(val)) {
                                arrayNb.push(parseFloat($.trim(val)));
                                arrayMinMax.push(parseFloat($.trim(val)));

                            }
                        }

                    }
                });
                dataRows.push(arrayNb);
            }
        });

        data.addRows(dataRows);


        //-------------------------------------------------------------------------------//
        //construction for area chart

        //material display
        var options = {
            title: 'Users Number for ' + name,
            subtitle: 'Use scroll button in the plot area to zoom in',
            height: 500,
            width: 1500,
            explorer: {},
            chartArea: {left: '200', width: '80%'},
            vAxis: {
                title: 'User Number',
                minValue: (Math.min.apply(null, arrayMinMax) - 1),
                maxValue: (Math.max.apply(null, arrayMinMax) + 3)
            },
            legend: {position: "bottom", textStyle: {fontName: 'Arial', fontSize: 12, bold: false}},

        };

        var areaChart = new google.visualization.AreaChart(chartDiv);

        // Wait for the chart to finish drawing before calling the getImageURI() method.
        google.visualization.events.addListener(areaChart, 'ready', function () {
            document.getElementById('chart_div_png').outerHTML = '<a class="btn btn-default" target="_blank" href="' + areaChart.getImageURI() + '">PNG printable version</a>';
        });


        areaChart.draw(data, options);
    }

    drawAreaChart();


    //end of area chart
    //--------------------------------------------------------------------------------//
}

//-------------------------------------------------------------//
// SHOW COLLAPSE WITH TAB TO DETAIL NB OF VO BETWEEN 2 DATES
//-------------------------------------------------------------//
function getVOCreationDetails(clicked, link, dateStart, dateEnd) {
    $.ajax({
        type: "POST",
        url: link,
        data: {"date_start": dateStart, "date_end": dateEnd},
        beforeSend: function () {
            $(clicked).html("<span class='loader'></span>");

            $('#voCreatedDetails').remove();

        },
        complete: function (data) {
            //hide wheel and show glyphicon
            $(clicked).html("<span class='fas fa-link'></span>");

            //append modal with content result
            $('#homeSection').append(data.responseText);
            $('#voCreatedDetails').slideDown();



            var table = $('#createdVoDetailsTable' + dateStart + dateEnd).DataTable({
                "pageLength": 25,
                lengthChange: false,
                "searchable": false,
                buttons: [ 'copy', 'excel', 'pdf' ]
            } );

            table.buttons().container()
                .appendTo( '#usersMetricsTable_wrapper .col-md-6:eq(0)' );

        },
        error: function (data) {
            $("#flashes").append("<div class='alert alert-danger' role='alert'>" + data.status + " " + data.responseText + "</div>");
            $('#voCreatedDetails').remove();

        }

    });

}

//-------------------------------------------------------------//
// BUILD USER PER VO HISTORY METRICS CHART
//-------------------------------------------------------------//
function buildDisciplinesMetricsPieChart(tabNbVo, tabNbUsers, title) {
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {

        var container = document.getElementById('pieChartNbVo');

        //get tab nb VO
        var nbVoData = $.parseJSON(tabNbVo);

        var arrayNbVo = [];

        $.each(nbVoData, function (key, value) {
            arrayNbVo.push([key, value]);
        });

        //draw nb vo chart
        var data = google.visualization.arrayToDataTable(arrayNbVo);

        var options = {
            title: 'Vo Distribution : ' + title,
            is3D: true
        };

        var chart = new google.visualization.PieChart(container);

        google.visualization.events.addListener(chart, 'ready', function () {
            document.getElementById('chartPngNbVO').outerHTML = '<a class="btn btn-default" target="_blank" href="' + chart.getImageURI() + '">VO Distribution PNG printable version</a>';
        });

        chart.draw(data, options);

        //////////////////////////////////////////////////////////////////////////////////
        var container2 = document.getElementById('pieChartNbUsers');

        //get tab nb users
        var nbUsersData = $.parseJSON(tabNbUsers);

       var arrayNbUsers = [];

        $.each(nbUsersData, function (key, value) {
            arrayNbUsers.push([key, value]);
        });

        //draw nb users chart
        data = google.visualization.arrayToDataTable(arrayNbUsers);

       options = {
            title: 'User Distribution : ' + title,
            is3D: true
        };

        chart = new google.visualization.PieChart(container2);

        google.visualization.events.addListener(chart, 'ready', function () {
            document.getElementById('chartPngNbUsers').outerHTML = '<a class="btn btn-default" target="_blank" href="' + chart.getImageURI() + '">User Distribution PNG printable version</a>';
        });

        chart.draw(data, options);

    }
}


//--------------------------------------------------------------//
// when user clicked on "Edit" button in rbCert view
// update modal
//--------------------------------------------------------------//
function completeRbModal(clicked) {
    //get the entire row
    var tr = $(clicked).closest('tr');

    var row = rbCertListTable.row(tr);


    $("#modalVoRbCert #formContactEGI #vo_robot_certificate_id").val(row.data()["Id"]);

    $("#modalVoRbCert #formContactEGI #vo_robot_certificate_vo_name").val($.trim($($.parseHTML(row.data()["Vo"])).text()));

    $("#modalVoRbCert #formContactEGI #vo_robot_certificate_email").val($.trim($($.parseHTML(row.data()["Contact Email"])).text()));

    $("#modalVoRbCert #formContactEGI #vo_robot_certificate_service_name").val(row.data()["Service Name"]);

    $("#modalVoRbCert #formContactEGI #vo_robot_certificate_service_url").val(row.data()["Service Url"]);

    $("#modalVoRbCert #formContactEGI #vo_robot_certificate_robot_dn").val(row.data()["Robot DN"]);


    if (row.data()["use per-user sub-proxies"] == "Yes") {
        $("#modalVoRbCert #formContactEGI #vo_robot_certificate_use_sub_proxies").prop("checked", true);
    }


    if ($(clicked).children(".glyphicon").is(".glyphicon-remove")) {
        $("#modalVoRbCert .modal-title span:nth-child(2)").text("Remove Robot Certificate for VO  " + $.trim($($.parseHTML(row.data()["Vo"])).text()) + " of Service " + row.data()["Service Name"]);
        $("#modalVoRbCert #rbCertRemButton").show();
        $("#modalVoRbCert #deleteWarning").show();

        $("#modalVoRbCert #formContactEGI #vo_robot_certificate_save").hide();
        $("#modalVoRbCert #formContactEGI :input").attr("readonly", "readonly");


    } else {
        $("#modalVoRbCert .modal-title span:nth-child(2)").text("Edit Robot Certificate for VO  " + $.trim($($.parseHTML(row.data()["Vo"])).text()) + " of Service " + row.data()["Service Name"]);
        $("#modalVoRbCert #rbCertRemButton").hide();
        $("#modalVoRbCert #deleteWarning").hide();

        $("#modalVoRbCert #formContactEGI :input").removeAttr("readonly");

        $("#modalVoRbCert #formContactEGI #vo_robot_certificate_save").show();

        $("#subProxiesInfo").trigger("change");

    }

}


//--------------------------------------------------------------//
// reset form in RbCertModal
//--------------------------------------------------------------//
function removeRbCert(url) {
    $.ajax({
        type: "POST",
        url: url,
        data: {"rbCertId": $("#modalVoRbCert #formContactEGI #vo_robot_certificate_id").val()},
        success: function (msg) {
            $("#modalVoRbCert .modal-body .alert-danger").remove();
        },
        error: function (data) {
            $("#modalVoRbCert .modal-body").prepend('<div class="alert alert-danger"> ' + data.responseText + '</div>');
        },
        complete: function (xhr) {
            window.location.reload();
        }

    });
}

