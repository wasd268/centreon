/* global jQuery, navigator, centreonMultiSelectLocales */

function mutliSelect(settings, elem) {
    var self = this;

    self.settings = settings;
    self.page = 1;
    self.elem = elem;
    self.elem.addClass('ms-bloc');
    self.ajax = self.settings.multiSelect.ajax;
    self.hasFilteredDatas = false;
    self.selectedDatas = {};
    self.id = self.elem.attr('id');

    self.initElements();
    self.initNiceScroll(elem);
    self.handleEvent();
    self.displayDatas();
}

mutliSelect.prototype = {

    initElements: function() {
        var self=this;

        self.elem.parent = jQuery('<div class="multiselect-elem">');
        self.elem.wrap(self.elem.parent);
        self.elem.searchBox = jQuery('<div class="ms-filter">');
        self.elem.before(self.elem.searchBox);
    },

    /**
     * Handle keypress event on search
     */
    handleEvent: function() {
        var self = this,
            keyword,
            input;

        input = jQuery('<input type="text" value="" placeholder="Search..."/>');
        self.elem.searchBox.append(input);

        jQuery(input).on('keyup', function(e) {
            keyword = input.val();

            if (e.which == 13) {
                return false;
            }

            console.log(keyword);

            if(keyword.length >= 3) {
                self.searchData(keyword);
            } else if(keyword === '') {
                self.displayDatas();
            }
        })
    },

    /**
     * Search by keyword and display results
     * @param keyword
     */
    searchData: function(keyword) {
        var self = this,
            url = self.ajax.url + '&q=' + keyword;

            jQuery.ajax({
                url: url,
                type : 'GET',
                dataType: 'json',
                contentType: 'application/json',
                success: function(data) {
                    var datas = '';
                    if (data != null) {
                        jQuery.each(data.items, function(idx,value) {
                            datas += '<div class="ms-elem">' +
                                '<input type="checkbox" name="' + self.id + '[]" id="elem_' + value.id + '" value="' + value.id + '" />' +
                                '<label for="elem_' + value.id + '" class="checkbox-label">' + value.text + '</label>' +
                                '</div>';
                        })
                        if (datas == ''){
                            self.elem.html('<p class="ms-elem">Element not found !</p>');
                        } else {
                            self.elem.html(datas);
                        }

                    } else {
                        console.log('Element not found !');
                    }
                },
                error: function() {
                    console.log('Element not found !');
                }
            });
    },

    /**
     * Get selected Datas if they exist
     */
    displayDatas: function() {
        var self = this,
            hasSelectedDatas;

        if (self.settings.url !== null) {

            jQuery.ajax({
                url: self.settings.url,
                type : 'GET',
                dataType: 'json',
                contentType: 'application/json',
                success: function(datas) {
                    if (datas != null) {
                            self.selectedDatas = datas;
                    }
                },
                error: function(XMLHttpRequest, textStatus, error) {
                    console.log(error);
                }
            }).done(function() {
                if (self.selectedDatas.length === 0 || self.selectedDatas === undefined) {
                    hasSelectedDatas = false;
                } else {
                    hasSelectedDatas = true;
                }

                self.getIntialDatas(hasSelectedDatas,self.selectedDatas);
            });

        } else {
            console.log('url undefined !');
        }
    },

    /**
     * Get datas with infinite scroll
     * @param hasSelectedDatas
     * @param selectedDatas
     */
    getIntialDatas: function(hasSelectedDatas, selectedDatas) {
        var self = this;

        self.getData(self.page, hasSelectedDatas, selectedDatas, false);

        /* Add event to infinite scroll */
        self.elem.on("scroll", function (e) {
            if (self.elem.scrollTop() + self.elem.height() > self.elem.height() * self.page) {
                self.page ++;
                self.getData(self.page, hasSelectedDatas, selectedDatas, false);
            }
        });

        if (hasSelectedDatas) {
            self.renderDatas(selectedDatas, true);
        }
    },

    /**
     * Display Datas
     * @param selectedDatas
     * @param isChecked
     */
    renderDatas: function(selectedDatas, isChecked) {
        var self = this,
            attrChecked = '';

            if (isChecked) {
                attrChecked = 'checked';
            }

            jQuery.each(selectedDatas, function(i, selectedValue) {
                self.elem.append(
                    '<div class="ms-elem">' +
                    '<input type="checkbox" ' + attrChecked + ' id="elem_' + selectedValue.id
                    + '" value="' + selectedValue.id + '" name="' + self.id + '[]" />' +
                    '<label for="elem_' + selectedValue.id + '"class="checkbox-label">' + selectedValue.text + '</label>' +
                    '</div>'
                );
            });
        },

    /**
     * Get datas by ajax call
     * @param nb_page
     * @param hasSelectedDatas
     * @param selectedDatas
     * @param isChecked
     */
    getData: function(nb_page, hasSelectedDatas, selectedDatas, isChecked) {
        var self = this,
            renderedDatas = [],
            total;

        if (self.settings.multiSelect.hasOwnProperty('ajax')) {

            var url = self.ajax.url + '&page_limit=' + self.settings.pageLimit + '&page=' + nb_page;

            jQuery.ajax({
                url: url,
                type : 'GET',
                dataType: 'json',
                contentType: 'application/json',
                success: function(data) {
                    if(data != null) {
                        total = Math.round(Math.ceil(data.total / self.settings.pageLimit)); 
                        if (nb_page <= total) {
                            if (hasSelectedDatas) {
                                jQuery.each(selectedDatas, function(i, selectedValue) {
                                    jQuery.each(data.items, function(idx,value) {
                                        if (value.text !== selectedValue.text) {
                                            renderedDatas.push({
                                                id: value.id,
                                                text: value.text
                                            });
                                            console.log(renderedDatas[i].text);
                                        } else {
                                            //console.log('yes');
                                        }
                                    });
                                    //console.log(renderedDatas[i].text);
                                });

                            } else {
                                //console.log(renderedDatas[i].text);
                                renderedDatas = data.items;
                            }

                            self.renderDatas(renderedDatas, isChecked);
                        }
                    }
                }
            });
        }
    },

    /**
     * Init nice scroll
     * @param elem
     */
    initNiceScroll: function(elem) {
        elem.niceScroll({
            cursoropacitymin: 0.2,
            railpadding: {right: 10}
        });
    }
};

(function(jQuery) {
    jQuery.fn.centreonMultiSelect2 = function(options) {

        var settings = jQuery.extend({}, jQuery.fn.centreonMultiSelect2.defaults, options);

        this.each(function () {
            var self = jQuery(this);
            that = new mutliSelect(settings, self);
        });

        return this;
    };
})(jQuery);

/**
 * defaults options
 */
jQuery.fn.centreonMultiSelect2.defaults = {
    url: null,
    pageLimit: 10,
    multiSelect: {}
};