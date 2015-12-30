{if $tags}<p class="tags s"><label>{str tag=tags}:</label> {list_tags owner=$owner tags=$tags}</p>{/if}
<table id="itemtable">
    <thead>
        <tr>
            <th class="c1">{str tag='code' section='artefact.checklist'}</th>
            <th class="c2">{str tag='title' section='artefact.checklist'}</th>
            <th class="c3">{str tag='scale' section='artefact.checklist'}</th>
        </tr>
    </thead>
    <tbody>
    {$items.tablerows|safe}
    </tbody>
</table>
<div id="checklist_page_container">{$items.pagination|safe}</div>
{if $license}
<div class="resumelicense">
{$license|safe}
</div>
{/if}
<script>
{literal}
function rewriteItemTitles() {
    forEach(
        getElementsByTagAndClassName('a', 'item-title','itemtable'),
        function(element) {
            connect(element, 'onclick', function(e) {
                e.stop();
                var description = getFirstElementByTagAndClassName('div', 'item-desc', element.parentNode);
                toggleElementClass('hidden', description);
            });
        }
    );
}

addLoadEvent(function() {
    {/literal}{$items.pagination_js|safe}{literal}
    removeElementClass('checklist_page_container', 'hidden');
});

function ItemPager() {
    var self = this;
    paginatorProxy.addObserver(self);
    connect(self, 'pagechanged', rewriteItemTitles);
}
var itemPager = new ItemPager();
addLoadEvent(rewriteItemTitles);
{/literal}
</script>
