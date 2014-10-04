function rewriteItemTitles(blockid) {
    forEach(
        getElementsByTagAndClassName('a', 'item-title', 'itemtable_' + blockid),
        function(element) {
            disconnectAll(element);
            connect(element, 'onclick', function(e) {
                e.stop();
                var description = getFirstElementByTagAndClassName('div', 'item-desc', element.parentNode);
                toggleElementClass('hidden', description);
            });
        }
    );
}
function ItemPager(blockid) {
    var self = this;
    paginatorProxy.addObserver(self);
    connect(self, 'pagechanged', partial(rewriteItemTitles, blockid));
}

var itemPagers = [];

function initNewChecklistBlock(blockid) {
    if ($('checklist_page_container_' + blockid)) {
        new Paginator('block' + blockid + '_pagination', 'itemtable_' + blockid, null, 'artefact/checklist/viewitems.json.php', null);
        itemPagers.push(new ItemPager(blockid));
    }
    rewriteItemTitles(blockid);
}
