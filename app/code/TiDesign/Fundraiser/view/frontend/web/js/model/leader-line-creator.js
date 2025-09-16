define(['LeaderLine'], function () {
    'use strict';

    return {
        createAreaAnchor: function (hoverSelector, targetSelector) {
            return this._createAnchor(
                hoverSelector,
                LeaderLine.areaAnchor(document.querySelector(targetSelector), {color: 'red', dropShadow: true})
            );
        },
        createPointAnchor: function (hoverSelector, targetSelector) {
            return this._createAnchor(hoverSelector, LeaderLine.pointAnchor(document.querySelector(targetSelector)));
        },
        createDefaultAnchor: function (hoverSelector, targetSelector) {
            return this._createAnchor(hoverSelector, document.querySelector(targetSelector));
        },
        _createAnchor: function (hoverSelector, targetElement) {
            return new LeaderLine(
                LeaderLine.mouseHoverAnchor(document.querySelector(hoverSelector)),
                targetElement,
                {dropShadow: true}
            );
        }
    }
});
