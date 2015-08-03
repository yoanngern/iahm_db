/**
 *
 * @param url
 */
function getAjax(url) {
    $.ajax({
        type: "GET",
        dataType: "json",
        url: url,
        error: function () {
            console.log("error");
        },
        success: function (data) {

            console.log(data);

        }
    });
}


/**
 *
 * @param url
 * @param data
 */
function postAjax(url, data) {
    $.ajax({
        type: "POST",
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        url: url,
        data: JSON.stringify(data),
        statusCode: {
            201: function() {
                console.log("Created");
            },
            204: function() {
                console.log("No Content");
            },
            400: function() {
                console.log("Bad Request");
            },
            404: function() {
                console.log("Not Found");
            }
        }
    });
}


function putAjax(url, data) {
    $.ajax({
        type: "PUT",
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        url: url + "?access_token=NDgzOTYyZTY4Y2ZhMzU5OTM2ODExMGNlODUxODI2ZThhZmNlYWQxYTdiZWUyYjBiNzU3ZDRlOGEwMjI5ZWI2OA",
        data: JSON.stringify(data),
        statusCode: {
            201: function() {
                console.log("Created");
            },
            204: function() {
                console.log("No Content");
            },
            400: function() {
                console.log("Bad Request");
            },
            404: function() {
                console.log("Not Found");
            }
        }
    });
}


function deleteAjax(url) {
    $.ajax({
        type: "DELETE",
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        url: url,
        statusCode: {
            201: function() {
                console.log("Created");
            },
            204: function() {
                console.log("No Content");
            },
            400: function() {
                console.log("Bad Request");
            },
            404: function() {
                console.log("Not Found");
            }
        }
    });
}


/**
 *
 * @param service
 * @param id
 * @param sub_service
 * @param format
 * @returns {string}
 */
function getApiUrl(service, id, sub_service, sub_id, format) {
    id = typeof id !== 'undefined' ? id : "";
    sub_service = typeof sub_service !== 'undefined' ? sub_service : "";
    sub_id = typeof sub_id !== 'undefined' ? sub_id : "";
    format = typeof format !== 'undefined' ? format : "json";

    var url = "";

    if (api.dev) {
        url = "app_dev.php"
    }

    url += "/api/" + service;

    if (id != "") {
        url += "/" + id;
    }

    if (sub_service != "") {
        url += "/" + sub_service;
    }

    if (sub_id != "") {
        url += "/" + sub_id;
    }

    url += "." + format;

    return url;
}