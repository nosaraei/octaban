import * as Vue from 'vue';
import BackendAjaxHandler from './ajax/Handler';
import BackendDateTimeControl from './ui/DateTimeControl';
import BackendUiEventHandler from './ui/EventHandler';
import BackendUiWidgetHandler from './ui/WidgetHandler';

if (window.Snowboard === undefined) {
    throw new Error('Snowboard must be loaded in order to use the Backend UI.');
}

((Snowboard) => {
    // Add necessary plugins
    Snowboard.addPlugin('backend.ajax.handler', BackendAjaxHandler);
    Snowboard.addPlugin('backend.ui.eventHandler', BackendUiEventHandler);
    Snowboard.addPlugin('backend.ui.widgetHandler', BackendUiWidgetHandler);

    // Add the pre-filter immediately
    Snowboard['backend.ajax.handler']().addPrefilter();

    // Global controls
    Snowboard.addPlugin('backend.ui.dateTimeControl', BackendDateTimeControl);
    Snowboard['backend.ui.widgetHandler']().register('datetime', 'backend.ui.dateTimeControl');

    // Add polyfill for AssetManager
    window.AssetManager = {
        load: (assets, callback) => {
            Snowboard.assetLoader().load(assets).then(
                () => {
                    if (callback && typeof callback === 'function') {
                        callback();
                    }
                },
            );
        },
    };
    window.assetManager = window.AssetManager;
})(window.Snowboard);

// Add Vue to global scope
window.Vue = Vue;
