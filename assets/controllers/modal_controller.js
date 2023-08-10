import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';
import jQuery from "jquery";
window.$ = window.jQuery = jQuery;
import axios from 'axios';

export default class extends Controller {

    static values = {
        url: String
    }

    open() {
        axios
        .get(this.urlValue)
        .then((response) => {
            const modalElement = $('#modal')
            modalElement.find('.modal-dialog').html(response.data);
            const modal = new Modal(modalElement)
            modal.show()
        });
    }

}
