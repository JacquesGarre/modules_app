import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

export default class extends Controller {

    static targets = [ "form", "submitBtn" ]

    static values = {
        name: String,
        url: String,
        submitLabel: String
    }

    submit() {
        const that = this;
        const formData = new FormData(that.formTarget);

        $(that.formTarget).find('.invalid-feedback').remove()
        $(that.formTarget).find('.field-invalid').removeClass('field-invalid')

        that.submitBtnTarget.innerHTML = "Please wait...";
        axios({
            method: "post",
            url: this.urlValue,
            data: formData,
            headers: { "Content-Type": "multipart/form-data" },
        })
        .then(function (response) {
            if(response.data.errors !== undefined){
                for(const fieldID in response.data.errors){
                    const fieldErrors = response.data.errors[fieldID].join(', ')
                    $(that.formTarget)
                    .find('#'+that.nameValue + '_' + fieldID)
                    .addClass('field-invalid')
                    .after(`<div class="invalid-feedback d-block">`+fieldErrors+`</div>`)
                }
                that.submitBtnTarget.innerHTML = that.submitLabelValue
            } else {
                that.submitBtnTarget.innerHTML = 'Saved <i class="fas fa-check"></i>'
            }
        })
        .catch(function (response) {
            that.errorsTarget.innerHTML = "An error happened..."
            that.submitBtnTarget.innerHTML = that.submitLabelValue
        });
    }

}
