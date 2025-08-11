function JSChangelogSwitcher(releaseRatingBlockClass, releaseComponentName, releaseId, userId, releaseRateAjaxRequestHandlerName) {

    this.init = function () {
        let releaseRateNode = document.querySelector('.' + releaseRatingBlockClass),
            userRate = null;

        releaseRateElements = releaseRateNode.querySelectorAll('[data-rate]'),

            releaseRateElements.forEach(function (el) {
                el.addEventListener('click', function (event) {
                    userRate = el.dataset.rate;
                    sendUserRate(userRate,releaseRateElements, releaseRateNode);
                })
            })
    }

    function sendUserRate(userRate, releaseRateElements, releaseRateNode) {
        ajaxParams = {
            userId,
            userRate,
            releaseId
        };
        BX.ajax.runComponentAction(releaseComponentName, releaseRateAjaxRequestHandlerName, {
            mode: 'ajax',
            dataType: 'json',
            data: ajaxParams
        }).then((response) => {
            let newReleaseRateVal = response.data.newRate,
                newReleaseRateEl = response.data.newRate.toFixed(),
                userRateNodElement = releaseRateNode.querySelector('[data-rate="user-rate"]');

            releaseRateElements.forEach(function (el) {
                if (newReleaseRateEl < el.dataset.rate) {
                    el.classList.remove('bem-product-rating__el_active');
                } else {
                    el.classList.add('bem-product-rating__el_active');
                }

                releaseRateNode.querySelector('.bem-product-rating').classList.remove('bem-product-rating_interactive');
            })
            userRateNodElement.innerHTML = newReleaseRateVal;

        }).catch((response) => {
            console.log(response.errors[0]);
        });
    }


}