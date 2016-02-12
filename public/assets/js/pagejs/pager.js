var Pager = function () {
    var self = this;
	var initialValue=1;
    self.iTotalDisplayRecords = ko.observable();
    self.iPageSize = ko.observable(10);
    self.currentPage = ko.observable(1);
    self.iTotalRecords = ko.observable();
    self.getDataCallback = function () {
        alert('Please Override getDataCallback');
    };

    self.sort = ko.observable();
    self.sort.extend({ notify: 'always' });
    self.sortDirection = ko.observable('ASC');

    self.isSearch = ko.observable(false);
    self.searchBy = ko.observable('');
    self.isActive = ko.observable(1);
    self.searchText = ko.observable().extend({ throttle: 500 }); ;
    self.displayTotalPages = ko.observable(5);

    self.pageSizeOptions = ko.observableArray([5, 10, 15, 20, 25]);
    self.selectedPageSize = ko.observable(self.iPageSize());
    self.selectedPageSize.subscribe(function (newSize) {
        self.iPageSize(newSize);
        self.currentPage(1);

        if (self.getDataCallback != undefined)
            self.getDataCallback();
    });
    
    self.currentSort = ko.observable();
    self.sortModel = function () {
        var stModel = this;
        stModel.sort = ko.observable();
        stModel.sortDirection = ko.observable('ASC');
        stModel.isDesending = ko.observable(false);
        stModel.sort.extend({ notify: 'always' });
        stModel.sort.subscribe(function (newval) {
            self.sort(newval);
            self.currentSort(stModel);
            if (stModel.sortDirection() == 'ASC') {
                stModel.sortDirection('DESC');
                self.sortDirection('DESC');
                stModel.isDesending(true);
            } else {
                stModel.sortDirection('ASC');
                self.sortDirection('ASC');
                stModel.isDesending(false);
            }
            self.getDataCallback();
        });
        return stModel;
    };

    self.search = function () {
        self.isSearch(true);
        self.currentPage(1);
        self.getDataCallback();

    };

    self.clearSearch = function () {
        self.searchBy('');
        self.isActive(1);
        self.searchText('');
        self.isSearch(false);
        self.getDataCallback();
    };

    self.pagesToShow = ko.observableArray();
    self.allPages = ko.dependentObservable(function () {
        var pages = [];
        var pagesToShow = pages;

        for (var i = 1; i <= Math.ceil(self.iTotalRecords() / self.iPageSize()); i++) {
            pages.push({ pageNumber: (i) });
        }
        if (pages.length > self.displayTotalPages()) {
            //if (self.currentPage() > Math.ceil(self.displayTotalPages() / 2)) {
            //    var start = (self.currentPage() - Math.floor(self.displayTotalPages() / 2));
            //    var end = start + self.displayTotalPages();
            //    pagesToShow = pages.slice(start, end);
            //} else {
            //    var start = 0;
            //    var end = start + self.displayTotalPages();
            //    pagesToShow = pages.slice(start, end);
            //}
            var count = Math.ceil(self.currentPage() / self.displayTotalPages());
            var start = (count - 1) * self.displayTotalPages();
            var end = start + self.displayTotalPages();
            pagesToShow = pages.slice(start, end);
        }

        self.pagesToShow(pagesToShow);
        return pages;
    });

    self.previousPage = function () {
        if (self.currentPage() > 1) {
            self.moveToPage(self.currentPage() - 1);
        }
    };

    self.nextPage = function () {
        if (self.currentPage() < self.allPages().length) {
            self.moveToPage(self.currentPage() + 1);
        }
    };

    self.gotoPage = function (e) {
        if (e.pageNumber != self.currentPage()) {
            self.moveToPage(e.pageNumber);
        }
    };

    self.moveToPage = function (index) {
        self.currentPage(index);
        self.getDataCallback();
    };
	
	 self.iTotalDisplayRecords.subscribe(function (newValue) {
        if (newValue == 0 && self.currentPage() > initialValue) {
            self.moveToPage(self.currentPage() - initialValue);
        }
    });
    
    self.FirstItemIndex = ko.computed(function () {
        return self.iPageSize() * (self.currentPage()-1) + 1;
    });

    self.LastItemIndex = ko.computed(function () {
        return Math.min(self.FirstItemIndex() + self.iPageSize() - 1, self.iTotalRecords());
    });
    
    //    self.PageCountMessage = ko.computed(function () {
    //        var from = (3 * (self.currentPage() - 1)) + 1;
    //        var to = from + self.iTotalDisplayRecords() - 1;
    //        if (self.iTotalDisplayRecords()) {
    //            return 'Showing ' + from + ' to ' + to + ' of ' + self.iTotalRecords() + ' entries';
    //        }
    //    });

    return self;
};