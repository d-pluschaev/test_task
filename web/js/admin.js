// startup
window.onload = function () {

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        window.appState.change(getViewByTabName(target));
    });

    window.appState = new AppState();
    window.appState.change(getViewByTabName("#search"));
};

function getViewByTabName(tabName) {
    return {
        "#import": ViewImport,
        "#search": ViewSearch
    }[tabName];
}

// controller
class AppState {
    change(viewClass) {
        var currentView = this.currentView;
        if (currentView) {
            currentView.detach();
        }

        currentView = new viewClass();
        currentView.init();
        this.currentView = currentView;
    }

    get currentView() {
        return this._currentView;
    }

    set currentView(view) {
        this._currentView = view;
    }
}

// views
class ViewImport {
    constructor() {
        this.elFileInput = $("#file-input");
        this.elFileInputMsg = $("#file-input-msg");
        this.uploadFileUrl = "import/csv_upload";

        this.elFileInputMsg.html("Choose a CSV file");
    }

    detach() {
        this.elFileInput.off("change");
    }

    init() {
        this.initEvents();
    }

    initEvents() {
        this.elFileInput.on("change", this.uploadFile.bind(this));
    }

    uploadFile(fileInput) {
        this.loadingModeEnable(true, "Importing... <i class='loader'></i>");
        AjaxAdapter.uploadFile(this.uploadFileUrl, this.elFileInput[0].files[0]).then(
            (function (response) {
                var msg = response;
                if (typeof response == "object") {
                    if (msg.error) {
                        msg = new Error("[File upload error] " + response.message);
                    } else {
                        msg = response.message;
                    }
                } else {
                    msg = new Error("[File upload error] " + response);
                }
                this.loadingModeEnable(false, msg);
            }).bind(this),
            (function (error) {
                this.loadingModeEnable(false, new Error("[File upload error] " + error.message));
            }).bind(this)
        );
    }

    loadingModeEnable(flag, text) {
        if (text instanceof Error) {
            text = "<b style='color:red'>" + text.message + "</b>";
        }
        this.elFileInputMsg.html(text);
        this.elFileInput.prop('disabled', flag);
    }
}

class ViewSearch {
    constructor() {
        this.elAgeDropdown = $("#dd-age");
        this.elSearchForm = $("#search-form");
        this.elSearchResultLabel = $("#search-result-label");
        this.elSearchSubmitButton = $("#search-submit");
        this.elSearchLoader = $("#search-loader");
        this.elSearchResults = $("#search-results");
        this.elSearchResultTemplate = $("#search-result-row-template");
        this.elPaginators = $('#pagination-top, #pagination-bottom');

        this.page = 1;
        this.requestInProgress = false;
    }

    detach() {
        this.elAgeDropdown.off("click");
        this.elSearchForm.off("submit");
    }

    init() {
        this.initEvents();
    }

    initEvents() {
        this.elAgeDropdown.on("click", function (el) {
            if (el.target.tagName == "A") {
                var li = el.target.parentNode;
                var selectedOption = parseInt($(li).attr('data-value'), 10);
                if (isNaN(selectedOption)) {
                    selectedOption = "";
                }
                $("#dd-age-val").val(selectedOption.toString());
                $("#dd-age-disp").html(selectedOption === "" ? "-" : selectedOption.toString());

            }
        });

        jQuery(function ($) {
            $("#inputPhone").mask("+999 (99) 999-99-99", {autoclear: false});
        });

        this.elSearchForm.on("submit", this.searchSubmit.bind(this));

        $.fn.datepicker.defaults.autoclose = true;
    }

    searchSubmit() {
        this.page = 1;
        this.searchRun();
        return false;
    }

    searchRun() {
        this.requestInProgress = true;
        var data = {};
        this.elSearchForm.find("input").each(function (i, el) {
            var val = el.value.trim();
            if (val !== "") {
                data[el.name] = val;
            }
        });

        this.searchRequest(data);
    }

    searchRequest(formData) {
        var self = this;
        $.ajax({
            method: "POST",
            url: "search?page=" + this.page,
            data: formData
        })
            .done(function (data) {
                self.loadingModeEnable(false);
                self.onSearchResponse(data);
            })
            .fail(function (msg) {
                self.requestInProgress = false;
                self.loadingModeEnable(false);
                alert("Request error");
            });
        this.loadingModeEnable(true);
    }

    onSearchResponse(data) {
        this.requestInProgress = false;
        if (data && data.count) {
            this.resultsClear();
            this.resultSetCount(data.count, data.limit);
            this.resultRenderPagination(data.count, data.limit, data.offset);
            this.resultSetResults(data);
        }
    }

    resultSetCount(count, limit) {
        if (count == 0) {
            this.elSearchResultLabel.html("No results");
            return;
        }
        if (count < limit) {
            limit = count;
        }
        this.elSearchResultLabel.html("Shown " + limit + " from " + count);
    }

    resultSetResults(data) {
        for (var i = 0; i < data.results.length; i++) {
            this.resultRenderRow(data.results[i], data.terms);
        }
    }

    resultRenderRow(row, terms) {
        var tpl = this.elSearchResultTemplate.html();
        for (var k in row) {
            if (terms[k]) {
                row[k] = this.resultHighlight(row[k], terms[k]);
            }
            tpl = tpl.replace(new RegExp("{" + k + "}", "g"), row[k]);
        }
        this.elSearchResults.append(tpl);
    }

    resultHighlight(val, term) {
        return val.toString().replace(new RegExp(term), "<span class='highlight'>" + term + "</span>");
    }

    resultsClear() {
        this.elSearchResults.empty();
        this.elPaginators.empty();
    }

    resultRenderPagination(count, limit, offset) {
        var self = this;
        this.elPaginators.bootpag({
            total: limit > 0 ? Math.ceil(count / limit) : 0,
            page: limit > 0 ? (offset / limit) + 1 : 1,
            maxVisible: Math.min(limit, count),
            next: 'Next',
            prev: 'Prev'
        }).on('page', function (event, num) {
            if (!self.requestInProgress) {
                self.switchPage(num);
            }
        });
    }

    switchPage(num) {
        this.page = num;
        this.searchRun();
    }

    loadingModeEnable(flag) {
        if (flag) {
            this.elSearchResultLabel.html("Loading...");
        }
        this.elSearchSubmitButton.prop('disabled', flag);
        flag ? this.elSearchLoader.show() : this.elSearchLoader.hide();
    }
}

// Helpers
// don't have time to deal with jquery file sending
class AjaxAdapter {
    /**
     * @param url
     * @param fileObj
     * @returns {Promise}
     */
    static uploadFile(url, fileObj) {
        return new Promise(function (resolve, reject) {
            var xhr = AjaxAdapter._prepareXhr(resolve, reject);
            xhr.open("POST", url, true);

            var fd = new FormData();
            fd.append("file", fileObj);
            xhr.send(fd);
        });
    }

    static _prepareXhr(resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    var res;
                    try {
                        res = JSON.parse(this.response);
                    } catch (e) {
                        res = this.response;
                    }
                    resolve(res);
                } else {
                    var error = new Error(this.statusText);
                    error.code = this.status;
                    reject(error);
                }
            }
        };

        xhr.onerror = function () {
            reject(new Error("Network Error"));
        };

        return xhr;
    }
}
