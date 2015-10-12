
searchResultCache = {
    api: {
        fetched: false,
        data: null
    },
    guide: {
        fetched: false,
        data: null
    },
    forum: {
        fetched: false,
        data: null
    }
};

updateSearchResults = function() {
    var $results = $('#search-resultbox');
    $results.show();

    var html = '';

    html += '<div class="search-result-head">Class reference</div>';
    if (searchResultCache.api.fetched) {
        html += '<ul>';
        html += searchResultCache.api.data.join(' ');
        html += '</ul>';
    }

    html += '<div class="search-result-head">Guide</div>';
    if (searchResultCache.guide.fetched) {
        html += '<ul>';
        html += searchResultCache.guide.data;
        html += '</ul>';
    }

    html += '<div class="search-result-head">Forum</div>';
    if (searchResultCache.forum.fetched) {
        html += '<ul>';
        html += searchResultCache.forum.data;
        html += '</ul>';
    }

    $results.html(html);
};

// holds information about Api class types
var searchApiDocTypes;
// indicates whether a request for data is pending
var searchApiDocTypeStatus = false;

searchApidoc = function(query) {

    // fetch types if not loaded yet
    if (!searchApiDocTypes) {

        // request is pending
        if (searchApiDocTypeStatus) {
            return;
        }
        searchApiDocTypeStatus = true;
        $.ajax({
            url: yiiBaseUrl + '/doc/api/2.0', // TODO multi version
            dataType: "json",
            success: function(data) {
                searchApiDocTypes = data.classes;
                searchApiDocTypeStatus = false;

                searchApiDocPopulate(query);
                updateSearchResults();
            }
        });
    } else {
        searchApiDocPopulate(query);
        updateSearchResults();
    }
}

searchApiDocPopulate = function(query) {

    // search for a property, method, constant or event name
    //
    // e.g.
    // .getId()
    // ::$cache
    // ::cache
    //
    var primitiveSearch = false;
    if (query.substring(0, 1) == '.') {
        primitiveSearch = true;
        query = query.substring(1);
    }
    if (query.substring(0, 2) == '::') {
        primitiveSearch = true;
        query = query.substring(2);
    }

    if (primitiveSearch) {

        var matches = [];

        // TODO do the primitive match

        searchResultCache.api.data = matches;
        searchResultCache.api.fetched = true;

    } else {
        // search in class names

        var bestMatch = [];
        var secondMatch = [];

        var i, len;
        for (i = 0, len = searchApiDocTypes.length; i < len; ++i) {
            var c = searchApiDocTypes[i];
            var baseNameMatch = -1;
            if (query.indexOf('\\') < 0 && (p = c.name.lastIndexOf('\\')) >= 0) {
                baseNameMatch = c.name.substring(p + 1).toLowerCase().indexOf(query.toLowerCase());
    //            console.log('bnm: ' + c.name + ' ' + c.name.substring(p) + '  '  + baseNameMatch);
            }
            var match = c.name.toLowerCase().indexOf(query.toLowerCase());
            if (match == 0 || baseNameMatch == 0) {
                bestMatch.push(
                    '<p>' +
                        '<a href="' + c.url + '"><strong>' + c.name + '</strong> ' + c.description + '</a>' +
                        '</p>'
                );
            } else if (match > -1) {
                // limit results to 5
                if (secondMatch.length > 5) {
                    continue;
                }
                secondMatch.push(
                    '<p>' +
                        '<a href="' + c.url + '"><strong>' + c.name + '</strong> ' + c.description + '</a>' +
                    '</p>'
                );
            }

        }

        searchResultCache.api.data = bestMatch.concat(secondMatch);
        searchResultCache.api.fetched = true;
    }
}

jQuery(document).ready(function () {

    var searchBox = $('#search');

    // animate search box to open on focus
    searchBox.focus(function() {
        $(this).animate({width: "255px"}, 50);
    });
    searchBox.blur(function() {
        var $this = $(this);
        if ($this.val() == "") {
            $this.animate({width: "150px"}, 50);
        }
    });


    // search when typing in search field
    searchBox.on("keyup", function(event) {
        var query = $(this).val();

        if (query == '' || event.which == 27) {
            $('#search-resultbox').hide();
            return;
//        } else if (event.which == 13) {
//            var selectedLink = $('#search-resultbox a.selected');
//            if (selectedLink.length != 0) {
//                document.location = selectedLink.attr('href');
//                return;
//            }
//        } else if (event.which == 38 || event.which == 40) {
//            $('#search-resultbox').show();
//
//            var selected = $('#search-resultbox a.selected');
//            if (selected.length == 0) {
//                $('#search-results').find('a').first().addClass('selected');
//            } else {
//                var next;
//                if (event.which == 40) {
//                    next = selected.parent().next().find('a').first();
//                } else {
//                    next = selected.parent().prev().find('a').first();
//                }
//                if (next.length != 0) {
//                    var resultbox = $('#search-results');
//                    var position = next.position();
//
////              TODO scrolling is buggy and jumps around
////                resultbox.scrollTop(Math.floor(position.top));
////                console.log(position.top);
//
//                    selected.removeClass('selected');
//                    next.addClass('selected');
//                }
//            }
//
//            return;
        }
//        $('#search-resultbox').show();
//        $('#search-resultbox').html('<li><span class="no-results">No results</span></li>');
//        updateSearchResults();

        searchApidoc(query);

//        var result = jssearch.search(query);
//
//        if (result.length > 0) {
//            var i = 0;
//            var resHtml = '';
//
//            for (var key in result) {
//                if (i++ > 20) {
//                    break;
//                }
//                resHtml = resHtml +
//                    '<li><a href="' + result[key].file.u.substr(3) +'"><span class="title">' + result[key].file.t + '</span>' +
//                    '<span class="description">' + result[key].file.d + '</span></a></li>';
//            }
//            $('#search-results').html(resHtml);
//        }
//        alert(query);
    });

// hide the search results on ESC
    $(document).on("keyup", function(event) { if (event.which == 27) { $('#search-resultbox').hide(); } });
// hide search results on click to document
    $(document).bind('click', function (e) { $('#search-resultbox').hide(); });
// except the following:
    searchBox.bind('click', function(e) { e.stopPropagation(); });
    $('#search-resultbox').bind('click', function(e) { e.stopPropagation(); });









});

