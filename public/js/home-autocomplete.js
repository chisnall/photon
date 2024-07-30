$(function(){
    var availableTests = [
        "response.valid",
        "response.code",
        "response.scheme",
        "response.time.less.than",
        "response.time.greater.than",
        "headers.count.equals",
        "headers.header.present",
        "headers.header.equals",
        "headers.header.contains",
        "json.valid",
        "json.key.present",
        "json.key.not.present",
        "json.key.equals",
        "json.key.not.equals",
        "json.key.contains",
        "json.key.not.contains",
        "json.key.is.string",
        "json.key.is.number",
        "json.key.is.integer",
        "json.key.is.float",
        "json.key.is.boolean",
        "json.key.is.null",
        "json.key.is.array",
        "json.key.is.object",
        "json.records.count.equals",
        "json.records.count.less.than",
        "json.records.count.greater.than"
    ];

    $( "form#testCreate [name='testType'], form#testUpdate [name='testType']" ).autocomplete({
        source: availableTests
    });
});
