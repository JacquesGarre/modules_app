import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

export default class extends Controller {

    static targets = [ "submitBtn", "errors" ]

    static values = {
        url: String,
        submitLabel: String
    }

    submit() {
        const formData = new FormData(this.element);
        const that = this;
        that.submitBtnTarget.innerHTML = "Please wait...";
        axios({
            method: "post",
            url: this.urlValue,
            data: formData,
            headers: { "Content-Type": "multipart/form-data" },
        })
        .then(function (response) {
            if(response.data.error !== undefined){
                that.errorsTarget.innerHTML = response.data.error
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
