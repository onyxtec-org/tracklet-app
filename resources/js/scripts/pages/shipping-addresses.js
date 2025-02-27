$(function () {
    "use strict";

    var dtShippingAddressTable = $(".shipping-address-table");

    if (dtShippingAddressTable.length) {
        dtShippingAddressTable.DataTable({
            ajax: "/shipping-addresses",
            columns: [
                { data: "id", visible: false },
                { data: "address" },
                { 
                    data: "created_at",
                    render: function (data, type, full, meta) {
                        let date = new Date(data);
                        return date.toLocaleString('en-US', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long', 
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        });
                    }
                },
            ],
            order: [[2, "desc"]],
            dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-right"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 100],
            buttons: [
                {
                    extend: "collection",
                    className: "btn btn-outline-secondary dropdown-toggle mr-2",
                    text:
                        feather.icons["share"].toSvg({ class: "font-small-4 mr-50" }) +
                        "Export",
                    buttons: [
                        {
                            extend: "print",
                            text:
                                feather.icons["printer"].toSvg({
                                    class: "font-small-4 mr-50",
                                }) + "Print",
                            className: "dropdown-item",
                            exportOptions: { columns: [1, 2] },
                        },
                        {
                            extend: "csv",
                            text:
                                feather.icons["file-text"].toSvg({
                                    class: "font-small-4 mr-50",
                                }) + "CSV",
                            className: "dropdown-item",
                            exportOptions: { columns: [1, 2] },
                        },
                        {
                            extend: "excel",
                            text:
                                feather.icons["file"].toSvg({ class: "font-small-4 mr-50" }) +
                                "Excel",
                            className: "dropdown-item",
                            exportOptions: { columns: [1, 2] },
                        },
                        {
                            extend: "pdf",
                            text:
                                feather.icons["clipboard"].toSvg({
                                    class: "font-small-4 mr-50",
                                }) + "PDF",
                            className: "dropdown-item",
                            exportOptions: { columns: [1, 2] },
                        },
                        {
                            extend: "copy",
                            text:
                                feather.icons["copy"].toSvg({ class: "font-small-4 mr-50" }) +
                                "Copy",
                            className: "dropdown-item",
                            exportOptions: { columns: [1, 2] },
                        },
                    ],
                    init: function (api, node, config) {
                        $(node).removeClass("btn-secondary");
                        $(node).parent().removeClass("btn-group");
                        setTimeout(function () {
                            $(node)
                                .closest(".dt-buttons")
                                .removeClass("btn-group")
                                .addClass("d-inline-flex");
                        }, 50);
                    },
                },
            ],
            language: {
                paginate: {
                    previous: "&nbsp;",
                    next: "&nbsp;",
                },
                sLengthMenu: "Show _MENU_",
                search: "Search",
                searchPlaceholder: "Search Shipping Addresses...",
            },
        });
    }
});
