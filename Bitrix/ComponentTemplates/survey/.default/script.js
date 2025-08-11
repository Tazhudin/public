function JSSurveySwitcher(formContainerId, nextBtnSelector, backBtnSelector, data, directionData, surveyComponentName, surveyAjaxRequestHandlerName) {
    let form = document.getElementById(formContainerId),
        currentGroupIndex = document.querySelector('[data-type="currentGroupIndex"]'),
        errorBlock = form.querySelector('[data-type="error"]'),
        groups = data,
        direction = directionData,
        componentParams;

    let curr = direction[0];

    this.init = function (componentParams = null) {

        this.componentParams = componentParams || {};

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            return false;
        });

        form.querySelector(nextBtnSelector).addEventListener('click', function (event) {
            event.preventDefault();
            let next = null;
            for (let i in direction) {
                if (direction[i] === curr) {
                    if (parseInt(i) === direction.length - 1) {
                        submitData();
                        return;
                    } else {
                        next = direction[parseInt(i) + 1];
                        break;
                    }
                }
            }
            changeGroup(next, next === direction[direction.length - 1]);
            return false;
        });

        form.querySelector(backBtnSelector).addEventListener('click', function (event) {
            let prev = null;
            for (let i in direction) {
                if (direction[parseInt(i)] === curr) {
                    if (parseInt(i) === 0) {
                        return;
                    }
                    break;
                }
                prev = direction[parseInt(i)];
            }
            if (prev === null) {
                return;
            }
            changeGroup(prev);
            return false;
        });
    }

    let submitData = () => {
        let questionsCount = 0,
            answersCount = 0,
            inputName = '';

        for (key in data){
            for (questionId in data[key]['QUESTIONS']){
                questionsCount++;
            }
        }

        $(form).serializeArray().forEach(function (input) {
            if (input.value != '' && inputName != input['name']) {
                inputName = input['name'];
                answersCount++;
            }
        })

        if (questionsCount === answersCount - 1) {
            let csrfToken = form.querySelector('[data-type="csrf_token"]').value,
                formData = $(form).serialize(),
                ajaxParams = {
                    'formData': formData,
                    'csrfToken': csrfToken,
                    'componentParams': this.componentParams
                };
            BX.ajax.runComponentAction(surveyComponentName, surveyAjaxRequestHandlerName, {
                mode: 'ajax',
                dataType: 'json',
                data: ajaxParams
            }).then((response) => {
                document.querySelector('.modal-form-survey__head').innerHTML = '';
                let surveyNod = form.parentNode;
                surveyNod.removeChild(form);
                surveyNod.innerHTML = response.data.message;
            }).catch((response) => {
                let error = response.errors[0];
                errorBlock.innerHTML = error.message;
                errorBlock.setAttribute('style', 'display:block')
            });

        } else {
            errorBlock.innerHTML = 'Вы ответили не на все вопросы!';
            errorBlock.setAttribute('style', 'display:block')
        }
    }

    function changeGroup(groupId, isLast = false) {
        if (errorBlock.innerHTML !== '') {
            errorBlock.innerHTML = '';
            errorBlock.setAttribute('style', 'display:none');
        }

        if (isLast) {
            form.querySelector('.surveyBtnNextSubmit__js').innerHTML = 'Отправить';
        } else {
            form.querySelector('.surveyBtnNextSubmit__js').innerHTML = 'Продолжить';
        }
        curr = groupId;

        currentGroupIndex.innerHTML = direction.indexOf(curr) + 1 ;

        form.querySelector('[data-type="group-name"]').innerHTML = groups[groupId]['NAME'];
        form.querySelectorAll('[data-type="group"]').forEach(el => {
            el.style = 'display:none';
        });
        form.querySelectorAll('[data-type="group"][data-group-id="' + groupId + '"]').forEach(el => {
            el.style = '';
        });
    }

}