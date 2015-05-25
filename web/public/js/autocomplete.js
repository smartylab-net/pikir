$(function () {
    $("#search_search").autocomplete({
        source: Routing.generate("search_auto"),
        minLength: 2,
        focus: function (event, ui) {
            $("#search_search").val(ui.item.label);
            return false;
        },
        select: function (event, ui) {
            window.location = Routing.generate("info_company_homepage", {"slug": ui.item.value});
            return false;
        }
    });
});