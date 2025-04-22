$(function () {
    "use strict";

    var dtDeviceRequestsTable = $(".device-requests-table");

    if (dtDeviceRequestsTable.length) {
        var url = "/device-requests";

        dtDeviceRequestsTable.DataTable({
            ajax: url,
            scrollX: true,
            columns: [
                { data: "name" },
                { data: "email" },
                { data: "phone_number" },
                { data: "device.name" },
                { data: "device_version.version" },
                { data: "primary_color.color_name" },
                { data: "secondary_color.color_name" },
                { data: "shipping_address.address" },
                { data: "shipping_attention", defaultContent: "-" },
                { data: "caller_id_requested", defaultContent: "-" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    responsivePriority: 1,
                    render: function (data, type, full, meta) {
                        var name = full["name"];

                        var row_output =
                            '<div class="d-flex justify-content-left align-items-center">' +
                            '<div class="d-flex flex-column">' +
                            '<b>' + name + '</b>' +
                            '</div></div>';

                        return row_output;
                    },
                },
                {
                    targets: 1,
                    visible: true,
                },
                {
                    targets: 5,
                    render: function (data, type, full, meta) {
                        return `<span class="badge badge-primary">${data || "-"}</span>`;
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, full, meta) {
                        return `<span class="badge badge-secondary">${data || "-"}</span>`;
                    },
                },
                
            ],
            order: [[3, "asc"]],
            dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-right"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
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
                            exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10] },
                        },
                        {
                            extend: "csv",
                            text:
                                feather.icons["file-text"].toSvg({
                                    class: "font-small-4 mr-50",
                                }) + "CSV",
                            className: "dropdown-item",
                            exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10] },
                        },
                        {
                            extend: "excel",
                            text:
                                feather.icons["file"].toSvg({ class: "font-small-4 mr-50" }) +
                                "Excel",
                            className: "dropdown-item",
                            exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10] },
                        },
                        {
                            extend: "pdf",
                            text:
                                feather.icons["clipboard"].toSvg({
                                    class: "font-small-4 mr-50",
                                }) + "PDF",
                            className: "dropdown-item",
                            exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10] },
                        },
                        {
                            extend: "copy",
                            text:
                                feather.icons["copy"].toSvg({ class: "font-small-4 mr-50" }) +
                                "Copy",
                            className: "dropdown-item",
                            exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10] },
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
                searchPlaceholder: "Search Device Requests...",
            },
            initComplete: function () {
                $("div.device-head-label").html('<div class="d-flex justify-content-start"><img src="/images/icons/device_request.png" height="30" width="30" alt="device request"><h5 class="pl-1 pt-1"><b>Device Requests</b></h5></div>');
            },
        });
    }

    $(document).ready(function () {});

    $("body").tooltip({
        selector: '[data-toggle="tooltip"]',
        container: "body",
    });
});