/*
 * @fileOverview IAHM DataBase
 * @version 1.0
 *
 * @author Yoann Gern http://yoanngern.ch
 * @see https://github.com/yoanngern
 * @see ...
 *
 */

// @codekit-prepend "vendor/jquery-2.1.4.js"
// @codekit-append "_functions.js"

$(document).ready(function () {

    window.api = {};

    api.dev = true;

    //getContacts();
    //postGroups();
    //putGroup();

});


function putGroup(id) {

    var data = {
        group: {
            title: "Mon titre",
            event: 2,
            members: [
                1, 3
            ],
            leaders: [
                2
            ],
            entities: [
                1
            ]
        }
    };

    putAjax(getApiUrl("groups", id), data);
}

function deleteGroup(id) {

    deleteAjax(getApiUrl("groups", id));
}

function postGroups() {

    var data = {
        group: {
            title: "Mon super groupe",
            event: 1,
            members: [],
            leaders: [],
            entities: []
        }
    };

    postAjax(getApiUrl("groups"), data);
}

function postEvents() {

    var data = {
        event: {
            title: "Mon super event",
            start: '2011-06-05 12:15:00',
            end: '2011-06-05 12:15:00',
            parent: 1,
            persons: [
                1
            ],
            groups: [
                {
                    title: "test"
                },
            ]
        }
    };

    postAjax(getApiUrl("events"), data);
}


function postEventGroups(id) {

    var data = {
        group: {
            title: "test48"
        }
    };

    postAjax(getApiUrl("events", id, "groups"), data);
}


function putEvent(id) {

    var data = {
        event: {
            title: "Mon super event2",
            /*
            start: '2011-06-05 12:15:00',
            end: '2012-06-05 12:15:00',
            parent: 2,
            persons: [
                2
            ],
            groups: []
            */
        }
    };

    putAjax(getApiUrl("events", id), data);
}

function deleteEvent(id) {

    deleteAjax(getApiUrl("events", id));
}

function postDonations() {

    var data = {
        donation: {
            date: "2012-06-05 12:15:00",
            amount: "400",
            currency: "CHF",
            type: "creditCard",
            comment_txt: "blabla",
            entity: null,
            person: null
        }
    };

    postAjax(getApiUrl("donations"), data);
}

function putDonation(id) {

    var data = {
        donation: {
            date: "2012-06-05 12:15:00",
            amount: "300",
            currency: "CHF",
            type: "creditCard",
            entity: null,
            person: 2,
            comment_txt: "erbh"
        }
    };

    putAjax(getApiUrl("donations", id), data);
}

function deleteDonation(id) {

    deleteAjax(getApiUrl("donations", id));
}

function postEntities() {

    var data = {
        family: {
            type: "family",
            name: "Nom de la famille",
            locations: [
                {
                    address: "La Sue 9",
                    postbox: "test",
                    district: "test",
                    city: "test",
                    department: "test",
                    postalCode: "test",
                    country: "test",
                    latitude: 24,
                    longitude: 24,
                    type: "test"
                }
            ],
            comment_txt: "blabla"
        }
    };

    postAjax(getApiUrl("entities"), data);
}

function putEntity(id) {

    var data = {
        family: {
            type: "business",
            name: "Nom de la famille 2",
            comment_txt: "bla"
        }
    };

    putAjax(getApiUrl("entities", id), data);
}

function deleteEntity(id) {

    deleteAjax(getApiUrl("entities", id));
}

function postEntityContacts(id) {

    var data = {
        contact: {
            firstname: "prenom",
            lastname: "nom",
            title: "title",
            gender: "man",
            dateOfBirth: "1993-05-06",
            phones: [
                {
                    number: "376418972365987",
                    type: "mobile"
                }
            ],
            emails: [
                {
                    value: "yoann@yoanngern.ch",
                    type: "private"
                }
            ],
            languages: [
                "fr", "en"
            ],
            comment_txt: "test",
            type: "father"
        }
    };

    postAjax(getApiUrl("entities", id, "contacts"), data);
}

function putEntityContact(entity, person) {

    var data = {
        person_type: {
            type: "test2"
        }
    };

    putAjax(getApiUrl("entities", entity, "contacts", person), data);
}

function deleteEntityContact(entity, person) {
    deleteAjax(getApiUrl("entities", entity, "contacts", person));
}

function postEntityDonations(id) {

    var data = {
        donation: {
            date: "2012-06-05 12:15:00",
            amount: "400",
            currency: "CHF",
            type: "creditCard",
            comment_txt: "blabla"
        }
    };

    postAjax(getApiUrl("entities", id, "donations"), data);
}

function postEntityGroups(id) {

    var data = {
        group: {
            title: "mon titre"
        }
    };

    postAjax(getApiUrl("entities", id, "groups"), data);
}

function postEntityLocations(id) {

    var data = {
        location: {
            address: "La Sue 9",
            postbox: "test",
            district: "test",
            city: "test",
            department: "test",
            postalCode: "test",
            country: "test",
            latitude: 24,
            longitude: 24,
            type: "test 2"
        }
    };

    postAjax(getApiUrl("entities", id, "locations"), data);
}

function putLocation(id) {

    var data = {
        location: {
            address: "La Sue 10",
            postbox: "test",
            district: "test",
            city: "test",
            department: "test",
            postalCode: "test",
            country: "test",
            latitude: 24,
            longitude: 24,
            type: "test 2"
        }
    };

    putAjax(getApiUrl("locations", id), data);
}

function deleteLocation(location) {
    deleteAjax(getApiUrl("locations", location));
}

function putContact(id) {

    var data = {
        contact: {
            firstname: "Yoann",
            lastname: "nom",
            title: "title",
            gender: "man",
            dateOfBirth: "1993-05-06",
            phones: [
                {
                    number: "376418972365987",
                    type: "mobile"
                }
            ],
            emails: [
                {
                    value: "yoann@yoanngern.ch",
                    type: "private"
                }
            ],
            languages: [
                "fr", "en"
            ],
            comment_txt: "test blabla",
            type: "father"
        }
    };

    putAjax(getApiUrl("contacts", id), data);
}

function deleteContact(id) {
    deleteAjax(getApiUrl("contacts", id));
}

function postContactDonations(id) {

    var data = {
        donation: {
            date: "2012-06-05 12:15:00",
            amount: "400",
            currency: "CHF",
            type: "creditCard",
            comment_txt: "blabla"
        }
    };

    postAjax(getApiUrl("contacts", id, "donations"), data);
}

function postContactMembers(id) {

    var data = {
        group: {
            title: "essai 32"
        }
    };

    postAjax(getApiUrl("contacts", id, "members"), data);
}

function postContactLeaders(id) {

    var data = {
        group: {
            title: "essai 33"
        }
    };

    postAjax(getApiUrl("contacts", id, "leaders"), data);
}

function putContactEvent(contact, event) {
    putAjax(getApiUrl("contacts", contact, "events", event));
}

function getContacts() {
    getAjax(getApiUrl("contacts"));
}

function getGroups() {
    getAjax(getApiUrl("groups"));
}

function getContactGroups(id) {
    getAjax(getApiUrl("contacts", id, "groups"));
}

function getContactEvents(id) {
    getAjax(getApiUrl("contacts", id, "events"));
}