//Variable and function used to load assign iqa section
const assigniqabtn = $('#assign_iqa_btn')[0];
const alspan = $('#choose_al_div')[0];
const iqaspan = $('#choose_aiqa_div')[0];
const success = $('#assign_success')[0];
const assignErrorText = $('#assign_error')[0];
const assignDiv = $('#assign_iqa_div')[0];
assigniqabtn.addEventListener('click', ()=>{
    assignErrorText.style.display = 'none';
    if(assignDiv.style.display == 'block'){
        assignDiv.style.display = 'none';
    } else if(assignDiv.style.display == 'none'){
        iqaspan.style.display = 'none';
        alspan.style.display = 'none';
        success.style.display = 'none';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/admin_assign_iqa_render.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    assignErrorText.innerText = text['error'];
                    assignErrorText.style.display = 'block';
                } else {
                    if(text['course']){
                        $('#choose_ac')[0].innerHTML = text['course'];
                    }
                    if(text['learner']){
                        $('#choose_al')[0].innerHTML = text['learner'];
                    }
                    if(text['iqa']){
                        $('#choose_aiqa')[0].innerHTML = text['iqa'];
                    }
                }
            } else {
                assignErrorText.innerText = 'Loading error';
                assignErrorText.style.display = 'block';
            }
        }
        xhr.send();
        assignDiv.style.display = 'block';
    }
});
//Used to have the relevant select options appear for a specified course
function assign_iqa_change(){
    success.style.display = 'none';
    const value = $('#choose_ac')[0].value;
    const options1 = $(`.iqa-learner`);
    options1.each(function(index, item){
        current = $(item)[0];
        if(current.getAttribute('cat') == value){
            current.style.display = 'block';
            current.required = true;
        } else {
            current.style.display = 'none';
            current.required = false;
        }
    });
    alspan.style.display = 'block';
    const options2 = $(`.iqa-iqa`);
    options2.each(function(index, item){
        current = $(item)[0];
        if(current.getAttribute('cat') == value){
            current.style.display = 'block';
            current.required = true;
        } else {
            current.style.display = 'none';
            current.required = false;
        }
    });
    iqaspan.style.display = 'block';
}
//Function used to get the relevant values for each field and submit it
const form = $('#assign_iqa_form')[0];
form.addEventListener('submit', (e)=>{
    e.preventDefault();
    let params = 'c='+$('#choose_ac')[0].value;
    const learner = $(`.iqa-learner`);
    const iqa = $('.iqa-iqa');
    let required = [false, false];
    assignErrorText.style.display = 'none';
    learner.each(function(index, item){
        current = $(item)[0];
        if(current.style.display == 'block' && current.required == true){
            if(current.value != null){
                params += '&l='+current.value;
                required[0] = true;
            }
        }
    });
    iqa.each(function(index, item){
        current = $(item)[0];
        if(current.style.display == 'block' && current.required == true){
            if(current.value != null){
                params += '&i='+current.value;
                required[1] = true;
            }
        }
    });
    if(required.includes(false)){
        if(required[0] == false){
            assignErrorText.innerText = 'No learner selected';
        } else if(required[1] == false){
            assignErrorText.innerText = 'No iqa selected';
        }
        assignErrorText.style.display = 'block';
    } else {
        //Used to submit form data
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/admin_assign_iqa.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    assignErrorText.innerText = text['error'];
                    assignErrorText.style.display = 'block';
                } else if(text['return']){
                    assignDiv.style.display = 'none';
                    assigniqabtn.click();
                    success.innerText = 'Success';
                    success.style.display = 'block';
                } else {
                    assignErrorText.innerText = 'Submit error';
                    assignErrorText.style.display = 'block';
                }
            } else {
                assignErrorText.style.display = 'block';
                assignErrorText.innerText = 'Connection error';
            }
        }
        xhr.send(params);
    }
});
const viewiqabtn = $('#view_iqa_btn')[0];
const viewErrorText = $('#view_error')[0];
const viewDiv = $('#view_iqa_div')[0];
viewiqabtn.addEventListener('click', ()=>{
    viewErrorText.style.display = 'none';
    if(viewDiv.style.display == 'block'){
        viewDiv.style.display = 'none';
    } else if(viewDiv.style.display == 'none'){
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/admin_view_iqa_render.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    viewErrorText.innerText = text['error'];
                    viewErrorText.style.display = 'block';
                } else if(text['return']){
                    viewDiv.innerHTML = text['return'];
                } else {
                    viewErrorText.innerText = 'No data available';
                    viewErrorText.style.display = 'block';
                }
            } else {
                viewErrorText.innerText = 'Loading error';
                viewErrorText.style.display = 'block';
            }
        }
        xhr.send();
        viewDiv.style.display = 'block';
    }
});