
/*
 *  update AUP file name field
 */
function updateAUPFileName(new_file) {
    var vo_name = $('#voName').val();
    if (new_file) {
        var date = new Date();
        var date_chunck = date.getUTCFullYear() + pad2(date.getMonth() + 1) + pad2(date.getDate());
        var extension_chunck = getExtensionFile(new_file);
        $('#manage_aup_file_name').val(vo_name + "-AcceptableUsePolicy-" + date_chunck + "-" + date.getTime() + "." + extension_chunck);
    }
}

/*
 *  Generate AUP file name and fill AUP file name
 */
function showNewAupFile() {

    var new_file = $('#manage_aup_file_aupFile').val();
    if (new_file) {
        updateAUPFileName(new_file);
        $('#manage_aup_file_name').show();
    }
    else {
        $('#manage_aup_file_name').hide();
    }
}

/*
 *  set days and month to two digits when equals 1-9
 */
function pad2(number) {
    return (number < 10 ? '0' : '') + number
}

/*
 *  Get extension of a given file
 */
function getExtensionFile(filename) {
    return (/[.]/.exec(filename)) ? /[^.]+$/.exec(filename) : undefined;
}