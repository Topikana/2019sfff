/* 
 *  refreshing 'disable' attributs from <inputs> depending on node's check status
 *  for a given Jstree object Id
 *  @param <string> treeId, Id of a jstree object
 *  @param <object> data related to the event
 */
function refreshJstreeInputs(treeId, data) {
    // to have the changed node: var node_id =$(data.rslt).attr('id');
    var checked = data.changed.selected;


    if(checked != undefined) {
        jQuery.each(checked, function(i, val) {

            enableInput($(checked[i]).attr('id'));
            //console.log('enable checked : '+$(checked[i]).attr('id'));
        });
    }

    var unchecked = data.changed.deselected;
    if(unchecked != undefined) {
        jQuery.each(unchecked, function(i, val) {
            disableInput($(unchecked[i]).attr('id'));
            //console.log('disable unchecked : '+$(unchecked[i]).attr('id'));
        });
    }

    disableUndetermined();

    // count number of checked targets
    var temp = data.selected;



    temp = temp.filter(function(s){
        return !(~s.indexOf("all"));
    });




    $("#counter").parents("span").removeClass("label-danger").addClass("label-primary");
    $("#targetError").remove();
    //document.getElementById('counter').innerHTML = temp.length;


    return temp;

}


function countSelectedTargets() {
    var arr = [];

    //count number of selected items
    $("#jsTreeTargets div").each(function () {
        var id2 = "#" + $(this).attr("id");

        if ($(id2).jstree(true).get_selected().length > 0) {
            temp = $(id2).jstree(true).get_selected().filter(function(s){
                return !(~s.indexOf("all"));
            });

            arr.push(temp);
        }
    });

    //concatenate array to have an unidimensionnal array
    if (arr.length > 0) {
        arr = arr.reduce(function (prev, next) {
            return prev.concat(next);
        });
    }

    //add to counter div
    $('#counter').html(arr.length);
}

function disableUndetermined() {

    $('li.jstree-undetermined').each(function(elem) {
        var input_child = $('#'+$(this).attr('id')+' input:first');
        $(input_child).attr("disabled", "disabled");
    });
    
}

function enableInput(node_id) {
    if(node_id != undefined)  {
        var input_child = $('#'+node_id+' input');
        if(input_child != undefined)  {
            $(input_child).removeAttr("disabled");
        }
    }
}

function disableInput(node_id) {
    if(node_id != undefined)  {
        var input_child = $('#'+node_id+' input');
        if(input_child != undefined)  {
            $(input_child).attr("disabled", "disabled");
        }
    }
}

/*
 *  refreshing trees depending on given Json data
 */
function updateTree(json_data) {

    var array_data = $.parseJSON((json_data).replace(/&quot;/g,'"'));
    for (var i = 0; i < array_data.length; i++) {

        $("#"+array_data[i].tree).jstree("uncheck_all");

        for(var j = 0; j < array_data[i].values.length; j++){
            $("#"+array_data[i].tree).jstree("check_node","#"+array_data[i].values[j]);
        }
    }
}


