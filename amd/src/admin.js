//Variable and function used to load assign iqa section
const iqabtn = $('#assign_iqa_btn')[0];
const alspan = $('#choose_al_div')[0];
const iqaspan = $('#choose_aiqa_div')[0];
const success = $('#assign_success')[0];
iqabtn.addEventListener('click', ()=>{
    const div = $('#assign_iqa_div')[0];
    if(div.style.display == 'block'){
        div.style.display = 'none';
    } else if(div.style.display == 'none'){
        iqaspan.style.display = 'none';
        alspan.style.display = 'none';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/admin_assign_iqa_render.inc.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
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
        }
        xhr.send();
        div.style.display = 'block';
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
    const errorText = $('#assign_error')[0];
    const learner = $(`.iqa-learner`);
    const iqa = $('.iqa-iqa');
    let required = [false, false];
    errorText.style.display = 'none';
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
            errorText.innerText = 'No learner selected';
        } else if(required[1] == false){
            errorText.innerText = 'No iqa selected';
        }
        errorText.style.display = 'block';
    } else {
        //Used to submit form data
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/admin_assign_iqa.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    errorText.innerText = text['error'];
                    errorText.style.display = 'block';
                } else if(text['return']){
                    $('#assign_iqa_div')[0].style.display = 'none';
                    iqabtn.click();
                    success.innerText = 'Success';
                    success.style.display = 'block';
                } else {
                    errorText.innerText = 'Submit error';
                    errorText.style.display = 'block';
                }
            } else {
                errorText.style.display = 'block';
                errorText.innerText = 'Connection error';
            }
        }
        xhr.send(params);
    }
});