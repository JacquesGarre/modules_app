import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

export default class extends Controller {

    static targets = [ "form", "submitBtn" ]

    static values = {
        name: String,
        url: String,
        submitLabel: String,
        method: String,
        table: String,
        keepMode: Boolean,
        action: String
    }


    submit() {
        const that = this;
        const formData = new FormData(that.formTarget);
        $(that.formTarget).find('.invalid-feedback').remove()
        $(that.formTarget).find('.field-invalid').removeClass('field-invalid')

        let url = $(that.formTarget).attr('action') !== undefined ? $(that.formTarget).attr('action') : this.urlValue;

        that.submitBtnTarget.innerHTML = "Submitting...";
        axios({
            method: this.methodValue,
            url: url,
            data: formData,
            headers: { "Content-Type": "multipart/form-data" },
        })
        .then(function (response) {
            if(response.data.errors !== undefined){

                // Erros on form
                if(that.nameValue in response.data.errors){
                    const formErrors = response.data.errors[that.nameValue].join(', ')
                    $(that.formTarget).append(`<div class="invalid-feedback d-block">`+formErrors+`</div>`)
                }

                // Errors on fields
                for(const fieldID in response.data.errors){
                    const fieldErrors = response.data.errors[fieldID].join(', ')
                    $(that.formTarget)
                    .find('#'+that.nameValue + '_' + fieldID)
                    .addClass('field-invalid')
                    .after(`<div class="invalid-feedback d-block">`+fieldErrors+`</div>`)
                }

                that.submitBtnTarget.innerHTML = that.submitLabelValue
            } else {
                that.submitBtnTarget.innerHTML = 'Submitted <i class="fas fa-check"></i>'
                that.dispatch("success", {detail: { content: that.tableValue }})

                if(!that.keepModeValue && that.actionValue != 'add'){
                    that.disable()
                } else {
                    that.enable()
                }
            }
        })
        .catch(function (response) {
            console.log(response)
            that.submitBtnTarget.innerHTML = that.submitLabelValue
        });
    }

    onchange(){

        const formData = new FormData(this.formTarget);

        console.log(formData)

        let that = this;
        axios({
            method: 'POST',
            url: this.urlValue+'?onchange=1',
            data: formData,
            headers: { "Content-Type": "multipart/form-data" },
        })
        .then(function (response) {
            $(that.formTarget).html(response.data)
        }).catch(err => { console.log("erreeer",err.response) });

    }

    enable(){
        const formData = new FormData(this.formTarget);
        let that = this;
        axios({
            method: 'POST',
            url: this.urlValue+'?enable=1',
            data: formData,
            headers: { "Content-Type": "multipart/form-data" },
        })
        .then(function (response) {
            $(that.formTarget).html(response.data)
        })
    }

    disable(){
        const formData = new FormData(this.formTarget);
        let that = this;
        axios({
            method: 'POST',
            url: this.urlValue+'?disable=1',
            data: formData,
            headers: { "Content-Type": "multipart/form-data" },
        })
        .then(function (response) {
            $(that.formTarget).html(response.data)
        })
    }

}
