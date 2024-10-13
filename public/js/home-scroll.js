function kebabToPascalCase(str) {
    return str
        .split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join('');
}

$("#left-content, #upper-content, #lower-content").on('scroll', function() {
    let contentId = $(this).attr('id');
    let contentName = kebabToPascalCase(contentId);
    let scrollTop = $(this).scrollTop();

    localStorage.setItem("scrollPosition" + contentName, scrollTop);
});
