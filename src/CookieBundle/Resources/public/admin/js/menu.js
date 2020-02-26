pimcore.registerNS("pimcore.plugin.manbuvCookieBundle");

pimcore.plugin.manbuvCookieBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.manbuvCookieBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
        this.navEl = Ext.get('pimcore_menu_search').insertSibling('<li id="pimcore_menu_dataprivacy" data-menu-tooltip="Datenschutz" class="pimcore_menu_item pimcore_menu_needs_children"><img src="/bundles/pimcoreadmin/img/flat-white-icons/keys.svg"></li>', 'after');
        this.menu = new Ext.menu.Menu({
            items: [],
        });
        pimcore.layout.toolbar.prototype.dataprivacyMenu = this.menu;
    },

    pimcoreReady: function (params, broker) {
        var toolbar = pimcore.globalmanager.get("layout_toolbar");
        this.navEl.on("click", function () {
            try {
                pimcore.globalmanager.get('cookiebundle_settings').activate();
            }
            catch (e) {
                pimcore.globalmanager.add('cookiebundle_settings', new cookiebundle.settings());
            }
        })
    }

});

var manbuvCookieBundleBundlePlugin = new pimcore.plugin.manbuvCookieBundle();