function JSBrandSectionFilterSwitcher(componentName, ajaxRequestHandlerName) {
    this.init = function () {
        let brandSectionsNodList = document.querySelectorAll('[data-brand="section"]');

        brandSectionsNodList.forEach(function (el) {
            el.addEventListener('click', function (event) {
                let sectionNodsParent = el.parentNode,
                    activeSection = sectionNodsParent.querySelector('.bem-link_red');

                activeSection.classList.remove('bem-link_red');
                el.classList.add('bem-link_red');

                // перезагрузка страницы временная мера, пока фронт не решит обновление скриптов,
                // после все будет по аякс.
                window.location.href = window.location.pathname + '?SECTION_ID=' + el.dataset.brandSectionId;

                //getSectionBrands(el);
            })
        })
    }

    function getSectionBrands(el) {
        let brandsNod = document.querySelector('[data-brands="brands"]'),
            sectionId = el.dataset.brandSectionId;

        brandsNod.innerHTML = '';

        BX.ajax.runComponentAction(componentName, ajaxRequestHandlerName, {
            mode: 'ajax',
            dataType: 'json',
            data: {sectionId}
        }).then((response) => {
            console.log(response);
            brandsNod.innerHTML = response.data.html;
        }).catch((response) => {
            console.log(response.errors[0]);
        });
    }
}


