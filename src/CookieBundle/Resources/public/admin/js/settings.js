pimcore.registerNS("cookiebundle.settings");

cookiebundle.settings = Class.create({

    initialize: function () {
        this.getData();
    },

    getData: function () {
        Ext.Ajax.request({
            url: '/admin/cbcookie/get-settings',
            success: function (response) {

                this.values = Ext.decode(response.responseText).values;
                this.config = Ext.decode(response.responseText).config;

                this.getTabPanel();

            }.bind(this)
        });
    },


    getTabPanel: function () {

        var values = this.values;
        //var config = this.config;
        var service = values.service;


        if (!this.panel) {

            this.panel = Ext.create('Ext.panel.Panel', {
                //id: "pimcore_settings_system",
                id: "cookiebundle_settings",
                title: t("cookiebundle_settings"),
                iconCls: "pimcore_icon_system",
                border: false,
                layout: "fit",
                closable: true
            });


            this.panel.on('destroy', function () {
                pimcore.globalmanager.remove('cookiebundle_settings');
            }.bind(this));

            function dividor() {
                return {
                    xtype: 'box',
                    hidden: false,
                    autoEl: {
                        tag: 'hr'
                    }
                }
            }

            this.layout = Ext.create('Ext.form.Panel', {
                bodyStyle: 'padding:20px 20px 20px 20px;',
                border: false,
                autoScroll: true,
                forceLayout: true,
                defaults: {
                    forceLayout: true
                },
                fieldDefaults: {
                    labelWidth: 250
                },
                buttons: [
                    {
                        text: t("save"),
                        handler: this.save.bind(this),
                        iconCls: "pimcore_icon_apply"
                    }
                ],
                items: [
                    {
                        fieldLabel: ('Google Analytics (gtag.js)'),
                        xtype: "checkbox",
                        name: "service.gtag",
                        checked: service.gtag
                    },
                    {
                        fieldLabel: ("UA (Tracking-ID)"),
                        xtype: "textfield",
                        name: "service.gtagUa",
                        value: service.gtagUa,
                        width: 600
                    },
                    dividor(),
                    {
                        fieldLabel: t('Google Maps'),
                        xtype: "checkbox",
                        name: "service.googleMaps",
                        checked: service.googleMaps
                    },
                    {
                        fieldLabel: t("Google Maps Apikey"),
                        xtype: "textfield",
                        name: "service.googleMapsApiKey",
                        value: service.googleMapsApiKey,
                        width: 600
                    },
                    dividor(),

                    {
                        fieldLabel: t('Facebook-Pixel'),
                        xtype: "checkbox",
                        name: "service.facebookPixel",
                        checked: service.facebookPixel
                    },
                    {
                        fieldLabel: t("Facebook-Pixel-ID"),
                        xtype: "textfield",
                        name: "service.facebookPixelId",
                        value: service.facebookPixelId,
                        width: 600
                    },

                    dividor(),
                    {
                        fieldLabel: t('Youtube (iFrame)'),
                        xtype: "checkbox",
                        name: "service.youtube",
                        value: service.youtube,
                    },
                    dividor(),
                    {
                        fieldLabel: t('Vimeo (iFrame)'),
                        xtype: "checkbox",
                        name: "service.vimeo",
                        value: service.vimeo,
                    },
                    dividor(),
                    {
                        fieldLabel: t('Webcontent (iFrames)'),
                        xtype: "checkbox",
                        name: "service.webContent",
                        value: service.webContent,
                    },
                    dividor(),
                    {
                        fieldLabel: ('recaptcha (v2 - Google)'),
                        xtype: "checkbox",
                        name: "service.recaptcha",
                        value: service.recaptcha,
                    },
                    dividor(),
                ]
            });

            this.panel.add(this.layout);

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem(this.panel);

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    minify: function () {
        alert('minify');
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem('papillotool_settings');
    },


    save: function () {
        var values = this.layout.getForm().getFieldValues();

        Ext.Ajax.request({
            url: '/admin/cbcookie/settings/save',
            method: "PUT",
            params: {
                data: Ext.encode(values)
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t('success'), t('cbCookie_settings_save_success'), 'success');

                    } else {
                        pimcore.helpers.showNotification(t('error'), t('papillotool_settings_save_error'),
                            'error', t(res.message));
                    }
                } catch (e) {
                    pimcore.helpers.showNotification(t('error'), t('papillotool_settings_save_error'), 'error');
                }
            }
        });
    }

});