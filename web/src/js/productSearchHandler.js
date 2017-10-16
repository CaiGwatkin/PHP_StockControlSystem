/**
 * Product Search Handler file.
 *
 * Handles updating search results table.
 */

/**
 * Template of the table rows
 * @type {string}
 */
var rowTemplate =
    '<tr class="data_row">' +
        '<td class="sku">{0}</td>' +
        '<td class="name">{1}</td>' +
        '<td class="category">{2}</td>' +
        '<td class="cost, currency">{3}</td>' +
        '<td class="stock, number">{4}</td>' +
    '</tr>';

/**
 * The input field in which the search term is typed.
 * @type {*|jQuery|HTMLElement}
 */
var searchInput = null;

/**
 * The table in which to display search results.
 * @type {*|jQuery|HTMLElement}
 */
var searchResultsTable = null;

/**
 * The row in which the header values exist.
 * @type {*|jQuery|HTMLElement}
 */
var headerRow = null;

/**
 * The term to search the database with.
 * @type {string}
 */
var searchTerm = '';

/**
 * The column to order results by.
 * @type {string}
 */
var orderBy = 'sku';

/**
 * The sort order of results { true: 'ASC', false: 'DESC' }.
 * @type {boolean}
 */
var sort = true;

/**
 * Shorthand for product variables names returned in JSON format.
 * @type {{sku: string, name: string, category: string, cost: string, costString: string, stock: string}}
 */
var productVariables = {
    'sku': '_sku',
    'name' : '_name',
    'category' : '_category',
    'cost' : '_cost',
    'costString' : '_costString',
    'stock' : '_stock'
};

/**
 * Allow formatting of strings.
 *
 * @returns {*}
 */
String.prototype.format = function() {
    var str = this;
    for (var i = 0; i < arguments.length; i++) {
        var reg = new RegExp("\\{" + i + "\\}", "gm");
        str = str.replace(reg, arguments[i]);
    }
    return str;
};

/**
 * Once HTML document is fully loaded, setup event handlers.
 */
$(document).ready(function() {
    // Find elements with JQuery.
    searchInput = $('input[type=search]');
    searchResultsTable = $('#search_results');
    headerRow = $('#header');

    // Initialise search results table.
    manageGetParameters(searchInput);
    getNewRows();   // When table in database is more full, remove this as will load entire table.

    // Add listeners.
    searchInput.keyup(search);
    searchResultsTable.on('click', 'th', reOrderResults);
});

/**
 * Setup
 *
 * @param searchInput
 */
function manageGetParameters(searchInput) {

    var temp = getQueryVariable('q');
    if (temp) {
        searchTerm = temp;
        searchInput.val(searchTerm);
    }
    temp = getQueryVariable('orderBy');
    if (temp) {
        orderBy = temp;
    }
    temp = getQueryVariable('sort');
    if (temp) {
        sort = temp==='ASC';
    }
}

/**
 * Update URL with get parameters and update search results.
 */
function search() {

    searchTerm = searchInput.val();
    if (searchTerm) {
        updateURLParameters();
        getNewRows();
    }
    else {
        window.history.replaceState('search', document.title, document.location.href.split('?')[0]);
        getNewRows();   // When table in database is more full, remove this as will load entire table.
    }
}

/**
 * Request results in new order based on class of clicked element.
 */
function reOrderResults() {

    var column = $(this).attr('class'),
        span = $(this).find('span');
    if (orderBy === column) {
        sort = !sort;
        if (sort) {
            span.attr('class', 'sorting_asc');
        }
        else {
            span.attr('class', 'sorting_desc');
        }
    }
    else {
        sort = true;
        orderBy = column;
        span.attr('class', 'sorting_asc');
        $(this).siblings().each(function() {
            $(this).find('span').attr('class', 'sorting');
        });
    }
    updateURLParameters();
    getNewRows();
}

/**
 * Update URL parameters.
 */
function updateURLParameters() {

    var newURL = '?q='+searchTerm+'&orderBy='+orderBy+'&sort='+(sort?'ASC':'DESC');
    window.history.replaceState('search', document.title, newURL);
}

/**
 * Send get request to database for new rows.
 * Parse JSON on success to add rows to table.
 */
function getNewRows() {

    $.ajax({
        type: 'GET',
        url: '/js/updateSearchResults',
        data: {
            'q': searchTerm,
            'orderBy': orderBy,
            'sort': sort?'ASC':'DESC'
        },
        dataType: 'json',
        success: [
            function (data) {
                searchResultsTable.html(headerRow);
                $.each(data, function(index, product) {
                    searchResultsTable.append(rowTemplate.format(
                        product[productVariables['sku']],
                        product[productVariables['name']],
                        product[productVariables['category']],
                        product[productVariables['costString']],
                        product[productVariables['stock']]
                    ));
                });
            }
        ]
    });
}

/**
 * Function used from https://css-tricks.com/snippets/javascript/get-url-variables/
 *
 * @param variable
 * @returns {*}
 */
function getQueryVariable(variable)  {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (pair[0] === variable) {
            return pair[1];
        }
    }
    return false;
}
