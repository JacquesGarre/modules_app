import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

export default class extends Controller {

    static targets = [ "form", "submitBtn" ]

    static values = {
        name: String,
        url: String,
        submitLabel: String,
        method: String,
        table: String
    }


    submit() {
        const that = this;
        const formData = new FormData(that.formTarget);

        $(that.formTarget).find('.invalid-feedback').remove()
        $(that.formTarget).find('.field-invalid').removeClass('field-invalid')

        that.submitBtnTarget.innerHTML = "Submitting...";
        axios({
            method: this.methodValue,
            url: this.urlValue,
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
            }
        })
        .catch(function (response) {
            console.log(response)
            that.submitBtnTarget.innerHTML = that.submitLabelValue
        });
    }

}
