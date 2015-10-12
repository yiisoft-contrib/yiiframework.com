
searchResultCache = {
    api: {
        title: 'API Documentation',
        fetched: false,
        data: []
    },
    guide: {
        title: 'Guide',
        fetched: false,
        data: []
    },
    forum: {
        title: 'Forum',
        fetched: false,
        data: []
    }
};

renderResultList = function(resultName) {

    var html = '';

    html += '<div class="result-head">' + searchResultCache[resultName].title + '</div>';
    if (searchResultCache[resultName].fetched) {
        html += '<ul class="result-list">';
        if (searchResultCache[resultName].data.length == 0) {
            html += '<li>No results.</li>';
        } else {
            html += '<li>' + searchResultCache[resultName].data.join('</li><li>') + '</li>';
        }
        html += '</ul>';
    } else {
        html += 'loading...';
    }

    return html;
}

updateSearchResults = function() {
    var $results = $('#search-resultbox');
    $results.show();

    var html = '';

    if (typeof yiiSearchVersion !== 'undefined') {
        html += 'search scope: version ' + yiiSearchVersion;
    }

    html += renderResultList('api');
    html += renderResultList('guide');
    html += renderResultList('forum');

    $results.html(html);
};

// holds information about Api class types
var searchApiDocTypes;
// indicates whether a request for data is pending
var searchApiDocTypeStatus = false;
// holds information about Api primitives like methods, properties, etc...
var searchApiDocMembers;
// indicates whether a request for data is pending
var searchApiDocMembersStatus = false;

searchApidoc = function(query) {

    var memberSearch = false;
    var memberSearchType = '';
    var p;
    if (query.substring(0, 1) == '.') {
        memberSearch = true;
        query = query.substring(1);
    } else if ((p = query.indexOf('.')) > -1) {
        memberSearch = true;
        memberSearchType = query.substring(0, p);
        query = query.substring(p + 1);
    } else if (query.substring(0, 2) == '::') {
        memberSearch = true;
        query = query.substring(2);
    } else if ((p = query.indexOf('::')) > -1) {
        memberSearch = true;
        memberSearchType = query.substring(0, p);
        query = query.substring(p + 2);
    }

    // search for a property, method, constant or event name
    //
    // e.g.
    // .getId()
    // ::$cache
    // ::cache
    //
    if (memberSearch) {
        // fetch methods if not loaded yet
        if (!searchApiDocMembers) {

            // request is pending
            if (searchApiDocMembersStatus) {
                return;
            }
            searchApiDocMembersStatus = true;
            $.ajax({
                url: yiiBaseUrl + '/doc/api/class-members',
                dataType: "json",
                success: function(data) {
                    searchApiDocMembers = data.members;
                    searchApiDocMembersStatus = false;

                    searchApiDocPopulateMembers(query, memberSearchType);
                    updateSearchResults();
                }
            });
        } else {
            searchApiDocPopulateMembers(query, memberSearchType);
        }
    } else {
        // fetch types if not loaded yet
        if (!searchApiDocTypes) {

            // request is pending
            if (searchApiDocTypeStatus) {
                return;
            }
            searchApiDocTypeStatus = true;
            $.ajax({
                url: yiiBaseUrl + '/doc/api',
                dataType: "json",
                success: function(data) {
                    searchApiDocTypes = data.classes;
                    searchApiDocTypeStatus = false;

                    searchApiDocPopulateTypes(query);
                    updateSearchResults();
                }
            });
        } else {
            searchApiDocPopulateTypes(query);
        }
    }
    updateSearchResults();
};

highlight = function(s, h) {
    if (h == '') {
        return s;
    }

    var pos = 0;
    var result = '';

    while((pos = s.toLowerCase().indexOf(h.toLowerCase())) > -1) {
        result += s.substring(0, pos);
        result += '<strong>' + s.substring(pos, pos + h.length) + '</strong>';
        s = s.substring(pos + h.length);
    }

    return result + s;
}

renderType = function(t, query) {
    return '<p>' +
        '<a href="' + t.url + '">' + highlight(t.name, query) + ' ' /*+ t.description*/ + '</a>' +
    '</p>';
};

renderMember = function(m, query, ownerFilter) {
    var name = m.name;
    if (m.type == 'method') {
        name += '()';
    }

    var impl = m.implemented;
    if (ownerFilter != '') {
        impl = impl.filter(function(owner) {
            if (owner == ownerFilter) {
                return true;
            } else if (owner.indexOf(ownerFilter) > -1) {
                return true;
            } else {
                return false;
            }
        });
    }

    if (impl.length > 1) {
        return '<p>' +
            '<a href="' + /*c.url +*/ '">' + highlight(name, query) + ' ' + m.type + ' of ' /* + c.description*/ + highlight(impl.join(', '), ownerFilter) + '</a>' +
            '</p>';
    } else {
        return '<p>' +
            '<a href="' + /*c.url +*/ '">' + highlight(impl.join(', '), ownerFilter) + '::' + highlight(name, query) + ' ' + m.type + '</a>' +
            '</p>';
    }
};

// search in method, property, const, and event names
searchApiDocPopulateMembers = function(query, owner) {

    var bestMatch = [];
    var secondMatch = [];

    if (query.length > 1 || owner != '' && query.length > 0) {
        var i, len;
        for (i = 0, len = searchApiDocMembers.length; i < len; ++i) {
            var m = searchApiDocMembers[i];

            // filter by version
            if (typeof yiiSearchVersion !== 'undefined' && yiiSearchVersion != m.version) {
                continue;
            }

            // filter by owner
            if (owner != '') {
                var matchOwner = false;
                for(var o = 0; o < m.implemented.length; ++o) {
                    var matchOwnerPos = m.implemented[o].toLowerCase().indexOf(owner.toLowerCase());
                    if (matchOwnerPos > -1) {
                        matchOwner = true;
                        break;
                    }
                } // TODO filter all unmatched owners
                if (!matchOwner) {
                    continue;
                }
            }

            var name = m.name;
            if (m.type == 'method') {
                name += '()';
            }
            var match = name.toLowerCase().indexOf(query.toLowerCase());
            if (match == 0) {
                bestMatch.push(renderMember(m, query, owner));
            } else if (match == 1 && name.substring(0, 1) == '$') {
                secondMatch.push(renderMember(m, query, owner));
            }

        }
    }

    searchResultCache.api.data = bestMatch.concat(secondMatch);
    searchResultCache.api.fetched = true;
};

// search in class names
searchApiDocPopulateTypes = function(query) {

    var bestMatch = [];
    var secondMatch = [];

    var i, len;
    for (i = 0, len = searchApiDocTypes.length; i < len; ++i) {
        var c = searchApiDocTypes[i];

        // filter by version
        if (typeof yiiSearchVersion !== 'undefined' && yiiSearchVersion != c.version) {
            continue;
        }

        var baseNameMatch = -1;
        if (query.indexOf('\\') < 0 && (p = c.name.lastIndexOf('\\')) >= 0) {
            baseNameMatch = c.name.substring(p + 1).toLowerCase().indexOf(query.toLowerCase());
//            console.log('bnm: ' + c.name + ' ' + c.name.substring(p) + '  '  + baseNameMatch);
        }
        var match = c.name.toLowerCase().indexOf(query.toLowerCase());
        if (match == 0 || baseNameMatch == 0) {
            bestMatch.push(renderType(c, query));
        } else if (match > -1) {
            // limit results to 5
            if (secondMatch.length > 5) {
                continue;
            }
            secondMatch.push(renderType(c, query));
        }

    }

    searchResultCache.api.data = bestMatch.concat(secondMatch);
    searchResultCache.api.fetched = true;
};

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

