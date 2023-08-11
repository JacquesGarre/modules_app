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
            $('#modal').find('.modal-dialog').html(response.data);
            let modal = Modal.getOrCreateInstance('#modal');
            modal.show()
        });
    }

    close() {
        const modal = Modal.getOrCreateInstance('#modal');
        modal.hide()
    }

}
