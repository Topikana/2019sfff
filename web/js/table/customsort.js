/*
    sortEnglishDateTime
    -------------------

    This function sorts English dateTime vaues such as:

    1st January 2003, 23:32:01
    23/03/1972 � 10:22:22
    1970/13/03 at 23:22:01
    
    The function is "safe" i.e. non-dateTime data (like the word "Unknown") can be passed in and is sorted properly.
    
    UPDATE 08/01/2009: 1. Full or Short-hand english month names (e.g. "March" or "Mar") now require a space
                       or a comma after them to be properly parsed.
                       2. If no timestamp is given, a fake timestamp "00:00:00" is added to the string this enables
                       the function to parse both date and datetime data.
*/
var sortEnglishDateTime = fdTableSort.sortNumeric;

function sortEnglishDateTimePrepareData(tdNode, innerText) {
        // You can localise the function here
        var months = ['january','february','march','april','may','june','july','august','september','october','november','december','jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        // Lowercase the text
        var aa = innerText.toLowerCase();         
        
        // Replace the longhand and shorthand months with an integer equivalent
        for(var i = 0; i < months.length; i++) {                 
                aa = aa.replace(new RegExp(months[i] + '([\\s|,]{1})'), (i+13)%12 + " ");
        };

        // Replace multiple spaces and anything that is not valid in the parsing of the date, then trim
        aa = aa.replace(/\s+/g, " ").replace(/([^\d\s\/-:.])/g, "").replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        
        // REMOVED: No timestamp at the end, then return -1
        //if(aa.search(/(\d){2}:(\d){2}(:(\d){2})?$/) == -1) { return -1; };

        // No timestamp at the end, then create a false one         
        if(aa.search(/(\d){2}:(\d){2}(:(\d){2})?$/) == -1) { aa += " 00:00:00"; };
        
        
        // Grab the timestamp
        var timestamp = aa.match(/(\d){2}:(\d){2}(:(\d){2})?$/)[0].replace(/:/g, "");

        // Make the timestamp 6 characters by default
        if(timestamp.length == 4) { timestamp += "00"; };

        // Remove it from the string to assist the date parser, then trim
        aa = aa.replace(/(\d){2}:(\d){2}(:(\d){2})?$/, "").replace(/\s\s*$/, '');

        // If you want the parser to favour the parsing of European dd/mm/yyyy dates then leave this set to "true"
        // If you want the parser to favour the parsing of American mm/dd/yyyy dates then set to "false"
        var favourDMY = true;

        // If you have a regular expression you wish to add, add the Object to the end of the array
        var dateTest = [
                       { regExp:/^(0?[1-9]|1[012])([- \/.])(0?[1-9]|[12][0-9]|3[01])([- \/.])((\d\d)?\d\d)$/, d:3, m:1, y:5 },  // mdy
                       { regExp:/^(0?[1-9]|[12][0-9]|3[01])([- \/.])(0?[1-9]|1[012])([- \/.])((\d\d)?\d\d)$/, d:1, m:3, y:5 },  // dmy
                       { regExp:/^(\d\d\d\d)([- \/.])(0?[1-9]|1[012])([- \/.])(0?[1-9]|[12][0-9]|3[01])$/, d:5, m:3, y:1 }      // ymd
                       ];

        var start,y,m,d;
        var cnt = 0;
        var numFormats = dateTest.length;
        while(cnt < numFormats) {
               start = (cnt + (favourDMY ? numFormats + 1 : numFormats)) % numFormats;
               if(aa.match(dateTest[start].regExp)) {
                       res = aa.match(dateTest[start].regExp);
                       y = res[dateTest[start].y];
                       m = res[dateTest[start].m];
                       d = res[dateTest[start].d];
                       if(m.length == 1) m = "0" + String(m);
                       if(d.length == 1) d = "0" + String(d);
                       if(y.length != 4) y = (parseInt(y) < 50) ? "20" + String(y) : "19" + String(y);

                       return y+String(m)+d+String(timestamp);
               };
               cnt++;
        };
        return -1;
};

/*
    sortAlphaNumeric
    ----------------

    This function sorts alphaNumeric values e.g. 1, e, 1a, -23c, 54z
    
    Notice how the prepareData function actually returns an Array i.e. you are not limited
    in the type of data you return to the tableSort script.
*/
function sortAlphaNumericPrepareData(tdNode, innerText){
        var aa = innerText.toLowerCase().replace(" ", "");
        var reg = /((\-|\+)?(\s+)?[0-9]+\.([0-9]+)?|(\-|\+)?(\s+)?(\.)?[0-9]+)([a-z]+)/;

        if(reg.test(aa)) {
                var aaP = aa.match(reg);
                return [aaP[1], aaP[8]];
        };

        // Return an array
        return isNaN(aa) ? ["",aa] : [aa,""];
}

function sortAlphaNumeric(a, b){
        // Get the previously prepared array
        var aa = a[fdTableSort.pos];
        var bb = b[fdTableSort.pos];

        // If they are equal then return 0
        if(aa[0] == bb[0] && aa[1] == bb[1]) { return 0; };

        // Check numeric parts if not equal
        if(aa[0] != bb[0]) {
                if(aa[0] != "" && bb[0] != "") { return aa[0] - bb[0]; };
                if(aa[0] == "" && bb[0] != "") { return -1; };
                return 1;
        };
        
        // Check alpha parts if numeric parts equal
        if(aa[1] == bb[1]) return 0;
        if(aa[1] < bb[1])  return -1;
        return 1;
}

/*
    sortDutchCurrencyValues
    -----------------------

    This function sorts Dutch currency values (of the type 100.000,00)
    The function is "safe" i.e. non-currency data (like the word "Unknown") can be passed in and is sorted properly.
*/
var sortDutchCurrencyValues = fdTableSort.sortNumeric;

function sortDutchCurrencyValuesPrepareData(tdNode, innerText) {
        innerText = parseInt(innerText.replace(/[^0-9\.,]+/g, "").replace(/\./g,"").replace(",","."));
        return isNaN(innerText) ? "" : innerText;
}

/*
   sortEnglishLonghandDateFormat
   -----------------------------

   This custom sort function sorts dates of the format:

   "12th April, 2006" or "12 April 2006" or "12-4-2006" or "12 April" or "12 4" or "12 Apr 2006" etc

   The function expects dates to be in the format day/month/year. Should no year be stipulated,
   the function treats the year as being the current year.

   The function is "safe" i.e. non-date data (like the word "Unknown") can be passed in and is sorted properly.
*/
var sortEnglishLonghandDateFormat = fdTableSort.sortNumeric;

function sortEnglishLonghandDateFormatPrepareData(tdNode, innerText) {
        var months = ['january','february','march','april','may','june','july','august','september','october','november','december'];

        var aa = innerText.toLowerCase();

        // Replace the longhand months with an integer equivalent
        for(var i = 0; i < 12; i++) {
                aa = aa.replace(months[i], i+1).replace(months[i].substring(0,3), i+1);
        }

        // If there are still alpha characters then return -1
        if(aa.search(/a-z/) != -1) return -1;

        // Replace multiple spaces and anything that is not numeric
        aa = aa.replace(/\s+/g, " ").replace(/[^\d\s]/g, "");

        // If were left with nothing then return -1
        if(aa.replace(" ", "") == "") return -1;

        // Split on the (now) single spaces
        aa = aa.split(" ");

        // If something has gone terribly wrong then return -1
        if(aa.length < 2) return -1;

        // If no year stipulated, then add this year as default
        if(aa.length == 2) {
                aa[2] = String(new Date().getFullYear());
        }

        // Equalise the day and month
        if(aa[0].length < 2) aa[0] = "0" + String(aa[0]);
        if(aa[1].length < 2) aa[1] = "0" + String(aa[1]);

        // Deal with Y2K issues
        if(aa[2].length != 4) {
                aa[2] = (parseInt(aa[2]) < 50) ? '20' + aa[2] : '19' + aa[2];
        }

        // YMD (can be used as integer during comparison)
        return aa[2] + String(aa[1]) + aa[0];
}

/*
   sortScientificNotation
   ----------------------

   This custom sort function sorts numbers stipulated in scientific notation

   The function is "safe" i.e. data like the word "Unknown" can be passed in and is sorted properly.

   N.B. The only way I can think to really sort scientific notation is to convert
        it to a floating point number and then perform the sort on that. If you can think of
        an easier/better way then please let me know.
*/
var sortScientificNotation = fdTableSort.sortNumeric;

function sortScientificNotationPrepareData(tdNode, innerText) {
        var aa = innerText;

        var floatRegExp = /((\-|\+)?(\s+)?[0-9]+\.([0-9]+)?|(\-|\+)?(\s+)?(\.)?[0-9]+)/g;

        aa = aa.match(floatRegExp);

        if(!aa || aa.length != 2) return "";

        var f1 = parseFloat(aa[0].replace(" ",""))*Math.pow(10,parseFloat(aa[1].replace(" ","")));
        return isNaN(f1) ? "" : f1;
}

/*
        sortImage
        ---------

        This is the function called in order to sort the data previously prepared by the function
        "sortImagePrepareData". It does a basic case sensitive comparison on the data using the
        tableSort's in-built sortText method.
*/
var sortImage = fdTableSort.sortText;

/*
        This is the function used to prepare i.e. parse data, to be used during the sort
        of the images within the last table.

        In this case, we are checking to see if the TD node has any child nodes that are
        images and, if an image exists, return it's "src" attribute.
        If no image exists, then we return an empty string.

        The "prepareData" functions are passed the actual TD node and also the TD node inner text
        which means you are free to check for child nodes etc and are not just limited to
        sorting on the TD node's inner text.

        The prepareData functions are not required (only your bespoke sort function is required)
        and only called by the script should they exist.
*/
function sortImagePrepareData(td, innerText) {
        var img = td.getElementsByTagName('img');
        return img.length ? img[0].src: "";
}

/*
        sortFileSize
        ------------

        1 Byte = 8 Bit
        1 Kilobyte = 1024 Bytes
        1 Megabyte = 1048576 Bytes
        1 Gigabyte = 1073741824 Bytes
*/
var sortFileSize = fdTableSort.sortNumeric;

function sortFileSizePrepareData(td, innerText) {
        var regExp = /(kb|mb|gb)/i;

        var type = innerText.search(regExp) != -1 ? innerText.match(regExp)[0] : "";

        switch (type.toLowerCase()) {
                case "kb" :
                        mult = 1024;
                        break;
                case "mb" :
                        mult = 1048576;
                        break;
                case "gb" :
                        mult = 1073741824;
                        break;
                default :
                        mult = 1;
        };

        innerText = parseFloat(innerText.replace(/[^0-9\.\-]/g,''));

        return isNaN(innerText) ? "" : innerText * mult;
};

/*
        sortshortDate
        ------------

        1 day(s) = 24 hour(s)
    
*/
var sortshortDate= fdTableSort.sortNumeric;

function sortshortDatePrepareData(td, innerText) {
        var regExp = /(sec|min|hour|day|week|month|year)/i;

        var type = innerText.search(regExp) != -1 ? innerText.match(regExp)[0] : "";

        switch (type.toLowerCase()) {
                case "min":
                        mult=60;
                        break;

                case "hour":
                        mult=3600;
                        break;

                case "day" :
                        mult = 86400;
                        break;
                case "week" :
                        mult =604800;
                        break;
                case "month":
                        mult=2592000;
                        break;
                 case "year":
                        mult=31536000;
                        break;
                default :
                        mult = 1;
        };

        innerText = parseFloat(innerText.replace(/[^0-9\.\-]/g,''));

        return isNaN(innerText) ? "" : innerText * mult;
};
