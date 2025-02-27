$(function () {
    "use strict";

    var dtDevicesTable = $(".devices-table");

    if (dtDevicesTable.length) {
        dtDevicesTable.DataTable({
            ajax: "/devices",
            columns: [
                { data: "id", visible: false },
                { data: "name" },
                { 
                    data: "versions",
                    render: function (data, type, full, meta) {
                        if (data.length > 0) {
                            return data.map(v => `<span class="badge badge-primary">${v.version}</span>`).join(" ");
                        }
                        return "-";
                    }
                },
                { 
                    data: "versions",
                    render: function (data, type, full, meta) {
                        let colors = [];
                        data.forEach(version => {
                            version.colors.forEach(color => {
                                colors.push(`<span class="badge badge-secondary">${color.color_name}</span>`);
                            });
                        });
                        return colors.length > 0 ? colors.join(" ") : "-";
                    }
                },
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
                            exportOptions: { columns: [1, 2, 3, 4] },
                        },
                        {
                            extend: "csv",
                            text:
                                feather.icons["file-text"].toSvg({
                                    class: "font-small-4 mr-50",
                                }) + "CSV",
                            className: "dropdown-item",
                            exportOptions: { columns: [1, 2, 3, 4] },
                        },
                        {
                            extend: "excel",
                            text:
                                feather.icons["file"].toSvg({ class: "font-small-4 mr-50" }) +
                                "Excel",
                            className: "dropdown-item",
                            exportOptions: { columns: [1, 2, 3, 4] },
                        },
                        {
                            extend: "pdf",
                            text:
                                feather.icons["clipboard"].toSvg({
                                    class: "font-small-4 mr-50",
                                }) + "PDF",
                            className: "dropdown-item",
                            exportOptions: { columns: [1, 2, 3, 4] },
                        },
                        {
                            extend: "copy",
                            text:
                                feather.icons["copy"].toSvg({ class: "font-small-4 mr-50" }) +
                                "Copy",
                            className: "dropdown-item",
                            exportOptions: { columns: [1, 2, 3, 4] },
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
                searchPlaceholder: "Search Devices...",
            },
        });
    }
});
