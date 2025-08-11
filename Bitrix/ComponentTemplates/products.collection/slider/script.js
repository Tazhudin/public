function JSCollectionSwitcher(componentName, AjaxRequestHandlerName, collectionTypes, componentParams) {
    this.init = function (componentParams = null) {
        // определение необходимых переменных
        let sliderNOD = document.querySelector('div[data-slider="slider-with-tabs"]'),
            sliderTabs = sliderNOD.querySelectorAll('div[data-tabs="tabs"] [data-bx-js="collection-type"]'),
            mobileTabs = sliderNOD.querySelector('div[data-bx-js="collection-mobileTabs"] select'),
            sliderFilter =  sliderNOD.querySelector('[data-slider-filter="select"] select');
        var currentTubSliderFilter = [];
        for (collectionType in collectionTypes) {
            currentTubSliderFilter[collectionTypes[collectionType]['DATA_ATTR']] = 'all';
        }
        // установка событий
        sliderTabs.forEach( function(tab, i, sliderTabs) {
            sliderTabs[i].addEventListener('click', () => getSlider(tab), false );
        });
        mobileTabs.addEventListener('change', () => getSlider(mobileTabs.options[mobileTabs.selectedIndex]), false );
        if (sliderFilter) {
            sliderFilter.addEventListener('change', () => getSlider(), false );
        }
        // оброботчик
        function getSlider(checkedTub = null) {
            if (checkedTub === null) {
                checkedTub = sliderNOD.querySelector('.underline-tabs__tab_active');
            }
            let checkedSliderType = checkedTub.getAttribute("data-tab"),
                sliderContentBlock =  sliderNOD.querySelector('div.row-slider__content[data-tab="'+ checkedSliderType +'"]');
            if (sliderFilter && currentTubSliderFilter[checkedSliderType] !== sliderFilter.options[sliderFilter.selectedIndex].value) {
                currentTubSliderFilter[checkedSliderType] = sliderFilter.options[sliderFilter.selectedIndex].value
                sliderContentBlock.innerHTML = '';
            } else if (sliderContentBlock.innerHTML.trim() !== '') {
                return;
            }
            componentParams.SECTION_ID = currentTubSliderFilter[checkedSliderType];
            for (collectionType in collectionTypes) {
                if (checkedSliderType ===  collectionTypes[collectionType]['DATA_ATTR']) {
                    componentParams.GET_COLLECTION_TYPE = collectionType;
                    break;
                }
            }
            let lastActiveContentBlocks = sliderNOD.querySelectorAll('.row-slider__content_active');

            if (lastActiveContentBlocks.length) {
                for (let index in lastActiveContentBlocks) {
                   $(lastActiveContentBlocks.item(index)).removeClass('row-slider__content_active');
                }
            }

            BX.ajax.runComponentAction(componentName, AjaxRequestHandlerName, {
                mode: 'ajax',
                dataType: 'json',
                data: {
                    'ajaxParams': componentParams,
                }
            }).then((response) => {
                if (response.status === 'success') {
                    sliderContentBlock.classList.add('row-slider__content_active');
                    sliderContentBlock.innerHTML = response.data.HTML;

                    new window.shared.Siema(sliderContentBlock, {
                        selector: '.row-slider__slider',
                        slideWidth: '.row-slider__product',
                        wrapperStyle: {
                            display: 'block'
                        },
                        arrows: true,
                        arrowSlideThrough: 3,
                        classes: {
                            prev: 'row-slider__arrow_left',
                            next: 'row-slider__arrow_right'
                        }
                    })
                } else {
                    console.log(response.errors[0]);
                }
            }).catch((response) => {
                console.log(response);
            });
        }
    }
}