
searchResultCache = {
    api: {
        title: 'API Documentation',
        fetched: false,
        data: []
    },
    other: {
        title: 'Other results',
        fetched: false,
        data: []
    }
};

var searchBox = $('#search');
var searchResultBox = $('#search-resultbox');
var openSearchWidth = 350;
var closedSearchWidth = 200;

renderResultList = function(resultName, limit) {

    var html = '';
    var limitHtml = '';
    if (limit) {
        limitHtml = '<span class="search-limit">' + limit + '</span>';
    }

    html += '<div class="result-head">' + searchResultCache[resultName].title + limitHtml + '</div>';
    if (searchResultCache[resultName].fetched) {
        html += '<ul>';
        if (searchResultCache[resultName].data.length) {
            html += '<li>' + searchResultCache[resultName].data.join('</li><li>') + '</li>';
        } else {
            html += '<li><p>No results.</p></li>';
        }
        html += '</ul>';
    } else {
        html += 'loading...';
    }

    return html;
};

updateSearchResults = function() {
    var $results = $('#search-resultbox');
    $results.show();

    var html = '';

    var apiLimit = '';
    if (typeof yiiSearchVersion !== 'undefined') {
        apiLimit += 'version ' + yiiSearchVersion + ' only';
    }

    var guideLimit = '';
    if (typeof yiiSearchVersion !== 'undefined' && typeof yiiSearchLanguage !== 'undefined') {
        guideLimit += 'version ' + yiiSearchVersion + ' and ' + yiiSearchLanguage + ' only';
    } else if (typeof yiiSearchVersion !== 'undefined') {
        guideLimit += 'version ' + yiiSearchVersion + ' only';
    } else if (typeof yiiSearchLanguage !== 'undefined') {
        guideLimit += yiiSearchLanguage + ' only';
    }

    html += renderResultList('api', apiLimit);
    html += renderResultList('other', guideLimit);
    //html += renderResultList('guide', guideLimit);

    // TODO: display when forum search is there
    //html += renderResultList('forum', '');
    //html += renderResultList('extension', '');

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

                searchApiDocPopulate(searchBox.val());
                updateSearchResults();
            }
        });
    }

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

                searchApiDocPopulate(searchBox.val());
                updateSearchResults();
            }
        });
    }
    searchApiDocPopulate(query);
    updateSearchResults();
};

// suggestions

renderSuggest = function(t, query) {
    return $('<a />').attr('href', t.url).text(t.title).prop('outerHTML');
};

searchSuggestResults = {};
searchSuggestResultsStatus = {};

searchSuggest = function(query) {
    // request is pending
    if (typeof searchSuggestResultsStatus[query] == 'undefined') {
        searchSuggestResultsStatus[query] = false;

        var apiUrl = '?q=' + encodeURIComponent(query);
        if (typeof yiiSearchVersion != 'undefined') {
            apiUrl += '&version=' + encodeURIComponent(yiiSearchVersion);
        }
        if (typeof yiiSearchLanguage != 'undefined') {
            apiUrl += '&language=' + encodeURIComponent(yiiSearchLanguage);
        }


        $.ajax({
            url: yiiBaseUrl + '/search/suggest' + apiUrl,
            dataType: "json",
            success: function(data) {
                //console.log(data);
                searchSuggestResults[query] = [];
                for(var i = 0; i < data.suggestions.length; ++i) {
                    searchSuggestResults[query].push(renderSuggest(data.suggestions[i], query));
                }
                searchSuggestResultsStatus[query] = true;

                searchResultCache.other.data = searchSuggestResults[query];
                searchResultCache.other.fetched = true;
                updateSearchResults();
            }
        });
    } else if (searchSuggestResultsStatus[query] == true) {
        searchResultCache.other.data = searchSuggestResults[query];
        searchResultCache.other.fetched = true;
        updateSearchResults();
    }
};


renderGuide = function(t, query) {
    return $('<a />').attr('href', t.url).text(t.title).append(
        $('<span class="result-annotation">').text(t.language + ', ' + t.version)
    ).prop('outerHTML');
};

/* TODO currently no live results for other types than api
searchGuideResults = {};
searchGuideResultsStatus = {};

searchGuide = function(query) {
    // request is pending
    if (typeof searchGuideResultsStatus[query] == 'undefined') {
        searchGuideResultsStatus[query] = false;

        var apiUrl = '?q=' + encodeURIComponent(query);
        if (typeof yiiSearchVersion != 'undefined') {
            apiUrl += '&version=' + encodeURIComponent(yiiSearchVersion);
        }
        if (typeof yiiSearchLanguage != 'undefined') {
            apiUrl += '&language=' + encodeURIComponent(yiiSearchLanguage);
        }


        $.ajax({
            url: yiiBaseUrl + '/search/as-you-type' + apiUrl,
            dataType: "json",
            success: function(data) {
                //console.log(data);
                searchGuideResults[query] = [];
                for(var i = 0; i < data.length; ++i) {
                    searchGuideResults[query].push(renderGuide(data[i], query));
                }
                searchGuideResultsStatus[query] = true;

                searchResultCache.guide.data = searchGuideResults[query];
                searchResultCache.guide.fetched = true;
                updateSearchResults();
            }
        });
    } else if (searchGuideResultsStatus[query] == true) {
        searchResultCache.guide.data = searchGuideResults[query];
        searchResultCache.guide.fetched = true;
        updateSearchResults();
    }
};

searchExtensionResults = {};
searchExtensionResultsStatus = {};

searchExtension = function(query) {
    if (typeof searchExtensionResultsStatus[query] === 'undefined') {
        searchExtensionResultsStatus[query] = false;

        var apiUrl = '?q=' + encodeURIComponent(query);
        $.ajax({
            url: yiiBaseUrl + '/search/extension' + apiUrl,
            dataType: "json",
            success: function(data) {
                searchExtensionResults[query] = [];
                for(var i = 0; i < data.length; ++i) {
                    searchExtensionResults[query].push(renderExtension(data[i], query));
                }
                searchExtensionResultsStatus[query] = true;

                searchResultCache.extension.data = searchExtensionResults[query];
                searchResultCache.extension.fetched = true;
                updateSearchResults();
            }
        });
    } else if (searchExtensionResultsStatus[query] === true) {
        searchResultCache.extension.data = searchExtensionResults[query];
        searchResultCache.extension.fetched = true;
        updateSearchResults();
    }
};

renderExtension = function(t, query) {
    return $('<a>').attr('href', t.url).html(highlight(t.title, query)).prop('outerHTML');
};*/

highlight = function(s, h) {
    if (typeof h === "undefined" || h == '') {
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
};

renderType = function(t, query) {
    return $('<a>').attr('href', t.url).html(highlight(t.name, query) + ' ').prop('outerHTML');
};

renderMember = function(m, query, ownerFilter) {
    var name = m.name;
    if (m.type == 'method') {
        name += '()';
    }

    var impl = m.implemented;
    if (ownerFilter != '') {
        impl = impl.filter(function(owner) {
            if (owner.name == ownerFilter) {
                return true;
            } else if (owner.name.toLowerCase().indexOf(ownerFilter.toLowerCase()) > -1) {
                return true;
            } else {
                return false;
            }
        });
    }

    var html = [];
    for (var i = 0; i < impl.length; ++i) {
        html.push('<a href="' + impl[i].url + '">' + highlight(impl[i].name, ownerFilter) + '::' + highlight(name, query) + ' ' + m.type + '</a>');
    }
    return html.join("</li>\n<li>");
};

searchApiDocPopulate = function(query) {

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
        searchApiDocPopulateMembers(query, memberSearchType);
    } else {
        searchApiDocPopulateTypes(query);
    }

};

// search in method, property, const, and event names
searchApiDocPopulateMembers = function(query, owner) {

    var bestMatch = [];
    var secondMatch = [];

    if (!searchApiDocMembers) {
        return;
    }

    if (query.length > 1 || owner != ''/* && query.length > 0*/) {
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
                    var matchOwnerPos = m.implemented[o].name.toLowerCase().indexOf(owner.toLowerCase());
                    if (matchOwnerPos > -1) {
                        matchOwner = true;
                        break;
                    }
                }
                if (!matchOwner) {
                    continue;
                }
            }

            var name = m.name;
            if (m.type == 'method') {
                name += '()';
            }
            var match = name.toLowerCase().indexOf(query.toLowerCase());
            // also match getter and setter names
            var getmatch = name.toLowerCase().indexOf('get' + query.toLowerCase());
            var setmatch = name.toLowerCase().indexOf('set' + query.toLowerCase());
            if (match == 0 || getmatch == 0 || setmatch == 0) {
                bestMatch.push(renderMember(m, query, owner));
            } else if (match == 1 && name.substring(0, 1) === '$') {
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
    if (searchApiDocTypes) {
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
    }
    // also search for members on normal search
    if (searchApiDocMembers) {
        for (i = 0, len = searchApiDocMembers.length; i < len; ++i) {
            var m = searchApiDocMembers[i];

            // filter by version
            if (typeof yiiSearchVersion !== 'undefined' && yiiSearchVersion != m.version) {
                continue;
            }

            var name = m.name;
            if (m.type == 'method') {
                name += '()';
            }
            var match = name.toLowerCase().indexOf(query.toLowerCase());
            // also match getter and setter names
            var getmatch = name.toLowerCase().indexOf('get' + query.toLowerCase());
            var setmatch = name.toLowerCase().indexOf('set' + query.toLowerCase());
            if (match == 0 || getmatch == 0 || setmatch == 0) {
                bestMatch.push(renderMember(m, query, ''));
            } else if (match == 1 && name.substring(0, 1) === '$') {
                secondMatch.push(renderMember(m, query, ''));
            }

        }
    }

    searchResultCache.api.data = bestMatch.concat(secondMatch);
    searchResultCache.api.fetched = true;
};

adjustSearchBoxSize = function() {

    // TODO may need to dynamically adjust search with dependend on screen size
    //openSearchWidth = ($('.container').width() - $('#main-nav').width() - $('#main-nav-head').width() - 120) * 0.7;
    //closedSearchWidth = Math.floor(openSearchWidth * 0.75);

    if (searchBox.val() == "") {
        searchBox.width(closedSearchWidth + "px");
    } else {
        searchBox.width(openSearchWidth + "px");
    }
    searchResultBox.width((openSearchWidth - 2) + "px"); // -2 for border

};

jQuery(window).resize(adjustSearchBoxSize);

jQuery(document).ready(function () {

    adjustSearchBoxSize();
    // animate search box to open on focus
    searchBox.focus(function() {
        $(this).animate({width: openSearchWidth + "px"}, 250);
    });
    searchBox.blur(function() {
        var $this = $(this);
        if ($this.val() == "") {
            $this.animate({width: closedSearchWidth + "px"}, 250);
        }
    });

    // search when typing in search field
    searchBox.on("keyup", function(event) {
        var query = $(this).val();

        //console.log(event.which);

        if (query == '' || event.which == 27) {
            searchResultBox.hide();
            return;
        } else if (event.which == 13) {
            var selectedLink = searchResultBox.find('a.selected');
            if (selectedLink.length != 0) {
                document.location = selectedLink.attr('href');
                event.stopPropagation();
                return;
            }
        } else if (event.which == 38 || event.which == 40 || event.which == 34 || event.which == 33) {
            searchResultBox.show();

            var selected = searchResultBox.find('a.selected');
            if (selected.length == 0) {
                searchResultBox.find('ul li a').first().addClass('selected');
            } else {
                var next;
                if (event.which == 40) {
                    // down
                    next = selected.parent().next().find('a').first();
                } else if (event.which == 38) {
                    // up
                    next = selected.parent().prev().find('a').first();
                } else if (event.which == 34) {
                    // page down
                    var i = 0;
                    var n = selected;
                    while(i++ < 10 && n.length > 0) {
                        next = n;
                        n = n.parent().next().find('a').first();
                    }
                } else if (event.which == 33) {
                    // page up
                    var i = 0;
                    var n = selected;
                    while(i++ < 10 && n.length > 0) {
                        next = n;
                        n = n.parent().prev().find('a').first();
                    }
                }
                if (next.length != 0) {
                    // position of next relative to the top of the result bar
                    var position = Math.floor(next.position().top);
                    var resultUl = selected.parent().parent();
                    var currentScroll = Math.floor(resultUl.scrollTop());

                    resultUl.animate({
                        scrollTop: Math.max(currentScroll + position - 60, 0)
                    }, 100);

                    selected.removeClass('selected');
                    next.addClass('selected');
                }
            }

            event.stopPropagation();

            return;
        }

        searchApidoc(query);
        searchSuggest(query);
        //searchExtension(query);

        // TODO search guide and others
    });

    // hide the search results on ESC
    $(document).on("keyup", function(event) { if (event.which == 27) { $('#search-resultbox').hide(); } });
    // hide search results on click to document
    $(document).bind('click', function (e) { $('#search-resultbox').hide(); });
    // except the following:
    searchBox.bind('click', function(e) { e.stopPropagation(); });
    $('#search-resultbox').bind('click', function(e) { e.stopPropagation(); });


});
