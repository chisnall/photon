"use strict";

$(window).on("load", function() {
    let container = $("div#json-output");
    let options = { indent:settings_json_indent, lineNumbers: true, trailingCommas: settings_json_trailingCommas, quoteKeys: settings_json_quoteKeys, linkUrls: settings_json_linkUrls, linksNewTab: true };
    $(container).html(prettyPrintJson.toHtml(responseBodyEncoded, options));
});
