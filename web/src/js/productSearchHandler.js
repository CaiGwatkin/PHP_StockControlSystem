/**
 * Product Search Handler file.
 *
 * Handles updating search results table.
 */

// <?php if ($products) {
//     foreach ($products as $product) { ?>
//     <tr class='data_row'>
//             <td class='sku'><?= $product->getSKU() ?></td>
//         <td class='name'><?= $product->getName() ?></td>
//         <td class='category'><?= $product->getCategory() ?></td>
//         <td class='cost, currency'><?= $product->getCostString() ?></td>
//         <td class='stock, number'><?= $product->getStockQuantity() ?></td>
//         </tr>
//         <?php }
// } ?>

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

var rowTemplate =
    '<tr class="data_row">' +
        '<td class="sku">{0}</td>' +
        '<td class="name">{1}</td>' +
        '<td class="category">{2}</td>' +
        '<td class="cost, currency">{3}</td>' +
        '<td class="stock, number">{4}</td>' +
    '</tr>',
    searchTerm = '',
    orderBy = 'sku',
    sort = true;

/**
 * Once HTML document is fully loaded, setup event handlers.
 */
$(document).ready(function() {
    var searchInput = $('input[type=search]'),
        searchResultsTable = $('#search_results'),
        headerRow = $('#header'),
        skuHeader = $('#header th[class=sku]'),
        nameHeader = $('#header th[class=name]'),
        categoryHeader = $('#header th[class=category]'),
        costHeader = $('#header th[class=cost]'),
        stockHeader = $('#header th[class=stock]');

    manageGetParameters(searchInput);

    searchInput.keyup(function() {
        searchTerm = searchInput.val();
        if (searchTerm) {
            updateURL();
            getNewRows(searchResultsTable, headerRow);
        }
        else {
            window.history.pushState('search', document.title, document.location.href.split('?')[0]);
        }
    });

    skuHeader.click(changeSorting('sku'));
    nameHeader.click(changeSorting('name'));
    categoryHeader.click(changeSorting('category'));
    costHeader.click(changeSorting('cost'));
    stockHeader.click(changeSorting('stock'));
});

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

function updateURL() {

    var newURL = '?q='+searchTerm+'&orderBy='+orderBy+'&sort='+(sort?'ASC':'DESC');
    window.history.pushState('search', document.title, newURL);
}

function changeSorting(column) {
    if (orderBy === column) {
        sort = !sort;
    }
    else {
        sort = true;
        orderBy = column;
    }
    updateURL();
}

function getNewRows(searchResultsTable, headerRow) {

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
                        product['_sku'],
                        product['_name'],
                        product['_category'],
                        product['_cost'],
                        product['_stock']
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
