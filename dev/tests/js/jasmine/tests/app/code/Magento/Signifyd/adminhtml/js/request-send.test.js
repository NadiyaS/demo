/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'mageUtils',
    'Magento_Signifyd/js/request-send'
], function (utils, RequestButton) {
    'use strict';

    describe('Check Signifyd UI component button', function () {
        var button,
            requestURL = '/url',
            requestData = {
                'order_id': 1
            };

        beforeEach(function () {
            button = new RequestButton({
                requestURL: requestURL,
                data: requestData
            });

            spyOn(utils, 'submit');
            button.sendRequest();
        });

        it('checks if method sendRequest exists', function () {
            expect(button.sendRequest).toBeDefined();
            expect(typeof button.sendRequest).toBe('function');
        });

        it('checks if request submited', function () {
            expect(utils.submit).toHaveBeenCalledWith(
                {
                    url: requestURL,
                    data: requestData
                }
            );
        });
    });
});
